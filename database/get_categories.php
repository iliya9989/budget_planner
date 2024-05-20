<?php
require 'db.php';

$selectCategoriesQuery = $db->prepare('SELECT * FROM categories');
$selectCategoriesQuery->execute();
$categories = $selectCategoriesQuery->fetchAll();

echo json_encode($categories);