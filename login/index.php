<?php
require "../db.php";
session_start();

if (!empty($_POST['email']) && !empty($_POST['password'])) {
    $email = trim($_POST['email']);

    $checkEmailQuery = $db->prepare('SELECT email FROM users WHERE email = :email');
    $checkEmailQuery->bindParam(':email', $email, PDO::PARAM_STR);
    $checkEmailQuery->execute();

    if ($checkEmailQuery->rowCount() > 0) {
        $getHashedPasswordQuery = $db->prepare('SELECT password FROM users WHERE email=:email');
        $getHashedPasswordQuery->bindParam(':email', $email, PDO::PARAM_STR);
        $getHashedPasswordQuery->execute();
        $hashedPassword = $getHashedPasswordQuery->fetch(PDO::FETCH_ASSOC)['password'];

        if (password_verify($_POST['password'],$hashedPassword)) {
            $getUserIdQuery = $db->prepare('SELECT user_id FROM users WHERE email = :email');
            $getUserIdQuery->bindParam(':email', $email, PDO::PARAM_STR);
            $getUserIdQuery->execute();
            $_SESSION['user_id'] = $getUserIdQuery->fetch(PDO::FETCH_ASSOC)['user_id'];
            setcookie('user_id',$_SESSION['user_id'], time() + 86000,'/');

            $getUsername = $db->prepare('SELECT username FROM users WHERE email = :email');
            $getUsername->bindParam(':email', $email, PDO::PARAM_STR);
            $getUsername->execute();
            $username = $getUsername->fetch(PDO::FETCH_ASSOC)['username'];
            $_SESSION['username'] = $username;
            setcookie('username', $username, time() + 86000,'/');

            header('Location: ..');
        } else {
            echo "Wrong password";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
</head>
<body class="bg-dark d-flex align-items-center justify-content-center" style="height: 100vh;">
<div class="container text-center">
    <div class="row">
        <div class="col-12">
            <h1 class="text-light">Log in</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-12 col-md-8 col-lg-6 mx-auto">
            <form method="post">
                <div class="form-group">
                    <label for="email" class="text-light">Email address</label>
                    <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp" placeholder="Enter email">
                </div>
                <div class="form-group">
                    <label for="password" class="text-light">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                </div>
                <button type="submit" class="btn btn-primary">Submit</button>
                <a class="btn btn-secondary" href="..">Go back</a>
            </form>
        </div>
    </div>
</div>
</body>

</html>

