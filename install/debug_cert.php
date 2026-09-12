<?php
require_once 'app/config/config.php';
require_once 'app/Core/Database.php';

$db = new Database();
$db->query("SELECT id, certificate_name, background_image FROM certificates ORDER BY id DESC LIMIT 5");
$results = $db->resultSet();

echo "<h1>Certificate Debug</h1>";
echo "<table border='1'><tr><th>ID</th><th>Name</th><th>Image Path</th></tr>";
foreach($results as $row){
    echo "<tr>";
    echo "<td>" . $row->id . "</td>";
    echo "<td>" . $row->certificate_name . "</td>";
    echo "<td>" . $row->background_image . "</td>";
    echo "</tr>";
}
echo "</table>";
