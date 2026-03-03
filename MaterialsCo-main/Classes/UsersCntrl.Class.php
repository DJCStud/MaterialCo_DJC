<?php
include_once __DIR__ . "/Users.Class.php";

class UsersCntrl extends Users {

    // Attributes
    private $name;
    private $email;
    private $age;
    private $password;
    private $passwordRep;

    // Constructor
    public function __construct($name = "", $email = "", $password = "", $passwordRep = "") {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->passwordRep = $passwordRep;
    }

    // Login
    public function loginUser($email, $password) {

        // Invalid Email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['Logmessage'] = "Invalid email format.";
            return false;
        }

        // Empty password
        if (empty($password)) {
            $_SESSION['Logmessage'] = "Password cannot be empty.";
            return false;
        }

        // Look up user
        $user = $this->getUsers($email);

        if (!$user) {
            $_SESSION['Logmessage'] = "Email not found.";
            return false;
        }

        // Wrong password
        if (!password_verify($password, $user['PASSWORD'])) {
            $_SESSION['Logmessage'] = "Incorrect password.";
            return false;
        }

        // SUCCESS
        session_regenerate_id();

        $_SESSION['USER_ID'] = $user['USER_ID'];
        $_SESSION['NAME'] = $user['NAME'];
        $_SESSION['EMAIL'] = $user['EMAIL'];

        return true;
    }

    // Register
    public function registerUser() {

        if (empty($this->name)) {
            $_SESSION['LogmessageReg'] = "Please enter a valid name.";
            return false;
        }

        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['LogmessageReg'] = "Invalid email format.";
            return false;
        }

        if (empty($this->password)) {
            $_SESSION['LogmessageReg'] = "Password cannot be empty.";
            return false;
        }

        if ($this->password !== $this->passwordRep) {
            $_SESSION['LogmessageReg'] = "Passwords do not match.";
            return false;
        }

        if ($this->getUsers($this->email)) {
            $_SESSION['LogmessageReg'] = "Email already exists.";
            return false;
        }

        $hash = password_hash($this->password, PASSWORD_DEFAULT);

        $this->insertUser($this->name, $this->email, $hash);

        $_SESSION['LogmessageRegSuccess'] = "Registration Successful!";
        return true;
    }
}
