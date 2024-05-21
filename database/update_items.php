<?php
require 'db.php';

$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    try {
        // Begin transaction
        $db->beginTransaction();

        // Prepare update queries for incomes and expenses
        $updateIncomeQuery = $db->prepare('UPDATE incomes SET income_name = :income_name, amount = :amount WHERE income_id = :income_id');
        $updateExpenseQuery = $db->prepare('UPDATE expenses SET expense_name = :expense_name, cost = :cost WHERE expense_id = :expense_id');

        // Prepare update queries for income and expense categories
        $updateIncomeCategoryQuery = $db->prepare('UPDATE income_categories SET category_id = :category_id WHERE income_id = :income_id');
        $updateExpenseCategoryQuery = $db->prepare('UPDATE expense_categories SET category_id = :category_id WHERE expense_id = :expense_id');

        // Update incomes
        foreach ($data['incomesNames'] as $income) {
            $updateIncomeQuery->bindParam(':income_name', $income['incomeName'], PDO::PARAM_STR);
            $updateIncomeQuery->bindParam(':amount', $income['incomeAmount'], PDO::PARAM_INT);
            $updateIncomeQuery->bindParam(':income_id', $income['incomeId'], PDO::PARAM_INT);
            $updateIncomeQuery->execute();
        }

        // Update expenses
        foreach ($data['expensesNames'] as $expense) {
            $updateExpenseQuery->bindParam(':expense_name', $expense['expenseName'], PDO::PARAM_STR);
            $updateExpenseQuery->bindParam(':cost', $expense['expenseCost'], PDO::PARAM_INT);
            $updateExpenseQuery->bindParam(':expense_id', $expense['expenseId'], PDO::PARAM_INT);
            $updateExpenseQuery->execute();
        }

        // Update income categories
        foreach ($data['incomesCategories'] as $incomeCategory) {
            $updateIncomeCategoryQuery->bindParam(':category_id', $incomeCategory['incomeCategoryId'], PDO::PARAM_INT);
            $updateIncomeCategoryQuery->bindParam(':income_id', $incomeCategory['incomeId'], PDO::PARAM_INT);
            $updateIncomeCategoryQuery->execute();
        }

        // Update expense categories
        foreach ($data['expensesCategories'] as $expenseCategory) {
            $updateExpenseCategoryQuery->bindParam(':category_id', $expenseCategory['expenseCategoryId'], PDO::PARAM_INT);
            $updateExpenseCategoryQuery->bindParam(':expense_id', $expenseCategory['expenseId'], PDO::PARAM_INT);
            $updateExpenseCategoryQuery->execute();
        }

        // Recalculate budget balances
        $recalculateBudgetQuery = $db->prepare('
            UPDATE budgets
            SET budget_balance = (
                SELECT COALESCE(SUM(i.amount), 0) - COALESCE(SUM(e.cost), 0)
                FROM budget_incomes bi
                LEFT JOIN incomes i ON bi.income_id = i.income_id
                LEFT JOIN budget_expenses be ON budgets.budget_id = be.budget_id
                LEFT JOIN expenses e ON be.expense_id = e.expense_id
                WHERE budgets.budget_id = bi.budget_id
            )
        ');
        $recalculateBudgetQuery->execute();

        $db->commit();
        echo json_encode(['status' => 'success', 'message' => 'Items updated successfully']);
    } catch (PDOException $e) {
        $db->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input data']);
}
