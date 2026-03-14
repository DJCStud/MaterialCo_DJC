<?php
session_start();
include __DIR__ . "/../../Classes/ItemsCntrl.Class.php";

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['addOrgBtn'])) {

    $name    = filter_var(trim($_POST['organizationName']), FILTER_SANITIZE_SPECIAL_CHARS);
    $address = filter_var(trim($_POST['organizationAddress']), FILTER_SANITIZE_SPECIAL_CHARS);
    $type    = filter_var(trim($_POST['organizationType']), FILTER_SANITIZE_SPECIAL_CHARS);
    $userID  = $_SESSION['USER_ID'];

    $items = new ItemsCntrl();

    if (!$items->addOrganization($userID, $name, $address, $type)) {
        $_SESSION['OrganizationMessage'] = "ERROR CREATING ORGANIZATION!";
    } else {
        $_SESSION['OrganizationMessageSuccess'] = "ORGANIZATION CREATED SUCCESSFULLY!";
    }

    header("Location: ../../organization.php");
    exit();

} else {
    header("Location: ../../organization.php?error=InvalidAccess");
    exit();
}