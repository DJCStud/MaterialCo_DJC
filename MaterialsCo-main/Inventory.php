<?php
    session_start(); 

    if(!isset($_SESSION['USER_ID'])){
        header('Location: ./index.php');
        exit();
    }   

?>
<body>
    <!--Important import files-->
    <?php
        include __DIR__ . "/Inclusions/Head.php";
        //include __DIR__ . "/Inclusions/navbar.php";
        include __DIR__ . "/Inclusions/Methods.php";
        include __DIR__ . "/Classes/Dbh.Class.php";
        include __DIR__ . "/Classes/ItemsView.Class.php";

        // Scripts
        include __DIR__ . '/Scripts/mainScript.php';

        $itemsView = new ItemsView();
        $USER_ID = $_SESSION['USER_ID'];
        $materials = $itemsView->viewInventory($USER_ID);
        $lowstockCount = $itemsView->viewLowStockCount($USER_ID);
        $outofstockCount = $itemsView->viewOutOfStockCount($USER_ID)
    ?>

    <!--Main Body for Inventory Page, 2 Columns-->
    <div id="BodyDiv" class="w-full h-full flex bg-[D0DACA] text-[1F2933]">

        <div class="w-1/5">
            <!--Sidebar from import-->
            <?php
                include __DIR__ . "/Inclusions/sidebar.php";
            ?>
        </div>

        <!--Parts Distribution-->
        <div class="w-full flex flex-col gap-5">

            <!--NavBar-->
            <div>
                <?php include __DIR__ . "/Inclusions/navbar.php";?>
            </div>

            <!--Message Panel-->
            <?php renderFlashBox(); ?>

            <div class="w-full h-20 my-2 px-5 text-3xl font-bold flex items-center">
                <h1>Inventory</h1>
            </div>

            <!--Indicators-->
            <div class="flex px-5 justify-start gap-5">

                
                <!--Inventory Status-->
                <div class="w-1/5 h-15 bg-[C7CFBE] border border-[1F2933] text-[1F2933] shadow-sm flex">
                    <div class="w-70 flex justify-center items-center">
                        <p class="text-sm">Low Stock <br> Products</p>
                    </div>
                    <div class="flex justify-center items-center w-1/2">
                        <p class="text-md font-bold"><?php echo $lowstockCount; ?></p>
                    </div>
                </div>

                <!--Inventory Status-->
                <div class="w-1/5 h-15 bg-[C7CFBE] border border-[1F2933] text-[1F2933] shadow-sm flex">
                    <div class="w-70 flex justify-center items-center">
                        <p class="text-sm">Out of Stock <br> Products</p>
                    </div>
                    <div class="flex justify-center items-center w-1/2">
                        <p class="text-md font-bold"><?php echo $outofstockCount; ?></p>
                    </div>
                </div>

                <!--Inventory Status-->
                <div id="showInventory" class="w-1/5 h-15 bg-[C7CFBE] border border-[1F2933] text-[1F2933] shadow-sm flex">
                    <div class="w-70 flex justify-center items-center gap-3 hover:cursor-pointer">
                        <i class="fa fa-plus-circle text-xl"></i>
                        <p class="text-sm">Record an Item</p>
                    </div>
                </div>

                <!-- Inventory Entry Modal -->
                <dialog id="inventoryEntry" class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[36rem] max-w-full p-6 rounded-lg shadow-xl bg-[C7CFBE] text-[1F2933] backdrop:bg-black/40 open:animate-fadeIn">
                    <form method="POST" action="./Process/InventoryProcess/addItem.php" class="space-y-6">

                        <!-- Modal Title -->
                        <h1 class="text-2xl font-bold">INVENTORY FORM</h1>

                        <!-- Material Name -->
                        <div class="space-y-2">
                        <label class="block text-md font-medium ">Material</label>
                        <input required type="text" class="w-full p-3 text-md border border-gray-300 rounded-md" name="materialName"/>
                        </div>

                        <!-- Quantity -->
                        <div class="space-y-2">
                        <label class="block text-md font-medium ">Quantity</label>
                        <input required type="number" value="1" min="1" class="w-full p-3 text-md border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" 
                         name="materialQuantity"/>
                        </div>

                        <!-- Price -->
                        <div class="space-y-2">
                        <label class="block text-md font-medium ">Price</label>
                        <input required type="number" value="100" class="w-full p-3 text-md border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400"
                         name="materialPrice" />
                        </div>

                        <!-- Model -->
                        <div class="space-y-2">
                        <label class="block text-md font-medium">Model (Optional)</label>
                        <input required type="text" value="N/A" class="w-full p-3 text-md border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" 
                         name="materialModel"/>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end gap-3 pt-4">
                        <button type="button" onclick="document.getElementById('inventoryEntry').close()" class="px-4 py-2 cursor-pointer bg-white font-semibold rounded hover:bg-gray-300">Cancel</button>
                        <button type="submit" name="addBtn" class="px-4 py-2 cursor-pointer bg-blue-500 text-white font-semibold rounded hover:bg-blue-600">Save</button>
                        </div>
                        
                    </form>
                </dialog>

            </div>

            <!--Inventory Table-->
            <div class="h-full w-full px-5">

                <table id="inventoryTable" class="table-auto bg-[C7CFBE] border-separate border h-fit max-h-full">
                    <thead>
                        <tr class="text-md">
                            <th class="w-md  text-[1F2933]">CODE</th>
                            <th class="w-md  text-[1F2933]">NAME</th>
                            <th class="w-md  text-[1F2933]">QUANTITY</th>
                            <th class="w-lg  text-[1F2933]">ITEM PRICE</th>
                            <th class="w-md  text-[1F2933]">REMARKS</th>
                            <th class="w-md  text-[1F2933]">DATE ADDED</th>
                            <th class="w-md  text-[1F2933]">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($materials as $row): ?>
                            <tr class="odd:bg-[C7CFBE] even:bg-[bdc3b2] text-[1F2933] border border-0 h-10 text-sm">
                                <td class="text-end"><?= htmlspecialchars($row['MATERIAL_ID']) ?></td>
                                <td><?= htmlspecialchars($row['MATERIAL_NAME']) ?></td>
                                <td class="text-end"><?= htmlspecialchars($row['QUANTITY']) ?></td>
                                <td class="text-end">P<?= htmlspecialchars($row['PRICE']) ?></td>
                                <td><?= htmlspecialchars($row['MODEL']) ?></td>
                                <td><?= htmlspecialchars(date('F j, Y', strtotime($row['DATE_ADDED']))) ?></td>
                                <td>
                                    <div class="flex justify-center items-center gap-5 text-lg">
                                        <div class="cursor-pointer text-[1F2933]" onclick="document.getElementById('updateModal<?= $row['MATERIAL_ID'] ?>').showModal()">
                                            <i class="fa fa-pencil text-lg"></i>
                                        </div>
                                        <div class="cursor-pointer" onclick="document.getElementById('deleteModal<?= $row['MATERIAL_ID'] ?>').showModal()">
                                            <i class="fa fa-trash text-lg text-[1F2933]"></i>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Delete Modal -->
                            <dialog id="deleteModal<?= $row['MATERIAL_ID'] ?>" 
                                class="fixed w-sm h-xl p-5 top-1/3 left-1/2 transform -translate-x-1/2 -translate-y-1/2 rounded-md bg-[C7CFBE] text-[1F2933] shadow-md backdrop:bg-black/40 open:animate-fadeIn">

                                <form method="POST" action="./Process/InventoryProcess/deleteItem.php" class="space-y-6">

                                    <!-- Modal Title -->
                                    <div class="text-xl font-semibold mb-5">
                                        Delete Item
                                    </div>

                                    <!-- Confirmation Text -->
                                    <p class="mb-5 ">
                                        Are you sure you want to delete <strong><?= htmlspecialchars($row['MATERIAL_NAME']) ?></strong> from inventory?
                                        This action cannot be undone.
                                    </p>

                                    <!-- Hidden Material ID -->
                                    <input type="hidden" name="materialId" value="<?= htmlspecialchars($row['MATERIAL_ID'])?>">

                                    <!-- Action Buttons -->
                                    <div class="flex justify-end gap-3 pt-4">
                                        <button type="button" 
                                                onclick="document.getElementById('deleteModal<?= $row['MATERIAL_ID'] ?>').close()" 
                                                class="px-4 py-2 cursor-pointer bg-gray-300  font-semibold rounded hover:bg-gray-400">
                                            Cancel
                                        </button>
                                        <button type="submit" name="deleteBtn" 
                                                class="px-4 py-2 cursor-pointer bg-red-500 text-white font-semibold rounded hover:bg-red-600">
                                            Confirm
                                        </button>
                                    </div>

                                </form>
                            </dialog>


                            <!-- Update Modal -->
                            <dialog id="updateModal<?= $row['MATERIAL_ID'] ?>" 
                                class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[36rem] max-w-full p-6 rounded-lg shadow-xl bg-[C7CFBE] text-[1F2933] backdrop:bg-black/40 open:animate-fadeIn">

                                <form method="POST" action="./Process/InventoryProcess/updateItem.php" class="space-y-6">
                                    
                                    <!-- Modal Title -->
                                    <h1 class="text-2xl font-boldmb-1">Update Information</h1>

                                    <!-- Material Name -->
                                    <div class="space-y-2">
                                        <label class="block text-md font-medium ">Material</label>
                                        <input type="text" name="materialName" value="<?= htmlspecialchars($row['MATERIAL_NAME'])?>" 
                                            class="w-full p-3 text-md border border-gray-300 rounded-md" />
                                    </div>

                                    <!-- Quantity -->
                                    <div class="space-y-2">
                                        <label class="block text-md font-medium ">Quantity</label>
                                        <input type="number" name="materialQuantity" min="0" value="<?= $row['QUANTITY'] ?>" 
                                            class="w-full p-3 text-md border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" required />
                                    </div>

                                    <!-- Price -->
                                    <div class="space-y-2">
                                        <label class="block text-md font-medium t">Price</label>
                                        <input type="number" name="materialPrice" value="<?= $row['PRICE'] ?>" 
                                            class="w-full p-3 text-md border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" />
                                    </div>

                                    <!-- Model -->
                                    <div class="space-y-2">
                                        <label class="block text-md font-medium ">Model</label>
                                        <input type="text" name="materialModel" value="<?= htmlspecialchars($row['MODEL']) ?>" 
                                            class="w-full p-3 text-md border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-400" />
                                    </div>

                                    <!-- Hidden Material ID -->
                                    <input type="hidden" name="materialId" value="<?= htmlspecialchars($row['MATERIAL_ID'])?>">

                                    <!-- Action Buttons -->
                                    <div class="flex justify-end gap-3 pt-4">
                                        <button type="button" 
                                            onclick="document.getElementById('updateModal<?= $row['MATERIAL_ID'] ?>').close()" 
                                            class="px-4 py-2 cursor-pointer bg-gray-200 font-semibold rounded hover:bg-gray-300">
                                            Cancel
                                        </button>
                                        <button type="submit" name="updateBtn" 
                                            class="px-4 py-2 cursor-pointer bg-blue-500 text-white font-semibold rounded hover:bg-blue-600">
                                            Save
                                        </button>
                                    </div>

                                </form>
                            </dialog>

                        <?php endforeach; ?>
                    </tbody>
                </table>

            </div>

        </div>

    </div>

</body>
