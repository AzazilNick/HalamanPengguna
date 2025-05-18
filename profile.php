<?php
    if (!isset($_COOKIE['user']))
    header('Location: login.php');
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Profile</title>
    </head>
    <body>
        <header>
            <a href="dashboard.php">Dashboard</a>
            <a href="logut_process.php">Logout</a>
        </header>
        <h1>Welcome to your profile!</h1>
        <h2>Hi, <?$_COOKIE['username']?>!</h2>
        </body>
    </html>