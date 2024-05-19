<?php
session_start();
require 'database/db.php';

if (!empty($_SESSION['user_id'])) {
    header('Location: budgets');
} else if (!empty($_COOKIE['user_id'])) {
    $_SESSION['user_id'] = $_COOKIE['user_id'];
    $_SESSION['username'] = $_COOKIE['username'];
    header('Location: budgets');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Budget Planner</title>
    <link rel="icon" href="favicon.png" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body class="bg-dark d-flex align-items-center justify-content-center" style="height: 100vh;">
<div class="container text-center">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="text-light">Welcome to Budget Planner</h1>
        </div>
    </div>
    <div class="row">
        <div class="col d-flex justify-content-center">
            <a class="btn btn-primary mr-2" href="register">Register</a>
            <a class="btn btn-secondary" href="login">Log in</a>
        </div>
    </div>
</div>
</body>
</html>
