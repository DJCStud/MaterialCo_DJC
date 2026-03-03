<?php
session_start();

include __DIR__ . "/../../Classes/ItemsCntrl.Class.php";

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['deleteBtn'])) {

    // Sanitize input
    $ID = filter_var(trim($_POST['materialId']), FILTER_SANITIZE_NUMBER_INT);

    // Controller
    $items = new ItemsCntrl($ID);

    // Execute registration
    if(!$items->deleteItem()){
        $_SESSION['InventoryMessage'] = "ERROR DELETING MATERIALS!";
    }
    else{
         $_SESSION['InventoryMessageSuccess'] = " SUCCESSFULLY DELETED!";
    }

   header("Location: ../../Inventory.php");
    exit();

} else {
    header("Location: ../Inventory.php?error=FailedtoAddItem");
    exit();
}