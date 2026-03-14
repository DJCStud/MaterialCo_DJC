<?php
session_start();
include __DIR__ . "/../../Classes/ItemsCntrl.Class.php";

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['deleteOrgBtn'])) {

    $organizationID = filter_var(trim($_POST['organizationId']), FILTER_SANITIZE_NUMBER_INT);
    $userID = $_SESSION['USER_ID'];

    $items = new ItemsCntrl();

    if (!$items->deleteOrganization($organizationID, $userID)) {
        $_SESSION['OrganizationMessage'] = "ERROR DELETING ORGANIZATION!";
    } else {
        $_SESSION['OrganizationMessageSuccess'] = "ORGANIZATION DELETED SUCCESSFULLY!";
    }

    header("Location: ../../organization.php");
    exit();

} else {
    header("Location: ../../organization.php?error=InvalidAccess");
    exit();
}