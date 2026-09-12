<?php
// app/Models/Income.php

class Income {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getIncomes($limit = 100) {
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT * FROM incomes WHERE school_id = :school_id ORDER BY date DESC, id DESC LIMIT :limit");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':limit', (int)$limit, PDO::PARAM_INT);
        return $this->db->resultSet();
    }

    public function getIncomeById($id) {
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("SELECT * FROM incomes WHERE id = :id AND school_id = :school_id LIMIT 1");
        $this->db->bind(':id', (int)$id);
        $this->db->bind(':school_id', $schoolId);
        return $this->db->single();
    }

    public function addIncome($data) {
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("INSERT INTO incomes (school_id, title, category, amount, date, payment_mode, reference_no, fee_payment_id, note) 
                          VALUES (:school_id, :title, :category, :amount, :date, :mode, :ref, :fee_id, :note)");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':title', trim($data['title']));
        $this->db->bind(':category', !empty($data['category']) ? $data['category'] : 'Student Fee');
        $this->db->bind(':amount', (float)$data['amount']);
        $this->db->bind(':date', !empty($data['date']) ? $data['date'] : date('Y-m-d'));
        $this->db->bind(':mode', !empty($data['payment_mode']) ? $data['payment_mode'] : 'Cash');
        $this->db->bind(':ref', !empty($data['reference_no']) ? trim($data['reference_no']) : null);
        $this->db->bind(':fee_id', !empty($data['fee_payment_id']) ? (int)$data['fee_payment_id'] : null);
        $this->db->bind(':note', trim($data['note'] ?? ''));

        return $this->db->execute();
    }

    public function updateIncome($data) {
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("UPDATE incomes 
                          SET title = :title, category = :category, amount = :amount, date = :date, 
                              payment_mode = :mode, reference_no = :ref, note = :note 
                          WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':title', trim($data['title']));
        $this->db->bind(':category', !empty($data['category']) ? $data['category'] : 'Student Fee');
        $this->db->bind(':amount', (float)$data['amount']);
        $this->db->bind(':date', $data['date']);
        $this->db->bind(':mode', !empty($data['payment_mode']) ? $data['payment_mode'] : 'Cash');
        $this->db->bind(':ref', !empty($data['reference_no']) ? trim($data['reference_no']) : null);
        $this->db->bind(':note', trim($data['note'] ?? ''));
        $this->db->bind(':id', (int)$data['id']);
        $this->db->bind(':school_id', $schoolId);

        return $this->db->execute();
    }

    public function deleteIncome($id) {
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $this->db->query("DELETE FROM incomes WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':id', (int)$id);
        $this->db->bind(':school_id', $schoolId);
        return $this->db->execute();
    }

    // Auto-sync entry when a fee is collected
    public static function syncFeePayment($paymentId, $studentName, $amount, $date, $mode, $receiptNo) {
        try {
            $schoolId = TenantContext::getSchoolId() ?: 1;
            $db = new Database();
            $title = "Fee Collection: " . $studentName;
            $db->query("INSERT INTO incomes (school_id, title, category, amount, date, payment_mode, reference_no, fee_payment_id, note) 
                        VALUES (:school_id, :title, 'Student Fee', :amount, :date, :mode, :ref, :fee_id, 'Automated accounting entry from Fee Collection')");
            $db->bind(':school_id', $schoolId);
            $db->bind(':title', $title);
            $db->bind(':amount', (float)$amount);
            $db->bind(':date', $date ?: date('Y-m-d'));
            $db->bind(':mode', $mode ?: 'Cash');
            $db->bind(':ref', $receiptNo ?: ('REC-' . $paymentId));
            $db->bind(':fee_id', (int)$paymentId);
            $db->execute();
        } catch (Exception $e) {
            error_log("Income sync error: " . $e->getMessage());
        }
    }

    // Daily Cash Book Closing Calculation
    public function getCashBookDailyClosing($date = null) {
        $schoolId = TenantContext::getSchoolId() ?: 1;
        $targetDate = $date ?: date('Y-m-d');

        // Total Income today
        $this->db->query("SELECT COALESCE(SUM(amount), 0) as total_income FROM incomes WHERE school_id = :school_id AND date = :date");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':date', $targetDate);
        $incomeRow = $this->db->single();
        $totalIncome = (float)($incomeRow->total_income ?? 0);

        // Total Expenses today
        $this->db->query("SELECT COALESCE(SUM(amount), 0) as total_expense FROM expenses WHERE (school_id = :school_id OR school_id IS NULL) AND date = :date");
        $this->db->bind(':school_id', $schoolId);
        $this->db->bind(':date', $targetDate);
        $expenseRow = $this->db->single();
        $totalExpense = (float)($expenseRow->total_expense ?? 0);

        return [
            'date' => $targetDate,
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'net_closing' => $totalIncome - $totalExpense
        ];
    }
}
