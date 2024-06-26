<?php
session_start();
require '../database/db.php';

$user_id = $_SESSION['user_id'];

// Selecting expenses
$selectExpensesQuery = $db->prepare('SELECT * FROM expenses WHERE user_id = :user_id');
$selectExpensesQuery->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$selectExpensesQuery->execute();
$expenses = $selectExpensesQuery->fetchAll(PDO::FETCH_ASSOC);

// Selecting incomes
$selectIncomesQuery = $db->prepare('SELECT * FROM incomes WHERE user_id = :user_id');
$selectIncomesQuery->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$selectIncomesQuery->execute();
$incomes = $selectIncomesQuery->fetchAll(PDO::FETCH_ASSOC);

// Selecting categories
$selectCategoriesQuery = $db->prepare('SELECT * FROM categories WHERE user_id = :user_id');
$selectCategoriesQuery->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$selectCategoriesQuery->execute();
$categories = $selectCategoriesQuery->fetchAll(PDO::FETCH_ASSOC);

// Selecting income categories
$selectIncomeCategoriesQuery = $db->prepare('SELECT * FROM income_categories WHERE user_id = :user_id');
$selectIncomeCategoriesQuery->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$selectIncomeCategoriesQuery->execute();
$incomeCategories = $selectIncomeCategoriesQuery->fetchAll(PDO::FETCH_ASSOC);

// Selecting expense categories
$selectExpenseCategoriesQuery = $db->prepare('SELECT * FROM expense_categories WHERE user_id = :user_id');
$selectExpenseCategoriesQuery->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$selectExpenseCategoriesQuery->execute();
$expenseCategories = $selectExpenseCategoriesQuery->fetchAll(PDO::FETCH_ASSOC);

// Create a mapping of categories for easy access
$categoryMap = [];
foreach ($categories as $category) {
    $categoryMap[$category['category_id']] = $category['category_name'];
}

function getCategoryOptions($categoryMap, $selectedCategoryId)
{
    $options = '';
    foreach ($categoryMap as $categoryId => $categoryName) {
        $selected = $categoryId == $selectedCategoryId ? 'selected' : '';
        $options .= "<option value=\"" . htmlspecialchars($categoryId) . "\" $selected>" . htmlspecialchars($categoryName) . "</option>";
    }
    return $options;
}
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
                    <?php foreach ($incomes as $income) {
                        $selectedCategoryId = null;
                        foreach ($incomeCategories as $incomeCategory) {
                            if ($income['income_id'] === $incomeCategory['income_id']) {
                                $selectedCategoryId = $incomeCategory['category_id'];
                                break;
                            }
                        }
                        ?>
                        <div class="income-item row bg-success border rounded-3 p-3 my-1 shadow text-light justify-content-around" data-id="<?= htmlspecialchars($income['income_id']) ?>">
                            <input value="<?= htmlspecialchars($income['income_name']) ?>" type="text" class="incomeName w-25 col-auto my-auto bg-light border rounded-5 text-success-emphasis text-center text-break">
                            <input value="<?= htmlspecialchars($income['amount']) ?>" id='itemValue' class="w-25 col-auto my-auto bg-light border rounded-5 text-success-emphasis text-center text-break ms-1">
                            <select class="form-select w-25">
                                <?= getCategoryOptions($categoryMap, $selectedCategoryId) ?>
                            </select>
                            <input class="align-self-center form-check-input" type="checkbox" value="" id="defaultCheck1">
                            <button class="deleteButtonIncome btn col-1" style="justify-self: end; font-weight: bold;">X</button>
                        </div>
                    <?php } ?>
                </div>
                <!-- Expenses -->
                <div id="expenseItemsContainer" class="col-4 mx-auto my-3 p-2">
                    <h3 class="text-center">Expenses</h3>
                    <?php foreach ($expenses as $expense) {
                        $selectedCategoryId = null;
                        foreach ($expenseCategories as $expenseCategory) {
                            if ($expense['expense_id'] === $expenseCategory['expense_id']) {
                                $selectedCategoryId = $expenseCategory['category_id'];
                                break;
                            }
                        }
                        ?>
                        <div class="expense-item row bg-danger border rounded-3 p-3 my-1 shadow text-light justify-content-around" data-id="<?= htmlspecialchars($expense['expense_id']) ?>">
                            <input value="<?= htmlspecialchars($expense['expense_name']) ?>" type="text" class="expenseName w-25 col-auto my-auto bg-light border rounded-5 text-success-emphasis text-center text-break">
                            <input value="<?= htmlspecialchars($expense['cost']) ?>" id='itemValue' class="w-25 col-auto my-auto bg-light border rounded-5 text-success-emphasis text-center text-break ms-1">
                            <select class="form-select w-25">
                                <?= getCategoryOptions($categoryMap, $selectedCategoryId) ?>
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
