<?php
require_once 'app/start.php';
$db = new Database;
$db->query("SELECT * FROM front_cms_menus");
$menus = $db->resultSet();
foreach($menus as $menu){
    echo "ID: " . $menu->id . " | Title: " . $menu->title . " | Link: " . $menu->link . " | PageID: " . $menu->page_id . "<br>";
}
