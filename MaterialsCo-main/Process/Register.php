<?php
session_start();

include __DIR__ . "/../Classes/UsersCntrl.Class.php";

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['signUp'])) {

    // Sanitize input
    $name = filter_var(trim($_POST['signupName']), FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_var(trim($_POST['signupEmail']), FILTER_SANITIZE_EMAIL);
    $password = trim($_POST['signupPassword']);
    $passwordRep = trim($_POST['confirmPassword']);

    // Controller
    $signup = new UsersCntrl($name, $email, $password, $passwordRep);

    // Execute registration
    if(!$signup->registerUser()){
        header("Location: ../index.php");
        exit();
    } else{
        header("Location: ../index.php");
        exit();
    }

} else {
    header("Location: ../index.php?error=invalidAccess");
    exit();
}