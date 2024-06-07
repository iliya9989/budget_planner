<?php
session_start();
require '../database/db.php';

$budgetData = null;
if (!empty($_SESSION['username']) && !empty($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $getBudgetDataQuery = $db->prepare('SELECT budget_id, budget_name, budget_balance 
                FROM budgets 
                WHERE user_id = :user_id');
    $getBudgetDataQuery->bindParam(':user_id', $user_id);
    $getBudgetDataQuery->execute();
    $budgetData = $getBudgetDataQuery->fetchAll(PDO::FETCH_ASSOC);
} else {
    header('Location: ../edit_budget.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Budgets</title>
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
        <h1>My Budgets</h1>
        <h3>Logged in as: <?= $_SESSION['username'] ?> </h3>
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
                <div id="incomeItemsContainer" class="col-7 mx-auto my-3 p-2">
                    <?php
                    foreach ($budgetData as $budget) {
                        if ($budget['budget_balance'] > 0) {
                            ?>
                            <div class="col-7 mx-auto my-3 p-2">
                                <div class="row bg-success border rounded-3 p-3 my-1 shadow text-light justify-content-around">
                                    <p class="col-auto my-auto bg-light border rounded-5 text-success-emphasis text-center text-break"><?= $budget['budget_name'] ?></p>
                                    <p class="col-auto my-auto bg-light border rounded-5 text-success-emphasis text-center text-break ms-1"><?= $budget['budget_balance'] ?></p>
                                    <a class="btn btn-secondary col-3 fw-bold" href="../edit_budget/edit_budget.php?budget_id=<?= htmlspecialchars($budget['budget_id']) ?>">Edit</a>
                                </div>
                            </div>
                            <?php
                        } else if ($budget['budget_balance'] < 0) {
                            ?>
                            <div class="col-7 mx-auto my-3 p-2">
                                <div class="row bg-danger border rounded-3 p-3 my-1 shadow text-light justify-content-around">
                                    <p class="col-auto my-auto bg-light border rounded-5 text-danger-emphasis text-center text-break"><?= $budget['budget_name'] ?></p>
                                    <p class="col-auto my-auto bg-light border rounded-5 text-danger-emphasis text-center text-break ms-1"><?= $budget['budget_balance'] ?></p>
                                    <a class="btn btn-secondary col-3 fw-bold" href="../edit_budget/edit_budget.php?budget_id=<?= htmlspecialchars($budget['budget_id']) ?>">Edit</a>
                                </div>
                            </div>
                            <?php
                        } else if ($budget['budget_balance'] === 0) {
                            ?>
                            <div class="col-7 mx-auto my-3 p-2">
                                <div class="row bg-warning border rounded-3 p-3 my-1 shadow text-light justify-content-around">
                                    <p class="col-auto my-auto bg-light border rounded-5 text-warning-emphasis text-center text-break"><?= $budget['budget_name'] ?></p>
                                    <p class="col-auto my-auto bg-light border rounded-5 text-warning-emphasis text-center text-break ms-1"><?= $budget['budget_balance'] ?></p>
                                    <a class="btn btn-secondary col-3 fw-bold" href="../edit_budget/edit_budget.php?budget_id=<?= htmlspecialchars($budget['budget_id']) ?>">Edit</a>
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
