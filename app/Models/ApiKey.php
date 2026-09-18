<?php
// app/Models/ApiKey.php

class ApiKey {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    /**
     * Get all API keys for a school
     */
    public function getAllBySchool($schoolId) {
        $this->db->query("SELECT * FROM api_keys WHERE school_id = :school_id ORDER BY id DESC");
        $this->db->bind(':school_id', (int)$schoolId);
        return $this->db->resultSet();
    }

    /**
     * Get specific API key by ID and school
     */
    public function getById($id, $schoolId) {
        $this->db->query("SELECT * FROM api_keys WHERE id = :id AND school_id = :school_id LIMIT 1");
        $this->db->bind(':id', (int)$id);
        $this->db->bind(':school_id', (int)$schoolId);
        return $this->db->single();
    }

    /**
     * Find an API key by its raw key string
     */
    public function findByKey($apiKey) {
        $this->db->query("SELECT * FROM api_keys WHERE api_key = :api_key LIMIT 1");
        $this->db->bind(':api_key', trim($apiKey));
        return $this->db->single();
    }

    /**
     * Generate and store a new API key
     */
    public function generateKey($schoolId, $clientName, $rateLimit = 120) {
        $key = 'sk_live_' . bin2hex(random_bytes(24));
        $clientName = trim($clientName);
        if (empty($clientName)) {
            $clientName = 'Mobile App Client';
        }
        $rateLimit = max(10, (int)$rateLimit);

        $this->db->query("INSERT INTO api_keys (school_id, client_name, api_key, is_active, rate_limit_per_min, created_at)
                          VALUES (:school_id, :client_name, :api_key, 1, :rate_limit, NOW())");
        $this->db->bind(':school_id', (int)$schoolId);
        $this->db->bind(':client_name', $clientName);
        $this->db->bind(':api_key', $key);
        $this->db->bind(':rate_limit', $rateLimit);

        if ($this->db->execute()) {
            return [
                'id' => $this->db->lastInsertId(),
                'api_key' => $key,
                'client_name' => $clientName
            ];
        }
        return false;
    }

    /**
     * Toggle active/inactive status of an API key
     */
    public function toggleStatus($id, $schoolId) {
        $key = $this->getById($id, $schoolId);
        if (!$key) {
            return false;
        }

        $newStatus = ((int)$key->is_active === 1) ? 0 : 1;
        $this->db->query("UPDATE api_keys SET is_active = :status WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':status', $newStatus);
        $this->db->bind(':id', (int)$id);
        $this->db->bind(':school_id', (int)$schoolId);
        return $this->db->execute();
    }

    /**
     * Delete an API key and cascade delete associated user tokens
     */
    public function deleteKey($id, $schoolId) {
        $this->db->query("DELETE FROM api_user_tokens WHERE api_key_id = :api_key_id AND school_id = :school_id");
        $this->db->bind(':api_key_id', (int)$id);
        $this->db->bind(':school_id', (int)$schoolId);
        $this->db->execute();

        $this->db->query("DELETE FROM api_keys WHERE id = :id AND school_id = :school_id");
        $this->db->bind(':id', (int)$id);
        $this->db->bind(':school_id', (int)$schoolId);
        return $this->db->execute();
    }

    /**
     * Create mobile user session Bearer token
     */
    public function createUserToken($userId, $schoolId, $apiKeyId = null, $deviceName = 'Mobile App', $devicePlatform = 'android', $daysValid = 30) {
        $token = 'usr_' . bin2hex(random_bytes(36));
        $expiresAt = date('Y-m-d H:i:s', strtotime("+{$daysValid} days"));

        $this->db->query("INSERT INTO api_user_tokens (school_id, user_id, api_key_id, token, device_name, device_platform, expires_at, created_at)
                          VALUES (:school_id, :user_id, :api_key_id, :token, :device_name, :device_platform, :expires_at, NOW())");
        $this->db->bind(':school_id', (int)$schoolId);
        $this->db->bind(':user_id', (int)$userId);
        $this->db->bind(':api_key_id', !empty($apiKeyId) ? (int)$apiKeyId : null);
        $this->db->bind(':token', $token);
        $this->db->bind(':device_name', trim($deviceName));
        $this->db->bind(':device_platform', strtolower(trim($devicePlatform)));
        $this->db->bind(':expires_at', $expiresAt);

        if ($this->db->execute()) {
            return [
                'token' => $token,
                'expires_at' => $expiresAt
            ];
        }
        return false;
    }

    /**
     * Revoke / expire a specific Bearer token
     */
    public function revokeUserToken($token) {
        $this->db->query("DELETE FROM api_user_tokens WHERE token = :token");
        $this->db->bind(':token', trim($token));
        return $this->db->execute();
    }
}
