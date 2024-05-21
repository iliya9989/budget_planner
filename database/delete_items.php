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

        // Track affected budgets
        $affectedBudgets = [];

        // Cycle for deleting income-related items
        foreach ($data['deleteIncomesIdsArray'] as $incomeId) {
            // Get budgets affected by this income
            $selectBudgetIdsQuery = $db->prepare('SELECT budget_id FROM budget_incomes WHERE income_id = :income_id');
            $selectBudgetIdsQuery->bindParam(':income_id', $incomeId, PDO::PARAM_INT);
            $selectBudgetIdsQuery->execute();
            $budgetIds = $selectBudgetIdsQuery->fetchAll(PDO::FETCH_COLUMN);

            // Track these budgets
            $affectedBudgets = array_merge($affectedBudgets, $budgetIds);

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
            // Get budgets affected by this expense
            $selectBudgetIdsQuery = $db->prepare('SELECT budget_id FROM budget_expenses WHERE expense_id = :expense_id');
            $selectBudgetIdsQuery->bindParam(':expense_id', $expenseId, PDO::PARAM_INT);
            $selectBudgetIdsQuery->execute();
            $budgetIds = $selectBudgetIdsQuery->fetchAll(PDO::FETCH_COLUMN);

            // Track these budgets
            $affectedBudgets = array_merge($affectedBudgets, $budgetIds);

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

        // Remove duplicate budget IDs
        $affectedBudgets = array_unique($affectedBudgets);

        // Recalculate budget balances for affected budgets
        $recalculateBudgetQuery = $db->prepare('
            UPDATE budgets
            SET budget_balance = (
                SELECT COALESCE(SUM(i.amount), 0) - COALESCE(SUM(e.cost), 0)
                FROM budget_incomes bi
                LEFT JOIN incomes i ON bi.income_id = i.income_id
                LEFT JOIN budget_expenses be ON budgets.budget_id = be.budget_id
                LEFT JOIN expenses e ON be.expense_id = e.expense_id
                WHERE budgets.budget_id = :budget_id
            )
            WHERE budget_id = :budget_id
        ');

        foreach ($affectedBudgets as $budgetId) {
            $recalculateBudgetQuery->bindParam(':budget_id', $budgetId, PDO::PARAM_INT);
            $recalculateBudgetQuery->execute();
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
