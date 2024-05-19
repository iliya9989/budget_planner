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

        // Delete existing incomes and expenses related to this budget
        $deleteIncomesQuery = $db->prepare('DELETE FROM budget_incomes WHERE budget_id = :budget_id');
        $deleteIncomesQuery->bindValue(':budget_id', $budget_id, PDO::PARAM_INT);
        $deleteIncomesQuery->execute();

        $deleteExpensesQuery = $db->prepare('DELETE FROM budget_expenses WHERE budget_id = :budget_id');
        $deleteExpensesQuery->bindValue(':budget_id', $budget_id, PDO::PARAM_INT);
        $deleteExpensesQuery->execute();

        // Insert new incomes and link to budget
        foreach ($incomeItems as $incomeName => $incomeAmount) {
            $insertIncomeQuery = $db->prepare('INSERT INTO incomes (income_name, amount) VALUES (:income_name, :amount)');
            $insertIncomeQuery->bindValue(':income_name', $incomeName, PDO::PARAM_STR);
            $insertIncomeQuery->bindValue(':amount', $incomeAmount, PDO::PARAM_INT);
            $insertIncomeQuery->execute();

            $income_id = $db->lastInsertId();

            $insertBudgetIncomeQuery = $db->prepare('INSERT INTO budget_incomes (budget_id, income_id) VALUES (:budget_id, :income_id)');
            $insertBudgetIncomeQuery->bindValue(':budget_id', $budget_id, PDO::PARAM_INT);
            $insertBudgetIncomeQuery->bindValue(':income_id', $income_id, PDO::PARAM_INT);
            $insertBudgetIncomeQuery->execute();
        }

        // Insert new expenses and link to budget
        foreach ($expensesItems as $expenseName => $expenseCost) {
            $insertExpenseQuery = $db->prepare('INSERT INTO expenses (expense_name, cost) VALUES (:expense_name, :cost)');
            $insertExpenseQuery->bindValue(':expense_name', $expenseName, PDO::PARAM_STR);
            $insertExpenseQuery->bindValue(':cost', $expenseCost, PDO::PARAM_INT);
            $insertExpenseQuery->execute();

            $expense_id = $db->lastInsertId();

            $insertBudgetExpenseQuery = $db->prepare('INSERT INTO budget_expenses (budget_id, expense_id) VALUES (:budget_id, :expense_id)');
            $insertBudgetExpenseQuery->bindValue(':budget_id', $budget_id, PDO::PARAM_INT);
            $insertBudgetExpenseQuery->bindValue(':expense_id', $expense_id, PDO::PARAM_INT);
            $insertBudgetExpenseQuery->execute();
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
