<?php
session_start();
require '../database/db.php';

$user_id = $_SESSION['user_id'];
//selecting expenses
$selectExpensesQuery = $db->prepare('SELECT * FROM expenses WHERE user_id = :user_id');
$selectExpensesQuery->bindValue(':user_id', $user_id);
$selectExpensesQuery->execute();
$expenses = $selectExpensesQuery->fetchAll();

//select incomes
$selectIncomesQuery = $db->prepare('SELECT * FROM incomes WHERE user_id = :user_id');
$selectIncomesQuery->bindValue(':user_id', $user_id);
$selectIncomesQuery->execute();
$incomes = $selectIncomesQuery->fetchAll();

//select categories
$selectCategoriesQuery = $db->prepare('SELECT * FROM categories WHERE user_id = :user_id');
$selectCategoriesQuery->bindValue(':user_id', $user_id);
$selectCategoriesQuery->execute();
$categories = $selectCategoriesQuery->fetchAll();

//select income categories
$selectIncomeCategoriesQuery = $db->prepare('SELECT * FROM income_categories WHERE user_id = :user_id');
$selectIncomeCategoriesQuery->bindValue(':user_id', $user_id);
$selectIncomeCategoriesQuery->execute();
$incomeCategories = $selectIncomeCategoriesQuery->fetchAll();

//select expense categories
$selectExpenseCategoriesQuery = $db->prepare('SELECT * FROM expense_categories WHERE user_id = :user_id');
$selectExpenseCategoriesQuery->bindValue(':user_id', $user_id);
$selectExpenseCategoriesQuery->execute();
$expenseCategories = $selectExpenseCategoriesQuery->fetchAll();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Incomes and Expenses</title>
    <link rel="icon" href="../favicon.png" />
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
        <h1>Incomes and Expenses</h1>
    </div>
    <div>
        <a class="btn btn-success me-2" id="saveButton">Save the list</a>
        <a class="btn btn-primary me-2" id="constructBudgetButton">Add selected items to a new budget</a>
        <a class="btn btn-secondary" href="..">Back</a>
    </div>
</header>

<main class="container mt-5">
    <div class="col">
        <div class="bg-light border rounded-3 p-3">
            <div class="row">
                <!-- Incomes -->
                <div id="incomeItemsContainer" class="col-4 mx-auto my-3 p-2">
                    <h3 class="text-center">Incomes</h3>
                    <?php foreach ($incomes as $income) { ?>
                        <div class="income-item row bg-success border rounded-3 p-3 my-1 shadow text-light justify-content-around" data-id="<?= $income['income_id'] ?>">
                            <input value="<?= $income['income_name'] ?>" type="text" class="incomeName w-25 col-auto my-auto bg-light border rounded-5 text-success-emphasis text-center text-break">
                            <input value="<?= $income['amount'] ?>" id='itemValue' class="w-25 col-auto my-auto bg-light border rounded-5 text-success-emphasis text-center text-break ms-1">
                            <select class="form-select w-25">
                                <?php
                                foreach ($categories as $category) {
                                    foreach ($incomeCategories as $incomeCategory) {
                                        if($income['income_id'] === $incomeCategory['income_id'] && $incomeCategory['category_id'] === $category['category_id']){
                                            ?>
                                            <option selected value="<?= $category['category_id'] ?>" data-category-id="<?= $category['category_id'] ?>"><?= $category['category_name'] ?></option>
                                            <?php
                                        } else {
                                            ?>
                                            <option value="<?= $category['category_id'] ?>" data-category-id="<?= $category['category_id'] ?>"><?= $category['category_name'] ?></option>
                                            <?php
                                        }
                                    }
                                }
                                ?>
                            </select>
                            <input class="align-self-center form-check-input" type="checkbox" value="" id="defaultCheck1">
                            <button class="deleteButtonIncome btn col-1" style="justify-self: end; font-weight: bold;">X</button>
                        </div>
                    <?php } ?>
                </div>
                <!-- Expenses -->
                <div id="expenseItemsContainer" class="col-4 mx-auto my-3 p-2">
                    <h3 class="text-center">Expenses</h3>
                    <?php foreach ($expenses as $expense) { ?>
                        <div class="expense-item row bg-danger border rounded-3 p-3 my-1 shadow text-light justify-content-around" data-id="<?= $expense['expense_id'] ?>">
                            <input value="<?= $expense['expense_name'] ?>" type="text" class="expenseName w-25 col-auto my-auto bg-light border rounded-5 text-success-emphasis text-center text-break">
                            <input value="<?= $expense['cost'] ?>" id='itemValue' class="w-25 col-auto my-auto bg-light border rounded-5 text-success-emphasis text-center text-break ms-1">
                            <select class="form-select w-25">
                                <?php
                                foreach ($categories as $category) {
                                    foreach ($expenseCategories as $expenseCategory) {
                                        if($expense['expense_id'] === $expenseCategory['expense_id'] && $expenseCategory['category_id'] === $category['category_id']){
                                            ?>
                                            <option selected value="<?= $category['category_id'] ?>" data-category-id="<?= $category['category_id'] ?>"><?= $category['category_name'] ?></option>
                                            <?php
                                        } else {
                                            ?>
                                            <option value="<?= $category['category_id'] ?>" data-category-id="<?= $category['category_id'] ?>"><?= $category['category_name'] ?></option>
                                            <?php
                                        }
                                    }
                                }
                                ?>
                            </select>
                            <input class="align-self-center form-check-input" type="checkbox" value="" id="defaultCheck1">
                            <button class="deleteButtonExpense btn col-1" style="justify-self: end; font-weight: bold;">X</button>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</main>
<script src="script.js"></script>
<script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"
></script>
</body>
</html>
