<?php
require_once 'app/start.php';
$db = new Database;
// Add columns if they don't exist
$alterQueries = [
    "ALTER TABLE admission_enquiry ADD COLUMN father_name VARCHAR(100) DEFAULT NULL",
    "ALTER TABLE admission_enquiry ADD COLUMN mother_name VARCHAR(100) DEFAULT NULL",
    "ALTER TABLE admission_enquiry ADD COLUMN dob DATE DEFAULT NULL",
    "ALTER TABLE admission_enquiry ADD COLUMN gender VARCHAR(20) DEFAULT NULL",
    "ALTER TABLE admission_enquiry ADD COLUMN guardian_name VARCHAR(100) DEFAULT NULL",
    "ALTER TABLE admission_enquiry ADD COLUMN guardian_relation VARCHAR(50) DEFAULT NULL",
    "ALTER TABLE admission_enquiry ADD COLUMN previous_school VARCHAR(255) DEFAULT NULL"
];

foreach ($alterQueries as $query) {
    echo "Executing: $query <br>";
    if($db->query($query)){
        try {
            $db->execute();
            echo "Success<br>";
        } catch(PDOException $e){
             echo "Error or Column exists: " . $e->getMessage() . "<br>";
        }
    }
}
echo "Admission Enquiry Table Updated.";
