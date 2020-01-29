<?php
require_once "login-api/auth-api.php";

if (isset($_POST['login'])) {

    $log_test = user::login($_POST['email'],$_POST['password']);
    if (!$log_test) {
        $_SESSION['msg'] = "Failed to log in";
        header("Location: index.php?login_succeed=0");
        exit;
    }
    $_SESSION['msg'] = "Successfully Logged in!";
    header("Location: index.php?login_succeed=1");
} else if (isset($_POST['create_account'])) {
    $error = "Passwords do not match";
    if (!isset($_POST['password']) || !(isset($_POST['password_confirm']))) {
        header("Location: index.php?login_succeed=0");
        exit;
    } else {
        $error = "Unknown Error";
        $log_test = user::create_account($_POST,$error);
    }

    if (!$log_test) {
        header("Location: index.php?create_account_succeed=0&error=$error");
        exit;
    }
    $_SESSION['msg']="Account Created";
    header("Location: index.php?create_account_succeed=1");
} else if (isset($_POST['logout'])) {
    user::get_this_user()->logout();
    $_SESSION['msg'] = "Logged out!";
    header("Location: index.php");
} else {
    die ("something went wrong");
}