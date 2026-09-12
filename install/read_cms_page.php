<?php
require_once 'app/start.php';
$db = new Database;
// Fetch the page content for 'online-admisson' (or check all pages to find it)
$db->query("SELECT * FROM front_cms_pages WHERE slug LIKE '%admisson%' OR title LIKE '%Admission%'");
$pages = $db->resultSet();
foreach($pages as $page){
    echo "Slug: " . $page->slug . "\n";
    echo "Title: " . $page->title . "\n";
    echo "Content Preview: " . substr(strip_tags($page->content), 0, 500) . "\n";
    echo "Full Content (HTML): \n" . $page->content . "\n";
    echo "--------------------------------------------------\n";
}
