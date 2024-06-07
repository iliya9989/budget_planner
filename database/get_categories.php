<?php
session_start();
require 'db.php';

$user_id = $_SESSION['user_id'];
$selectCategoriesQuery = $db->prepare('SELECT * FROM categories WHERE user_id = :user_id');
$selectCategoriesQuery->bindParam(':user_id', $user_id);
$selectCategoriesQuery->execute();
$categories = $selectCategoriesQuery->fetchAll();

echo json_encode($categories);