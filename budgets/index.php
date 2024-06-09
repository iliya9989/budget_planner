<?php
session_start();
require '../database/db.php';

$budgetData = null;

if (!empty($_SESSION['username']) && !empty($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $getBudgetDataQuery = $db->prepare('SELECT budget_id, budget_name, budget_balance 
                FROM budgets 
                WHERE user_id = :user_id');
    $getBudgetDataQuery->bindParam(':user_id', $user_id, PDO::PARAM_INT);
    $getBudgetDataQuery->execute();
    $budgetData = $getBudgetDataQuery->fetchAll(PDO::FETCH_ASSOC);

    $budgetIncomesCategoriesData = [];
    $budgetExpensesCategoriesData = [];

    // Fetching data about budget's expenses
    $fetchBudgetsExpensesIds = $db->prepare('SELECT expense_id FROM budget_expenses WHERE budget_id = :budget_id');
    foreach ($budgetData as $budget) {
        $fetchBudgetsExpensesIds->bindValue('budget_id', $budget['budget_id']);
        $fetchBudgetsExpensesIds->execute();
        $budgetsExpensesIds = $fetchBudgetsExpensesIds->fetchAll(PDO::FETCH_ASSOC);

        // Fetching expenses of the budget (cost)
        $fetchExpensesCost = $db->prepare('SELECT cost FROM expenses WHERE expense_id = :expense_id');
        foreach ($budgetsExpensesIds as $budgetsExpensesId) {
            $fetchExpensesCost->bindValue('expense_id', $budgetsExpensesId['expense_id']);
            $fetchExpensesCost->execute();
            $expensesCost = $fetchExpensesCost->fetch(PDO::FETCH_ASSOC);

            // Fetch category associated with the expense
            $fetchExpensesCategoryId = $db->prepare('SELECT category_id FROM expense_categories WHERE expense_id = :expense_id');
            $fetchExpensesCategoryId->bindValue('expense_id', $budgetsExpensesId['expense_id']);
            $fetchExpensesCategoryId->execute();
            $expenseCategoryId = $fetchExpensesCategoryId->fetch(PDO::FETCH_ASSOC);

            // Fetch category's name
            $fetchCategoryName = $db->prepare('SELECT category_name FROM categories WHERE category_id = :category_id');
            $fetchCategoryName->bindValue('category_id', $expenseCategoryId['category_id']);
            $fetchCategoryName->execute();
            $categoryName = $fetchCategoryName->fetch(PDO::FETCH_ASSOC);

            // Pushing data about category's expense cost of this budget into the array
            if (!isset($budgetExpensesCategoriesData[$budget['budget_id']][$categoryName['category_name']])) {
                $budgetExpensesCategoriesData[$budget['budget_id']][$categoryName['category_name']] = 0;
            }

            // Add the expense cost to the category's total cost
            $budgetExpensesCategoriesData[$budget['budget_id']][$categoryName['category_name']] += $expensesCost['cost'];
        }
    }

    // Fetching data about budget's incomes
    $fetchBudgetsIncomesIds = $db->prepare('SELECT income_id FROM budget_incomes WHERE budget_id = :budget_id');
    foreach ($budgetData as $budget) {
        $fetchBudgetsIncomesIds->bindValue('budget_id', $budget['budget_id']);
        $fetchBudgetsIncomesIds->execute();
        $budgetsIncomesIds = $fetchBudgetsIncomesIds->fetchAll(PDO::FETCH_ASSOC);

        // Fetching income's of the budget (cost)
        $fetchIncomesCost = $db->prepare('SELECT amount FROM incomes WHERE income_id = :income_id');
        foreach ($budgetsIncomesIds as $budgetsIncomesId) {
            $fetchIncomesCost->bindValue('income_id', $budgetsIncomesId['income_id']);
            $fetchIncomesCost->execute();
            $incomeAmount = $fetchIncomesCost->fetch(PDO::FETCH_ASSOC);

            // Fetch category associated with the income
            $fetchIncomesCategoryId = $db->prepare('SELECT category_id FROM income_categories WHERE income_id = :income_id');
            $fetchIncomesCategoryId->bindValue('income_id', $budgetsIncomesId['income_id']);
            $fetchIncomesCategoryId->execute();
            $incomeCategoryId = $fetchIncomesCategoryId->fetch(PDO::FETCH_ASSOC);

            // Fetch category's name
            $fetchCategoryName = $db->prepare('SELECT category_name FROM categories WHERE category_id = :category_id');
            $fetchCategoryName->bindValue('category_id', $incomeCategoryId['category_id']);
            $fetchCategoryName->execute();
            $categoryName = $fetchCategoryName->fetch(PDO::FETCH_ASSOC);

            // Pushing data about category's income amount of this budget into the array
            if (!isset($budgetIncomesCategoriesData[$budget['budget_id']][$categoryName['category_name']])) {
                $budgetIncomesCategoriesData[$budget['budget_id']][$categoryName['category_name']] = 0;
            }

            // Add the income amount to the category's total cost
            $budgetIncomesCategoriesData[$budget['budget_id']][$categoryName['category_name']] += $incomeAmount['amount'];
        }
    }
} else {
    header('Location: ../edit_budget.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Overview</title>
    <link rel="icon" href="../favicon.png"/>
    <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
            crossorigin="anonymous"
    />
</head>
<body class="bg-dark min-vh-100">
<header class="d-flex align-items-center justify-content-between text-light p-3">
    <div>
        <h1>Overview</h1>
        <h2>Logged in as: <?= htmlspecialchars($_SESSION['username']) ?> </h2>
    </div>
    <div>
        <nav>
            <a class="btn btn-success me-2" href="../create_budget">New Budget</a>
            <a class="btn btn-primary me-2" href="../categories">Manage categories</a>
            <a class="btn btn-primary me-2" href="../incomes_and_expenses">Manage incomes and expenses</a>
            <a class="btn btn-secondary me-2" href="../password_change">Change password</a>
            <a class="btn btn-secondary" href="../logout">Log out</a>
        </nav>
    </div>
</header>

<main class="container mt-5">
    <div class="col">
        <div class="bg-light border rounded-3 p-3">
            <div class="row">
                <div class="col-12 mx-auto my-3 p-2">
                    <div class="row text-center">
                        <h3>Budgets and categories totals</h3>
                    </div>
                    <?php
                    foreach ($budgetData as $budget) {
                        $budget_name = htmlspecialchars($budget['budget_name']);
                        $budget_balance = htmlspecialchars($budget['budget_balance']);
                        $budget_id = htmlspecialchars($budget['budget_id']);
                        if ($budget['budget_balance'] > 0) {
                            ?>
                            <div class="row m-2 border border-5 border-success rounded-3 p-3 my-3 shadow text-light">
                                <h4 class="text-black text-center"><?= $budget_name ?></h4>
                                <div class="col-3">
                                    <div class="row">
                                        <?php foreach ($budgetIncomesCategoriesData[$budget_id] as $name => $amount) { ?>
                                            <div class="col-12 mb-2">
                                                <div class="border border-success rounded-3 p-3 shadow bg-light text-success-emphasis">
                                                    <b><?= htmlspecialchars($name) ?>:</b> <?= htmlspecialchars($amount) ?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="row">
                                        <?php foreach ($budgetExpensesCategoriesData[$budget_id] as $name => $cost) { ?>
                                            <div class="col-12 mb-2">
                                                <div class="border border-danger rounded-3 p-3 shadow bg-light text-danger-emphasis">
                                                    <b><?= htmlspecialchars($name) ?>:</b> <?= htmlspecialchars($cost) ?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="row bg-success border rounded-3 p-3 my-1 shadow text-light justify-content-around">
                                        <p class="col-auto my-auto bg-light border rounded-5 text-success-emphasis text-center text-break"><?= $budget_name ?></p>
                                        <p class="col-auto my-auto bg-light border rounded-5 text-success-emphasis text-center text-break ms-1"><?= $budget_balance ?></p>
                                        <a class="btn btn-secondary col-3 fw-bold"
                                           href="../edit_budget/edit_budget.php?budget_id=<?= $budget_id ?>">Edit</a>
                                    </div>
                                </div>
                            </div>
                            <?php
                        } elseif ($budget['budget_balance'] < 0) {
                            ?>
                            <div class="row m-2 border border-5 border-danger rounded-3 p-3 my-3 shadow text-light">
                                <h4 class="text-black text-center"><?= $budget_name ?></h4>
                                <div class="col-3">
                                    <div class="row">
                                        <?php foreach ($budgetIncomesCategoriesData[$budget_id] as $name => $amount) { ?>
                                            <div class="col-12 mb-2">
                                                <div class="border border-success rounded-3 p-3 shadow bg-light text-success-emphasis">
                                                    <b><?= htmlspecialchars($name) ?>:</b> <?= htmlspecialchars($amount) ?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="row">
                                        <?php foreach ($budgetExpensesCategoriesData[$budget_id] as $name => $cost) { ?>
                                            <div class="col-12 mb-2">
                                                <div class="border border-danger rounded-3 p-3 shadow bg-light text-danger-emphasis">
                                                    <b><?= htmlspecialchars($name) ?>:</b> <?= htmlspecialchars($cost) ?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="row bg-danger border rounded-3 p-3 my-1 shadow text-light justify-content-around">
                                        <p class="col-auto my-auto bg-light border rounded-5 text-danger-emphasis text-center text-break"><?= $budget_name ?></p>
                                        <p class="col-auto my-auto bg-light border rounded-5 text-danger-emphasis text-center text-break ms-1"><?= $budget_balance ?></p>
                                        <a class="btn btn-secondary col-3 fw-bold"
                                           href="../edit_budget/edit_budget.php?budget_id=<?= $budget_id ?>">Edit</a>
                                    </div>
                                </div>
                            </div>
                            <?php
                        } else {
                            ?>
                            <div class="row m-2 border border-5 border-warning rounded-3 p-3 my-3 shadow text-light">
                                <h4 class="text-black text-center"><?= $budget_name ?></h4>
                                <div class="col-3">
                                    <div class="row">
                                        <?php foreach ($budgetIncomesCategoriesData[$budget_id] as $name => $amount) { ?>
                                            <div class="col-12 mb-2">
                                                <div class="border border-success rounded-3 p-3 shadow bg-light text-success-emphasis">
                                                    <b><?= htmlspecialchars($name) ?>:</b> <?= htmlspecialchars($amount) ?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="row">
                                        <?php foreach ($budgetExpensesCategoriesData[$budget_id] as $name => $cost) { ?>
                                            <div class="col-12 mb-2">
                                                <div class="border border-danger rounded-3 p-3 shadow bg-light text-danger-emphasis">
                                                    <b><?= htmlspecialchars($name) ?>:</b> <?= htmlspecialchars($cost) ?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="row bg-warning border rounded-3 p-3 my-1 shadow text-light justify-content-around">
                                        <p class="col-auto my-auto bg-light border rounded-5 text-warning-emphasis text-center text-break"><?= $budget_name ?></p>
                                        <p class="col-auto my-auto bg-light border rounded-5 text-warning-emphasis text-center text-break ms-1"><?= $budget_balance ?></p>
                                        <a class="btn btn-secondary col-3 fw-bold"
                                           href="../edit_budget/edit_budget.php?budget_id=<?= $budget_id ?>">Edit</a>
                                    </div>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</main>
<script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"
></script>
</body>
</html>
