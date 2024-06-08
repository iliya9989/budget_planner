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
} else {
    header('Location: ../edit_budget.php');
    exit();
}

$incomeCategoriesAmounts = [];
$expenseCategoriesCosts = [];

// Fetching categories
$fetchCategories = $db->prepare('SELECT category_id, category_name FROM categories WHERE user_id = :user_id');
$fetchCategories->bindValue(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$fetchCategories->execute();
$categories = $fetchCategories->fetchAll(PDO::FETCH_ASSOC);

// Prepare statements for fetching category's expenses and incomes
$fetchCategoryExpenses = $db->prepare('SELECT expense_id FROM expense_categories WHERE category_id = :category_id');
$fetchCategoryIncomes = $db->prepare('SELECT income_id FROM income_categories WHERE category_id = :category_id');

foreach ($categories as $category) {
    $fetchCategoryExpenses->bindValue(':category_id', $category['category_id'], PDO::PARAM_INT);
    $fetchCategoryExpenses->execute();
    $categoryExpensesIds = $fetchCategoryExpenses->fetchAll(PDO::FETCH_ASSOC);

    $fetchCategoryIncomes->bindValue(':category_id', $category['category_id'], PDO::PARAM_INT);
    $fetchCategoryIncomes->execute();
    $categoryIncomeIds = $fetchCategoryIncomes->fetchAll(PDO::FETCH_ASSOC);

    // Fetching category's values of incomes and expenses
    $fetchExpenseCosts = $db->prepare('SELECT cost FROM expenses WHERE expense_id = :expense_id');
    $fetchIncomeValues = $db->prepare('SELECT amount FROM incomes WHERE income_id = :income_id');

    // Initialize category in arrays if not set
    if (!isset($expenseCategoriesCosts[$category['category_name']])) {
        $expenseCategoriesCosts[$category['category_name']] = 0;
    }
    if (!isset($incomeCategoriesAmounts[$category['category_name']])) {
        $incomeCategoriesAmounts[$category['category_name']] = 0;
    }

    foreach ($categoryExpensesIds as $categoryExpensesId) {
        $fetchExpenseCosts->bindValue(':expense_id', $categoryExpensesId['expense_id'], PDO::PARAM_INT);
        $fetchExpenseCosts->execute();
        $cost = $fetchExpenseCosts->fetch(PDO::FETCH_ASSOC);
        if ($cost) {
            $expenseCategoriesCosts[$category['category_name']] += $cost['cost'];
        }
    }

    foreach ($categoryIncomeIds as $categoryIncomeId) {
        $fetchIncomeValues->bindValue(':income_id', $categoryIncomeId['income_id'], PDO::PARAM_INT);
        $fetchIncomeValues->execute();
        $amount = $fetchIncomeValues->fetch(PDO::FETCH_ASSOC);
        if ($amount) {
            $incomeCategoriesAmounts[$category['category_name']] += $amount['amount'];
        }
    }
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
            <a class="btn btn-primary me-2" href="../create_budget">New Budget</a>
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
                <div class="col">
                    <div class="row">
                        <div class="col">
                            <h3>Income Categories Totals</h3>
                            <?php
                            foreach ($incomeCategoriesAmounts as $name => $amount) {
                                if ($amount === 0) continue;
                                ?>
                                <div class="row m-2 border border-success rounded-3 p-3 my-1 shadow text-light justify-content-around">
                                    <p class="col-auto my-auto bg-light border rounded-5 text-success-emphasis text-center text-break">
                                        <b><?= htmlspecialchars($name) ?>:</b> <?= htmlspecialchars($amount) ?></p>
                                </div>
                            <?php } ?>
                        </div>
                        <div class="col">
                            <h3>Expense Categories Totals</h3>
                            <?php
                            foreach ($expenseCategoriesCosts as $name => $cost) {
                                if ($cost === 0) continue;
                                ?>
                                <div class="row m-2 border border-danger rounded-3 p-3 my-1 shadow text-light justify-content-around">
                                    <p class="col-auto my-auto bg-light border rounded-5 text-success-emphasis text-center text-break">
                                        <b><?= htmlspecialchars($name) ?>:</b> <?= htmlspecialchars($cost) ?></p>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div id="incomeItemsContainer" class="col-7 mx-auto my-3 p-2">
                    <?php
                    foreach ($budgetData as $budget) {
                        $budget_name = htmlspecialchars($budget['budget_name']);
                        $budget_balance = htmlspecialchars($budget['budget_balance']);
                        $budget_id = htmlspecialchars($budget['budget_id']);

                        if ($budget['budget_balance'] > 0) {
                            ?>
                            <div class="col-7 mx-auto my-3 p-2">
                                <div class="row bg-success border rounded-3 p-3 my-1 shadow text-light justify-content-around">
                                    <p class="col-auto my-auto bg-light border rounded-5 text-success-emphasis text-center text-break"><?= $budget_name ?></p>
                                    <p class="col-auto my-auto bg-light border rounded-5 text-success-emphasis text-center text-break ms-1"><?= $budget_balance ?></p>
                                    <a class="btn btn-secondary col-3 fw-bold"
                                       href="../edit_budget/edit_budget.php?budget_id=<?= $budget_id ?>">Edit</a>
                                </div>
                            </div>
                            <?php
                        } elseif ($budget['budget_balance'] < 0) {
                            ?>
                            <div class="col-7 mx-auto my-3 p-2">
                                <div class="row bg-danger border rounded-3 p-3 my-1 shadow text-light justify-content-around">
                                    <p class="col-auto my-auto bg-light border rounded-5 text-danger-emphasis text-center text-break"><?= $budget_name ?></p>
                                    <p class="col-auto my-auto bg-light border rounded-5 text-danger-emphasis text-center text-break ms-1"><?= $budget_balance ?></p>
                                    <a class="btn btn-secondary col-3 fw-bold"
                                       href="../edit_budget/edit_budget.php?budget_id=<?= $budget_id ?>">Edit</a>
                                </div>
                            </div>
                            <?php
                        } else {
                            ?>
                            <div class="col-7 mx-auto my-3 p-2">
                                <div class="row bg-warning border rounded-3 p-3 my-1 shadow text-light justify-content-around">
                                    <p class="col-auto my-auto bg-light border rounded-5 text-warning-emphasis text-center text-break"><?= $budget_name ?></p>
                                    <p class="col-auto my-auto bg-light border rounded-5 text-warning-emphasis text-center text-break ms-1"><?= $budget_balance ?></p>
                                    <a class="btn btn-secondary col-3 fw-bold"
                                       href="../edit_budget/edit_budget.php?budget_id=<?= $budget_id ?>">Edit</a>
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
