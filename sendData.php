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
    $data = json_decode($rawData, true);

    if ($data) {
        try {
            // Start transaction
            $db->beginTransaction();

            // Create a new budget entry
            $stmt = $db->prepare("INSERT INTO budgets (user_id, budget_name) VALUES (?, ?)");
            $stmt->execute([$user_id, 'New Budget']);
            $budget_id = $db->lastInsertId();

            // Prepare statements for incomes and expenses
            $stmtIncome = $db->prepare("INSERT INTO incomes (income_name, amount) VALUES (?, ?)");
            $stmtExpense = $db->prepare("INSERT INTO expenses (expense_name, cost) VALUES (?, ?)");

            // Insert income items and link them to the budget
            foreach ($data['incomeItems'] as $name => $amount) {
                $stmtIncome->execute([$name, $amount]);
                $income_id = $db->lastInsertId();
                $db->exec("INSERT INTO budget_incomes (budget_id, income_id) VALUES ($budget_id, $income_id)");
            }

            // Insert expense items and link them to the budget
            foreach ($data['expensesItems'] as $name => $cost) {
                $stmtExpense->execute([$name, $cost]);
                $expense_id = $db->lastInsertId();
                $db->exec("INSERT INTO budget_expenses (budget_id, expense_id) VALUES ($budget_id, $expense_id)");
            }

            // Commit transaction
            $db->commit();

            echo json_encode(['status' => 'success', 'message' => 'Budget updated successfully']);
        } catch (PDOException $e) {
            // Rollback on failure
            $db->rollback();
            echo json_encode(['status' => 'error', 'message' => 'Database error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid JSON']);
    }
    exit;
}
?>
