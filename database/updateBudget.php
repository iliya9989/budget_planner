<?php
require '../database/db.php';

// Get the raw POST data
$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    // Log received data for debugging
    file_put_contents('php://stderr', print_r($data, TRUE));

    // Extract data from the received JSON
    $budgetName = $data['budgetName'];
    $budgetBalance = $data['budgetBalance'];
    $incomeItems = $data['incomeItems'];
    $expensesItems = $data['expensesItems'];
    $budget_id = $data['budget_id'];

    try {
        // Start a transaction
        $db->beginTransaction();

        // Update budget details
        $updateBudgetQuery = $db->prepare('UPDATE budgets SET budget_name = :budget_name, budget_balance = :budget_balance WHERE budget_id = :budget_id');
        $updateBudgetQuery->bindValue(':budget_name', $budgetName, PDO::PARAM_STR);
        $updateBudgetQuery->bindValue(':budget_balance', $budgetBalance, PDO::PARAM_INT);
        $updateBudgetQuery->bindValue(':budget_id', $budget_id, PDO::PARAM_INT);
        $updateBudgetQuery->execute();

        // Fetch existing incomes and expenses related to this budget
        $existingIncomesQuery = $db->prepare('SELECT i.income_id FROM incomes i JOIN budget_incomes bi ON i.income_id = bi.income_id WHERE bi.budget_id = :budget_id');
        $existingIncomesQuery->bindValue(':budget_id', $budget_id, PDO::PARAM_INT);
        $existingIncomesQuery->execute();
        $existingIncomes = $existingIncomesQuery->fetchAll(PDO::FETCH_COLUMN, 0);

        $existingExpensesQuery = $db->prepare('SELECT e.expense_id FROM expenses e JOIN budget_expenses be ON e.expense_id = be.expense_id WHERE be.budget_id = :budget_id');
        $existingExpensesQuery->bindValue(':budget_id', $budget_id, PDO::PARAM_INT);
        $existingExpensesQuery->execute();
        $existingExpenses = $existingExpensesQuery->fetchAll(PDO::FETCH_COLUMN, 0);

        // Verify if categories exist
        $existingCategoriesQuery = $db->prepare('SELECT category_id FROM categories');
        $existingCategoriesQuery->execute();
        $existingCategories = $existingCategoriesQuery->fetchAll(PDO::FETCH_COLUMN, 0);

        // Handle incomes
        $incomeIdsToKeep = [];
        foreach ($incomeItems as $incomeItem) {
            if (!in_array($incomeItem['category_id'], $existingCategories)) {
                throw new PDOException("Category ID {$incomeItem['category_id']} does not exist.");
            }

            if (isset($incomeItem['id']) && in_array($incomeItem['id'], $existingIncomes)) {
                // Update existing income
                $updateIncomeQuery = $db->prepare('UPDATE incomes SET income_name = :income_name, amount = :amount WHERE income_id = :income_id');
                $updateIncomeQuery->bindValue(':income_name', $incomeItem['name'], PDO::PARAM_STR);
                $updateIncomeQuery->bindValue(':amount', $incomeItem['value'], PDO::PARAM_INT);
                $updateIncomeQuery->bindValue(':income_id', $incomeItem['id'], PDO::PARAM_INT);
                $updateIncomeQuery->execute();

                $updateIncomeCategoryQuery = $db->prepare('UPDATE income_categories SET category_id = :category_id WHERE income_id = :income_id');
                $updateIncomeCategoryQuery->bindValue(':category_id', $incomeItem['category_id'], PDO::PARAM_INT);
                $updateIncomeCategoryQuery->bindValue(':income_id', $incomeItem['id'], PDO::PARAM_INT);
                $updateIncomeCategoryQuery->execute();

                $incomeIdsToKeep[] = $incomeItem['id'];
            } else {
                // Insert new income
                $insertIncomeQuery = $db->prepare('INSERT INTO incomes (income_name, amount) VALUES (:income_name, :amount)');
                $insertIncomeQuery->bindValue(':income_name', $incomeItem['name'], PDO::PARAM_STR);
                $insertIncomeQuery->bindValue(':amount', $incomeItem['value'], PDO::PARAM_INT);
                $insertIncomeQuery->execute();

                $income_id = $db->lastInsertId();

                $insertBudgetIncomeQuery = $db->prepare('INSERT INTO budget_incomes (budget_id, income_id) VALUES (:budget_id, :income_id)');
                $insertBudgetIncomeQuery->bindValue(':budget_id', $budget_id, PDO::PARAM_INT);
                $insertBudgetIncomeQuery->bindValue(':income_id', $income_id, PDO::PARAM_INT);
                $insertBudgetIncomeQuery->execute();

                $insertIncomeCategoryQuery = $db->prepare('INSERT INTO income_categories (income_id, category_id) VALUES (:income_id, :category_id)');
                $insertIncomeCategoryQuery->bindValue(':income_id', $income_id, PDO::PARAM_INT);
                $insertIncomeCategoryQuery->bindValue(':category_id', $incomeItem['category_id'], PDO::PARAM_INT);
                $insertIncomeCategoryQuery->execute();

                $incomeIdsToKeep[] = $income_id;
            }
        }

        // Delete incomes that are not in the list
        $incomeIdsToDelete = array_diff($existingIncomes, $incomeIdsToKeep);
        if (!empty($incomeIdsToDelete)) {
            $deleteIncomeCategoriesQuery = $db->prepare('DELETE FROM income_categories WHERE income_id IN (' . implode(',', $incomeIdsToDelete) . ')');
            $deleteIncomeCategoriesQuery->execute();

            $deleteBudgetIncomesQuery = $db->prepare('DELETE FROM budget_incomes WHERE income_id IN (' . implode(',', $incomeIdsToDelete) . ') AND budget_id = :budget_id');
            $deleteBudgetIncomesQuery->bindValue(':budget_id', $budget_id, PDO::PARAM_INT);
            $deleteBudgetIncomesQuery->execute();

            $deleteIncomesQuery = $db->prepare('DELETE FROM incomes WHERE income_id IN (' . implode(',', $incomeIdsToDelete) . ')');
            $deleteIncomesQuery->execute();
        }

        // Handle expenses
        $expenseIdsToKeep = [];
        foreach ($expensesItems as $expenseItem) {
            if (!in_array($expenseItem['category_id'], $existingCategories)) {
                throw new PDOException("Category ID {$expenseItem['category_id']} does not exist.");
            }

            if (isset($expenseItem['id']) && in_array($expenseItem['id'], $existingExpenses)) {
                // Update existing expense
                $updateExpenseQuery = $db->prepare('UPDATE expenses SET expense_name = :expense_name, cost = :cost WHERE expense_id = :expense_id');
                $updateExpenseQuery->bindValue(':expense_name', $expenseItem['name'], PDO::PARAM_STR);
                $updateExpenseQuery->bindValue(':cost', $expenseItem['value'], PDO::PARAM_INT);
                $updateExpenseQuery->bindValue(':expense_id', $expenseItem['id'], PDO::PARAM_INT);
                $updateExpenseQuery->execute();

                $updateExpenseCategoryQuery = $db->prepare('UPDATE expense_categories SET category_id = :category_id WHERE expense_id = :expense_id');
                $updateExpenseCategoryQuery->bindValue(':category_id', $expenseItem['category_id'], PDO::PARAM_INT);
                $updateExpenseCategoryQuery->bindValue(':expense_id', $expenseItem['id'], PDO::PARAM_INT);
                $updateExpenseCategoryQuery->execute();

                $expenseIdsToKeep[] = $expenseItem['id'];
            } else {
                // Insert new expense
                $insertExpenseQuery = $db->prepare('INSERT INTO expenses (expense_name, cost) VALUES (:expense_name, :cost)');
                $insertExpenseQuery->bindValue(':expense_name', $expenseItem['name'], PDO::PARAM_STR);
                $insertExpenseQuery->bindValue(':cost', $expenseItem['value'], PDO::PARAM_INT);
                $insertExpenseQuery->execute();

                $expense_id = $db->lastInsertId();

                $insertBudgetExpenseQuery = $db->prepare('INSERT INTO budget_expenses (budget_id, expense_id) VALUES (:budget_id, :expense_id)');
                $insertBudgetExpenseQuery->bindValue(':budget_id', $budget_id, PDO::PARAM_INT);
                $insertBudgetExpenseQuery->bindValue(':expense_id', $expense_id, PDO::PARAM_INT);
                $insertBudgetExpenseQuery->execute();

                $insertExpenseCategoryQuery = $db->prepare('INSERT INTO expense_categories (expense_id, category_id) VALUES (:expense_id, :category_id)');
                $insertExpenseCategoryQuery->bindValue(':expense_id', $expense_id, PDO::PARAM_INT);
                $insertExpenseCategoryQuery->bindValue(':category_id', $expenseItem['category_id'], PDO::PARAM_INT);
                $insertExpenseCategoryQuery->execute();

                $expenseIdsToKeep[] = $expense_id;
            }
        }

        // Delete expenses that are not in the list
        $expenseIdsToDelete = array_diff($existingExpenses, $expenseIdsToKeep);
        if (!empty($expenseIdsToDelete)) {
            $deleteExpenseCategoriesQuery = $db->prepare('DELETE FROM expense_categories WHERE expense_id IN (' . implode(',', $expenseIdsToDelete) . ')');
            $deleteExpenseCategoriesQuery->execute();

            $deleteBudgetExpensesQuery = $db->prepare('DELETE FROM budget_expenses WHERE expense_id IN (' . implode(',', $expenseIdsToDelete) . ') AND budget_id = :budget_id');
            $deleteBudgetExpensesQuery->bindValue(':budget_id', $budget_id, PDO::PARAM_INT);
            $deleteBudgetExpensesQuery->execute();

            $deleteExpensesQuery = $db->prepare('DELETE FROM expenses WHERE expense_id IN (' . implode(',', $expenseIdsToDelete) . ')');
            $deleteExpensesQuery->execute();
        }

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
?>
