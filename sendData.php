<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

// Check if the user is logged in and retrieve the user_id
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
if (!$user_id) {
    echo json_encode(['status' => 'error', 'message' => 'User not logged in']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rawData = file_get_contents("php://input");
    if ($rawData === false) {
        echo json_encode(['status' => 'error', 'message' => 'Failed to read input data']);
        exit;
    }

    $data = json_decode($rawData, true);
    if ($data === null) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid JSON']);
        exit;
    }

    if (!empty($data['budgetName'])) {
        $budgetName = $data['budgetName'];
    } else {
        $budgetName = 'New Budget';
    }

    if (!isset($data['incomeItems']) || !is_array($data['incomeItems'])) {
        $data['incomeItems'] = [];
    }

    if (!isset($data['expensesItems']) || !is_array($data['expensesItems'])) {
        $data['expensesItems'] = [];
    }

    $budgetBalance = isset($data['budgetBalance']) ? $data['budgetBalance'] : 0;

    try {
        // Start transaction
        $db->beginTransaction();

        // Create a new budget entry
        $stmt = $db->prepare("INSERT INTO budgets (user_id, budget_name, budget_balance) VALUES (?, ?, ?)");
        $stmt->execute([$user_id, $budgetName, $budgetBalance]);
        $budget_id = $db->lastInsertId();

        // Prepare statements for incomes and expenses
        $stmtIncome = $db->prepare("INSERT INTO incomes (income_name, amount) VALUES (?, ?)");
        $stmtExpense = $db->prepare("INSERT INTO expenses (expense_name, cost) VALUES (?, ?)");

        // Prepare statements for linking incomes and expenses to the budget
        $stmtBudgetIncome = $db->prepare("INSERT INTO budget_incomes (budget_id, income_id) VALUES (?, ?)");
        $stmtBudgetExpense = $db->prepare("INSERT INTO budget_expenses (budget_id, expense_id) VALUES (?, ?)");

        // Insert income items and link them to the budget
        foreach ($data['incomeItems'] as $name => $amount) {
            $stmtIncome->execute([$name, $amount]);
            $income_id = $db->lastInsertId();
            $stmtBudgetIncome->execute([$budget_id, $income_id]);
        }

        // Insert expense items and link them to the budget
        foreach ($data['expensesItems'] as $name => $cost) {
            $stmtExpense->execute([$name, $cost]);
            $expense_id = $db->lastInsertId();
            $stmtBudgetExpense->execute([$budget_id, $expense_id]);
        }

        // Commit transaction
        $db->commit();

        echo json_encode(['status' => 'success', 'message' => 'Budget updated successfully']);
    } catch (PDOException $e) {
        // Rollback on failure
        $db->rollBack();
        echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
    }
    exit;
}