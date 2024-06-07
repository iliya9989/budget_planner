<?php
require "../database/db.php";
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

if (!empty($_POST['email']) && !empty($_POST['password'])) {
    $email = trim($_POST['email']);
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $checkEmailQuery = $db->prepare('SELECT email FROM users WHERE email = :email');
        $checkEmailQuery->bindParam(':email', $email, PDO::PARAM_STR);
        $checkEmailQuery->execute();
        if ($checkEmailQuery->rowCount() > 0) {
            echo "User with this E-mail already exists";
            die();
        } else {
            $username = htmlspecialchars($_POST['username']);
            $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
            $registerUserQuery = $db->prepare('INSERT INTO users (email, password, username) VALUES (:email, :password, :username)');
            $registerUserQuery->bindParam(':email', $email, PDO::PARAM_STR);
            $registerUserQuery->bindParam(':password', $hashedPassword, PDO::PARAM_STR);
            $registerUserQuery->bindParam(':username', $username, PDO::PARAM_STR);
            $registerUserQuery->execute();

            $getUserIdQuery = $db->prepare('SELECT user_id FROM users WHERE email = :email');
            $getUserIdQuery->bindParam(':email', $email, PDO::PARAM_STR);
            $getUserIdQuery->execute();
            $_SESSION['user_id'] = htmlspecialchars($getUserIdQuery->fetch(PDO::FETCH_ASSOC)['user_id']);
            setcookie('user_id', $_SESSION['user_id'], time() + 86000, '/');
            $_SESSION['username'] = $username;
            setcookie('username', $username, time() + 86000, '/');

            header('Location: ..');
            exit();
        }
    } else {
        echo "Invalid email";
        die();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="icon" href="../favicon.png" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body class="bg-dark d-flex align-items-center justify-content-center" style="height: 100vh;">
<div class="container text-center">
    <div class="row">
        <div class="col-12">
            <h1 class="text-light">Register</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-md-8 col-lg-6 mx-auto">
            <form method="post">
                <div class="form-group">
                    <label for="username" class="text-light">Username</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                </div>
                <div class="form-group">
                    <label for="email" class="text-light">Email address</label>
                    <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp" placeholder="Enter email" required>
                </div>
                <div class="form-group">
                    <label for="password" class="text-light">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
                <a class="btn btn-secondary" href="..">Go back</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
