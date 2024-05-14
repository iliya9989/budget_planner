<?php
session_start();
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
            <form>
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

