<?php
session_start();

include __DIR__ . "/../../Classes/ItemsCntrl.Class.php";

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['addBtn'])) {

    // Sanitize input
    $name = filter_var(trim($_POST['materialName']), FILTER_SANITIZE_SPECIAL_CHARS);
    $quantity = filter_var(trim($_POST['materialQuantity']), FILTER_SANITIZE_NUMBER_INT);
    $price = filter_var(trim($_POST['materialPrice']), FILTER_SANITIZE_NUMBER_INT);
    $description = trim($_POST['description']);
    $USERID = $_SESSION['USER_ID'];

    // Debug check — now placed after $USERID actually exists
    if (empty($USERID)) {
        die("DEBUG: USER_ID is empty at insert time. Session dump: " . print_r($_SESSION, true));
    }

    // Controller
    $items = new ItemsCntrl($USERID, $name, $quantity, $price, $description);

    // Execute registration
    if(!$items->addItem()){
        $_SESSION['InventoryMessage'] = "ERROR INSERTING MATERIALS!";
    }
    else{
        $_SESSION['InventoryMessageSuccess'] = "INSERT SUCCESSFUL!";
    }

    // Redirect after processing
    header("Location: ../../Inventory.php");
    exit();

} else {
    header("Location: ../Inventory.php?error=FailedtoAddItem");
    exit();
}
