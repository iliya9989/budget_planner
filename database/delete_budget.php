<?php
require '../database/db.php';

// Get the raw POST data
$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    $budget_id = $data['budget_id'];

    try {
        // Start a transaction
        $db->beginTransaction();

        // Delete budget entries from related tables
        $deleteBudgetIncomesQuery = $db->prepare('DELETE FROM budget_incomes WHERE budget_id = :budget_id');
        $deleteBudgetIncomesQuery->bindValue(':budget_id', $budget_id, PDO::PARAM_INT);
        $deleteBudgetIncomesQuery->execute();

        $deleteBudgetExpensesQuery = $db->prepare('DELETE FROM budget_expenses WHERE budget_id = :budget_id');
        $deleteBudgetExpensesQuery->bindValue(':budget_id', $budget_id, PDO::PARAM_INT);
        $deleteBudgetExpensesQuery->execute();

        // Delete the budget itself
        $deleteBudgetQuery = $db->prepare('DELETE FROM budgets WHERE budget_id = :budget_id');
        $deleteBudgetQuery->bindValue(':budget_id', $budget_id, PDO::PARAM_INT);
        $deleteBudgetQuery->execute();

        // Commit the transaction
        $db->commit();
        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        // Rollback the transaction on error
        $db->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input data']);
}