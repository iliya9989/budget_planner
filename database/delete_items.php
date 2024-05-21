<?php
require 'db.php';

$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    try {
        // Begin transaction
        $db->beginTransaction();

        // Prepare delete queries for budget related tables
        $deleteBudgetIncomeQuery = $db->prepare('DELETE FROM budget_incomes WHERE income_id = :income_id');
        $deleteBudgetExpenseQuery = $db->prepare('DELETE FROM budget_expenses WHERE expense_id = :expense_id');

        // Prepare delete queries for categories and main tables
        $deleteIncomeCategoryQuery = $db->prepare('DELETE FROM income_categories WHERE income_id = :income_id');
        $deleteIncomeQuery = $db->prepare('DELETE FROM incomes WHERE income_id = :income_id');
        $deleteExpenseCategoryQuery = $db->prepare('DELETE FROM expense_categories WHERE expense_id = :expense_id');
        $deleteExpenseQuery = $db->prepare('DELETE FROM expenses WHERE expense_id = :expense_id');

        // Cycle for deleting income-related items
        foreach ($data['deleteIncomesIdsArray'] as $incomeId) {
            // Delete from budget_incomes first to prevent foreign key constraint violation
            $deleteBudgetIncomeQuery->bindParam(':income_id', $incomeId, PDO::PARAM_INT);
            $deleteBudgetIncomeQuery->execute();

            // Delete from income_categories
            $deleteIncomeCategoryQuery->bindParam(':income_id', $incomeId, PDO::PARAM_INT);
            $deleteIncomeCategoryQuery->execute();

            // Delete from incomes
            $deleteIncomeQuery->bindParam(':income_id', $incomeId, PDO::PARAM_INT);
            $deleteIncomeQuery->execute();
        }

        // Cycle for deleting expense-related items
        foreach ($data['deleteExpensesIdsArray'] as $expenseId) {
            // Delete from budget_expenses first to prevent foreign key constraint violation
            $deleteBudgetExpenseQuery->bindParam(':expense_id', $expenseId, PDO::PARAM_INT);
            $deleteBudgetExpenseQuery->execute();

            // Delete from expense_categories
            $deleteExpenseCategoryQuery->bindParam(':expense_id', $expenseId, PDO::PARAM_INT);
            $deleteExpenseCategoryQuery->execute();

            // Delete from expenses
            $deleteExpenseQuery->bindParam(':expense_id', $expenseId, PDO::PARAM_INT);
            $deleteExpenseQuery->execute();
        }

        $db->commit();
        echo json_encode(['status' => 'success', 'message' => 'Items deleted successfully']);
    } catch (PDOException $e) {
        $db->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input data']);
}
