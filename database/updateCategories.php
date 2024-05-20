<?php
require '../database/db.php';

// Enable error logging
ini_set('log_errors', 1);
ini_set('error_log', '../logs/error_log.txt');

// Log a custom message
error_log("updateCategories.php script started.");

// Get the raw POST data
$data = json_decode(file_get_contents('php://input'), true);

// Log the received data
error_log("Received data: " . print_r($data, true));

if ($data) {
    try {
        // Start a transaction
        $db->beginTransaction();
        error_log("Transaction started.");

        // Handle new categories
        if (isset($data['newCategories']) && !empty($data['newCategories'])) {
            $insertCategoryQuery = $db->prepare('INSERT INTO categories (category_name) VALUES (:category_name)');
            foreach ($data['newCategories'] as $newCategory) {
                error_log("Inserting new category: " . $newCategory['name']);
                $insertCategoryQuery->bindValue(':category_name', $newCategory['name'], PDO::PARAM_STR);
                if (!$insertCategoryQuery->execute()) {
                    error_log("Error inserting new category: " . json_encode($insertCategoryQuery->errorInfo()));
                }
            }
        }

        // Handle category deletions
        if (isset($data['categoriesToDelete']) && !empty($data['categoriesToDelete'])) {
            $deleteIncomeCategoryQuery = $db->prepare('DELETE FROM income_categories WHERE category_id = :category_id');
            $deleteExpenseCategoryQuery = $db->prepare('DELETE FROM expense_categories WHERE category_id = :category_id');
            $deleteCategoryQuery = $db->prepare('DELETE FROM categories WHERE category_id = :category_id');

            foreach ($data['categoriesToDelete'] as $categoryId) {  // Change here
                error_log("Deleting category: " . $categoryId);

                $deleteIncomeCategoryQuery->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
                if (!$deleteIncomeCategoryQuery->execute()) {
                    error_log("Error deleting from income_categories: " . json_encode($deleteIncomeCategoryQuery->errorInfo()));
                }

                $deleteExpenseCategoryQuery->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
                if (!$deleteExpenseCategoryQuery->execute()) {
                    error_log("Error deleting from expense_categories: " . json_encode($deleteExpenseCategoryQuery->errorInfo()));
                }

                $deleteCategoryQuery->bindValue(':category_id', $categoryId, PDO::PARAM_INT);
                if (!$deleteCategoryQuery->execute()) {
                    error_log("Error deleting from categories: " . json_encode($deleteCategoryQuery->errorInfo()));
                }
            }
        }

        // Handle existing category updates
        if (isset($data['categoriesToSave']) && !empty($data['categoriesToSave'])) {
            $updateCategoryQuery = $db->prepare('UPDATE categories SET category_name = :category_name WHERE category_id = :category_id');
            foreach ($data['categoriesToSave'] as $categoryToSave) {
                error_log("Updating category: " . $categoryToSave['id'] . " with name: " . $categoryToSave['name']);
                $updateCategoryQuery->bindValue(':category_name', $categoryToSave['name'], PDO::PARAM_STR);
                $updateCategoryQuery->bindValue(':category_id', $categoryToSave['id'], PDO::PARAM_INT);
                if (!$updateCategoryQuery->execute()) {
                    error_log("Error updating category: " . json_encode($updateCategoryQuery->errorInfo()));
                }
            }
        }

        // Commit the transaction
        $db->commit();
        error_log("Transaction committed.");
        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        // Rollback the transaction on error
        $db->rollBack();
        error_log("Transaction rolled back. Error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    error_log("Invalid input data.");
    echo json_encode(['status' => 'error', 'message' => 'Invalid input data']);
}
?>
