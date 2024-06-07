<?php
session_start();
require "../database/db.php";

if (!empty($_POST['current-password']) && !empty($_POST['new-password'])) {

    // fetching the current password
    $fetchCurrentPasswordHashQuery = $db->prepare('SELECT password FROM users WHERE user_id = :user_id');
    $fetchCurrentPasswordHashQuery->bindValue('user_id', $_SESSION['user_id']);
    $fetchCurrentPasswordHashQuery->execute();
    $hashedPassword = $fetchCurrentPasswordHashQuery->fetch(PDO::FETCH_ASSOC)['password'];

    // verifying the password
    if (password_verify($_POST['current-password'], $hashedPassword)) {
        // updating the password
        $hashedNewPassword = password_hash($_POST['new-password'], PASSWORD_DEFAULT);
        $updatePasswordQuery = $db->prepare('UPDATE users SET password = :hashedNewPassword WHERE user_id = :user_id');
        $updatePasswordQuery->bindValue('user_id', $_SESSION['user_id']);
        $updatePasswordQuery->bindValue('hashedNewPassword', $hashedNewPassword);
        $updatePasswordQuery->execute();
        header('Location: ..');
        exit();
    } else {
        echo "<script type='text/javascript'>alert('Wrong current password');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Change your password</title>
    <link rel="icon" href="../favicon.png" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body class="bg-dark d-flex align-items-center justify-content-center" style="height: 100vh;">
<div class="container text-center">
    <div class="row">
        <div class="col-12">
            <h1 class="text-light">Change your password</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-md-8 col-lg-6 mx-auto">
            <form method="post">
                <div class="form-group">
                    <label for="current-password" class="text-light">Current Password</label>
                    <input type="password" class="form-control" id="current-password" name="current-password" aria-describedby="emailHelp" placeholder="Enter your current password">
                </div>
                <div class="form-group">
                    <label for="new-password" class="text-light">New password</label>
                    <input type="password" class="form-control" id="new-password" name="new-password" placeholder="Enter your new password">
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
                <a class="btn btn-secondary" href="..">Go back</a>
            </form>
        </div>
    </div>
</div>
</body>
</html>
