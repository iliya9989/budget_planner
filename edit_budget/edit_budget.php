<?php
require '../database/db.php';

$budget_id = null;
$budgetData = [];
$incomes = [];
$expenses = [];
$totalIncome = 0;
$totalExpenses = 0;

if (isset($_GET['budget_id']) && is_numeric($_GET['budget_id'])) {
    $budget_id = (int)$_GET['budget_id'];

    try {
        // Query to get budget details
        $getBudgetDataQuery = $db->prepare('SELECT 
                b.budget_name, 
                b.budget_balance
            FROM 
                budgets b
            WHERE 
                b.budget_id = :budget_id');
        $getBudgetDataQuery->bindValue(':budget_id', $budget_id, PDO::PARAM_INT);
        $getBudgetDataQuery->execute();
        $budgetData = $getBudgetDataQuery->fetch(PDO::FETCH_ASSOC);

        // Query to get all incomes for the budget
        $getIncomesQuery = $db->prepare('SELECT 
                i.income_name AS income_name, 
                i.amount AS income_amount
            FROM 
                incomes i
            JOIN 
                budget_incomes bi ON i.income_id = bi.income_id
            WHERE 
                bi.budget_id = :budget_id');
        $getIncomesQuery->bindValue(':budget_id', $budget_id, PDO::PARAM_INT);
        $getIncomesQuery->execute();
        $incomes = $getIncomesQuery->fetchAll(PDO::FETCH_ASSOC);

        // Query to get all expenses for the budget
        $getExpensesQuery = $db->prepare('SELECT 
                e.expense_name AS expense_name, 
                e.cost AS expense_cost
            FROM 
                expenses e
            JOIN 
                budget_expenses be ON e.expense_id = be.expense_id
            WHERE 
                be.budget_id = :budget_id');
        $getExpensesQuery->bindValue(':budget_id', $budget_id, PDO::PARAM_INT);
        $getExpensesQuery->execute();
        $expenses = $getExpensesQuery->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid or missing budget ID.";
}

?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
            crossorigin="anonymous"
    />
    <link rel="icon" href="../favicon.png"/>
    <title>New Budget</title>
</head>
<body class="bg-secondary-subtle">
<div id="budget_id" style="display: none"><?= $_GET['budget_id'] ?></div>
<div class="container">
    <!-- jumbotron -->
    <div class="row">
        <div class="col">
            <div class="text-center my-5 bg-light border rounded-3">
                <h1 class="h1">Welcome to Budget Planner!</h1>
                <p class="lead">Start by submitting an item</p>
            </div>
            <form>
                <div class="form-group">
                    <label for="budgetName">Budget's name:</label>
                    <input
                            value="<?= $budgetData['budget_name'] ?>"
                            class="form-control"
                            type="text"
                            name="budgetName"
                            id="budgetName"
                            style="width: 25%"
                            required
                    />
                </div>
                <div class="form-group">
                    <br/>
                    <a id="saveTheBudgetButton" class="btn btn-primary text-light"
                    >Save the budget</a
                    >
                    <a id="deleteTheBudgetButton" class="btn btn-danger text-light"
                    >Delete the budget</a
                    >
                </div>
            </form>
        </div>
    </div>
    <!-- end of jumbotron -->

    <!-- main -->
    <div class="row">
        <div class="col text-center">
            <h2 class="h2">Income</h2>
        </div>
        <div class="col text-center">
            <h2 class="h2">Expenses</h2>
        </div>
    </div>
    <div class="row">
        <!-- income -->
        <div class="col">
            <div class="bg-light border rounded-3">
                <div class="col-8 mx-auto">
                    <form class="row">
                        <div class="col-6">
                            <input
                                    id="incomeItemName"
                                    type="text"
                                    class="form-control my-4"
                                    placeholder="Item"
                                    required
                            />
                        </div>
                        <div class="col-3">
                            <input
                                    id="incomeItemValue"
                                    type="number"
                                    class="form-control my-4"
                                    placeholder="Value"
                                    required
                            />
                        </div>
                        <button
                                id="submitIncome"
                                type="submit"
                                class="btn col-3 btn-info text-light my-4 shadow"
                        >
                            Submit
                        </button>
                    </form>
                </div>
                <div class="row">
                    <div
                            id="incomeItemsContainer"
                            class="col-7 mx-auto my-3 p-2"
                    >
                        <?php
                        foreach ($incomes as $income) {
                            $totalIncome += $income['income_amount'];
                            ?>
                            <div class="row bg-success border rounded-3 p-3 my-1 shadow text-light justify-content-around">
                                <p class="col-auto my-auto bg-light border rounded-5 text-success-emphasis text-center text-break"><?= $income['income_name'] ?></p>
                                <p id='itemValue'
                                   class="col-auto my-auto bg-light border rounded-5 text-success-emphasis text-center text-break ms-1"><?= $income['income_amount'] ?></p>
                                <button class="deleteButtonIncome btn col-1"
                                        style="justify-self: end; font-weight: bold;">X
                                </button>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>
                <div class="row">
                    <div
                            id="totalIncomeField"
                            class="col-7 mx-auto my-3 p-2 border rounded-3 bg-secondary text-light text-center"
                    >
                        Total Income
                    </div>
                </div>
            </div>
        </div>
        <!-- end of income -->

        <!-- expenses -->
        <div class="col">
            <div class="bg-light border rounded-3">
                <div class="col-8 mx-auto">
                    <form class="row">
                        <div class="col-6">
                            <input
                                    id="expensesItemName"
                                    type="text"
                                    class="form-control my-4"
                                    placeholder="Item"
                                    required
                            />
                        </div>
                        <div class="col-3">
                            <input
                                    id="expensesItemValue"
                                    type="number"
                                    class="form-control my-4"
                                    placeholder="Value"
                                    required
                            />
                        </div>
                        <button
                                id="submitExpenses"
                                type="submit"
                                class="btn col-3 btn-info text-light my-4 shadow"
                        >
                            Submit
                        </button>
                    </form>
                </div>
                <div
                        id="expensesItemsContainer"
                        class="col-7 mx-auto my-3 p-2"
                >
                    <?php
                    foreach ($expenses as $expense) {
                        $totalExpenses += $expense['expense_cost'];
                        ?>
                        <div class="row bg-danger border rounded-3 p-3 my-1 shadow text-light justify-content-around">
                            <p class="col-auto my-auto bg-light border rounded-5 text-danger-emphasis text-center text-break"><?= $expense['expense_name'] ?></p>
                            <p id='itemValue'
                               class="col-auto my-auto bg-light border rounded-5 text-danger-emphasis text-center text-break ms-1"><?= $expense['expense_cost'] ?></p>
                            <button class="deleteButtonExpenses btn col-1"
                                    style="justify-self: end; font-weight: bold;">X
                            </button>
                        </div>
                        <?php
                    }
                    ?>

                </div>
                <div class="row">
                    <div
                            id="totalExpensesField"
                            class="col-7 mx-auto my-3 p-2 border rounded-3 bg-secondary text-light text-center"
                    >
                        Total Expenses
                    </div>
                </div>
            </div>
        </div>
        <!-- end of expenses -->
    </div>
    <div class="row">
        <div
                id="balanceField"
                class="col-5 bg-secondary text-light my-4 mx-auto p-3 border rounded-3 text-center"
        >
            Balance
        </div>
    </div>
    <!-- end of main -->
</div>
<script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"
></script>
<div id="totalIncome" style="display: none"><?= $totalIncome ?></div>
<div id="totalExpenses" style="display: none"><?= $totalExpenses ?></div>
<div id="totalBalance" style="display: none"><?= $budgetData['budget_balance'] ?></div>
<script defer src="script.js"></script>
</body>
</html>