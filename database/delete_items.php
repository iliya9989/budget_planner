<?php
require 'db.php';

$data = json_decode(file_get_contents('php://input'), true);



if ($data) {
    try {
        //begin transaction
        $db->beginTransaction();
        //query for deleting incomes
        $deleteIncomeCategoryQuery = $db->prepare('DELETE FROM income_categories WHERE income_id = :income_id');
    } catch (PDOException $e) {
        $db->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input data']);
}