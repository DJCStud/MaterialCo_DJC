<?php
session_start();

include __DIR__ . "/../../Classes/ItemsCntrl.Class.php";

if ($_SERVER['REQUEST_METHOD'] === "POST" && isset($_POST['updateBtn'])) {

    // Sanitize input
    $name = filter_var(trim($_POST['materialName']), FILTER_SANITIZE_SPECIAL_CHARS);
    $quantity = filter_var(trim($_POST['materialQuantity']), FILTER_SANITIZE_NUMBER_INT);
    $price = filter_var(trim($_POST['materialPrice']), FILTER_SANITIZE_NUMBER_INT);
    $model = trim($_POST['materialModel']);
    $ID = filter_var(trim($_POST['materialId']), FILTER_SANITIZE_NUMBER_INT);

    // Controller
    $items = new ItemsCntrl($ID, $name, $quantity, $price, $model);

    // Execute registration
    if(!$items->updateItem()){
        $_SESSION['InventoryMessage'] = "ERROR UPDATING MATERIALS!";
    }
    else{
         $_SESSION['InventoryMessageSuccess'] = "UPDATE SUCCESSFUL!";
    }

   header("Location: ../../Inventory.php?name=$name&&quantity=$quantity&&price=$price&&size=$size&&model=$model&&id=$ID");
    exit();

} else {
    header("Location: ../Inventory.php?error=FailedtoAddItem");
    exit();
}