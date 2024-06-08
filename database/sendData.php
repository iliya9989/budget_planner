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
        $stmtIncome = $db->prepare("INSERT INTO incomes (user_id, income_name, amount) VALUES (?, ?, ?)");
        $stmtExpense = $db->prepare("INSERT INTO expenses (user_id, expense_name, cost) VALUES (?, ?, ?)");

        // Prepare statements for linking incomes and expenses to the budget
        $stmtBudgetIncome = $db->prepare("INSERT INTO budget_incomes (user_id, budget_id, income_id) VALUES (?, ?, ?)");
        $stmtBudgetExpense = $db->prepare("INSERT INTO budget_expenses (user_id, budget_id, expense_id) VALUES (?, ?, ?)");

        // Prepare statements for linking categories to incomes and expenses
        $stmtIncomeCategory = $db->prepare("INSERT INTO income_categories (user_id, income_id, category_id) VALUES (?, ?, ?)");
        $stmtExpenseCategory = $db->prepare("INSERT INTO expense_categories (user_id, expense_id, category_id) VALUES (?, ?, ?)");

        // Insert income items, link them to the budget and their categories
        foreach ($data['incomeItems'] as $name => $details) {

            $stmtIncome->execute([$user_id, $name, $details['value']]);
            if ($details['value'] < 0) {
                echo json_encode(['status' => 'error', 'message' => 'Negative number in incomes']);
                exit;
            }
            $income_id = $db->lastInsertId();
            $stmtBudgetIncome->execute([$user_id, $budget_id, $income_id]);
            if ($details['category']) {
                $stmtIncomeCategory->execute([$user_id, $income_id, $details['category']]);
            }
        }

        // Insert expense items, link them to the budget and their categories
        foreach ($data['expensesItems'] as $name => $details) {
            if ($details['value'] < 0) {
                echo json_encode(['status' => 'error', 'message' => 'Negative number in expenses']);
                exit;
            }
            $stmtExpense->execute([$user_id, $name, $details['value']]);
            $expense_id = $db->lastInsertId();
            $stmtBudgetExpense->execute([$user_id, $budget_id, $expense_id]);
            if ($details['category']) {
                $stmtExpenseCategory->execute([$user_id, $expense_id, $details['category']]);
            }
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
