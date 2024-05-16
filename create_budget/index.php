<?php

?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH"
        crossorigin="anonymous"
    />
    <link rel="icon" href="favicon.png" />
    <title>Budget Planner</title>
</head>
<body class="bg-secondary-subtle">
<div class="container">
    <!-- jumbotron -->
    <div class="row">
        <div class="col">
            <div class="text-center my-5 bg-light border rounded-3">
                <h1 class="h1">Welcome to Budget Planner!</h1>
                <p class="lead">Start by submitting an item</p>
            </div>
            <a id="saveTheBudgetButton" class="btn btn-primary text-light ml-auto">Save the budget</a>
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
                    ></div>
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
                ></div>
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
<script src="script.js"></script>
</body>
</html>

