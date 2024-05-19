<?php
try {
    $db = new PDO('mysql:host=localhost;dbname=shei03;charset=utf8', 'root', '100146');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB connection failed: " . $e->getMessage());
}
