<?php
session_start();
require '../database/db.php';

$user_id = $_SESSION['user_id'];
$selectCategoriesQuery = $db->prepare('SELECT * FROM categories WHERE user_id = :user_id');
$selectCategoriesQuery->bindParam(':user_id', $user_id, PDO::PARAM_INT);
$selectCategoriesQuery->execute();
$categories = $selectCategoriesQuery->fetchAll(PDO::FETCH_ASSOC);

$incomeCategories = [];
$expenseCategories = [];
$unusedCategories = [];

if ($selectCategoriesQuery->rowCount() > 0) {
    $category_id = 0;

    // Prepare queries for checking categories
    $checkIncomeCategoryQuery = $db->prepare('SELECT category_id FROM income_categories WHERE category_id = :category_id');
    $checkIncomeCategoryQuery->bindParam(':category_id', $category_id, PDO::PARAM_INT);

    $checkExpenseCategoryQuery = $db->prepare('SELECT category_id FROM expense_categories WHERE category_id = :category_id');
    $checkExpenseCategoryQuery->bindParam(':category_id', $category_id, PDO::PARAM_INT);

    foreach ($categories as $category) {
        $category_id = $category['category_id'];

        // Check if the category is an income category
        $checkIncomeCategoryQuery->execute();
        $isIncomeCategory = $checkIncomeCategoryQuery->fetch();

        // Check if the category is an expense category
        $checkExpenseCategoryQuery->execute();
        $isExpenseCategory = $checkExpenseCategoryQuery->fetch();

        if ($isIncomeCategory && $isExpenseCategory) {
            array_push($incomeCategories, [
                'category_id' => htmlspecialchars($category['category_id']),
                'category_name' => htmlspecialchars($category['category_name'])
            ]);
            array_push($expenseCategories, [
                'category_id' => htmlspecialchars($category['category_id']),
                'category_name' => htmlspecialchars($category['category_name'])
            ]);
        } elseif ($isIncomeCategory) {
            array_push($incomeCategories, [
                'category_id' => htmlspecialchars($category['category_id']),
                'category_name' => htmlspecialchars($category['category_name'])
            ]);
        } elseif ($isExpenseCategory) {
            array_push($expenseCategories, [
                'category_id' => htmlspecialchars($category['category_id']),
                'category_name' => htmlspecialchars($category['category_name'])
            ]);
        } else {
            array_push($unusedCategories, [
                'category_id' => htmlspecialchars($category['category_id']),
                'category_name' => htmlspecialchars($category['category_name'])
            ]);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Categories</title>
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
        <h1>Categories</h1>
    </div>
    <div>
        <nav>
            <a class="btn btn-secondary" href="..">Back</a>
        </nav>
    </div>
</header>

<main class="container mt-5">
    <div class="col">
        <div class="bg-light border rounded-3 p-3">
            <div class="row">
                <!-- Income categories -->
                <div id="incomeItemsContainer" class="col-4 mx-auto my-3 p-2">
                    <h3 class="text-center">Income Categories</h3>
                    <?php foreach ($incomeCategories as $incomeCategory) { ?>
                        <div class="row bg-success border rounded-3 p-3 my-1 shadow text-light justify-content-around" data-id="<?= $incomeCategory['category_id'] ?>">
                            <p class="col-auto my-auto bg-light border rounded-5 text-success-emphasis text-center text-break"><?= $incomeCategory['category_name'] ?></p>
                            <button class="deleteButton btn col-1" style="justify-self: end; font-weight: bold;">X</button>
                        </div>
                    <?php } ?>
                </div>
                <!-- Expense categories -->
                <div id="expenseItemsContainer" class="col-4 mx-auto my-3 p-2">
                    <h3 class="text-center">Expense Categories</h3>
                    <?php foreach ($expenseCategories as $expenseCategory) { ?>
                        <div class="row bg-danger border rounded-3 p-3 my-1 shadow text-light justify-content-around" data-id="<?= $expenseCategory['category_id'] ?>">
                            <p class="col-auto my-auto bg-light border rounded-5 text-success-emphasis text-center text-break"><?= $expenseCategory['category_name'] ?></p>
                            <button class="deleteButton btn col-1" style="justify-self: end; font-weight: bold;">X</button>
                        </div>
                    <?php } ?>
                </div>
                <!-- Unused categories -->
                <div id="unusedItemsContainer" class="col-4 mx-auto my-3 p-2">
                    <h3 class="text-center">Unused Categories</h3>
                    <?php foreach ($unusedCategories as $unusedCategory) { ?>
                        <div class="row bg-warning border rounded-3 p-3 my-1 shadow text-dark justify-content-around" data-id="<?= $unusedCategory['category_id'] ?>">
                            <p class="col-auto my-auto bg-light border rounded-5 text-warning-emphasis text-center text-break"><?= $unusedCategory['category_name'] ?></p>
                            <button class="deleteButton btn col-1" style="justify-self: end; font-weight: bold;">X</button>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="row">
                <div class="col-12 text-center">
                    <form class="d-inline-block">
                        <input type="text" id="newCategoryName" class="form-control my-2" placeholder="New Category Name" required>
                        <button id="addCategoryButton" class="btn btn-primary my-2">Add Category</button>
                    </form>
                    <button id="saveCategoriesButton" class="btn btn-success my-2">Save the List</button>
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
