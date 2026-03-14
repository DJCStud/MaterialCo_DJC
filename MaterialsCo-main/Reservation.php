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
        include __DIR__ . '/Scripts/dropdownScript.php';

        $itemsView = new ItemsView();
        $USER_ID = $_SESSION['USER_ID'];
        $materials = $itemsView->viewReservations($USER_ID);
        $inventoryMaterials = $itemsView->viewInventory($USER_ID);
        $pendingCount = $itemsView->viewPendingCount($USER_ID);
        $reservedCount = $itemsView->viewReservedCount($USER_ID);
    ?>

    <!--Main Body for Reservation Page, 2 Columns-->
    <div id="BodyDiv" class="w-full h-full flex bg-[D0DACA] text-[1F2933]">

        <div class="w-1/5">
            <!--Sidebar from import-->
            <?php
                include __DIR__ . "/Inclusions/sidebar.php";
            ?>
        </div>

        <!--Parts Reservation-->
        <div class="w-full flex flex-col gap-5">

            <!--NavBar-->
            <div>
                <?php include __DIR__ . "/Inclusions/navbar.php";?>
            </div>

            <!--Message Panel-->
            <?php renderFlashBox(); ?>

            <div class="w-full h-20 my-2 px-5 text-3xl font-bold flex items-center">
                <h1>Reservations</h1>
            </div>

            <!--Indicators-->
            <div class="flex justify-start gap-5 px-5">

                
                <!--Reservation Status-->
                <div class="w-1/5 h-15 bg-[C7CFBE] border border-[1F2933] text-[1F2933] shadow-sm flex">
                    <div class="w-70 flex justify-center items-center">
                        <p class="text-sm">Pendings</p>
                    </div>
                    <div class="flex justify-center items-center w-1/2">
                        <p class="text-md font-bold"><?php echo $pendingCount ?></p>
                    </div>
                </div>

                <!--Reservation Status-->
                <div class="w-1/5 h-15 bg-[C7CFBE] border border-[1F2933] text-[1F2933] shadow-sm flex">
                    <div class="w-70 flex justify-center items-center">
                        <p class="text-sm">Reserved</p>
                    </div>
                    <div class="flex justify-center items-center w-1/2">
                        <p class="text-md font-bold"><?php echo $reservedCount ?></p>
                    </div>
                </div>

                <!-- Reservation Box -->
                <div id="showReservation" 
                    class="w-1/5 h-15 bg-[C7CFBE] border border-[1F2933] text-[1F2933] shadow-sm flex justify-center items-center cursor-pointer">
                    <div class="flex justify-center items-center gap-3">
                        <i class="fa fa-plus-circle text-xl"></i>
                        <p class="text-sm text-center">Make a <br>Reservation</p>
                    </div>
                </div>

                <!-- Reservation Entry Modal -->
                <dialog id="reservationModal" 
                        class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[36rem] max-w-full p-6 rounded-lg shadow-xl bg-[C7CFBE] text-[1F2933] backdrop:bg-black/40 open:animate-fadeIn">

                    <form method="POST" action="./Process/ReservationProcess/addItem.php" class="space-y-6">

                        <!-- Modal Title -->
                        <h1 class="text-2xl font-bold">RESERVATION FORM</h1>

                        <!-- Material Dropdown -->
                        <div class="space-y-2">
                            <label class="block text-md font-medium">Material</label>
                            <select id="materialDropdown" name="material_id" class="w-full p-3 text-md border border-gray-300 rounded-md" required>
                                <option disabled selected value="">Select a Material</option>
                                <?php foreach ($inventoryMaterials as $material): ?>
                                    <?php if ($material['QUANTITY'] > 0): ?>
                                        <option value="<?= htmlspecialchars($material['MATERIAL_ID']) ?>" 
                                                data-available="<?= htmlspecialchars($material['QUANTITY']) ?>">
                                            <?= htmlspecialchars($material['MATERIAL_ID']) ?> - <?= htmlspecialchars($material['MATERIAL_NAME']) ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Additional Fields (Initially Disabled) -->
                        <div id="additionalFields" class="space-y-2 mt-4 hidden">
                        <!-- Quantity -->
                        <div class="space-y-2">
                            <label class="block text-lg font-medium ">
                                Quantity (Available: <span id="availableQty">0</span>)
                            </label>
                            <input 
                                type="number" 
                                name="quantity" 
                                min="0" 
                                class="w-full p-3 text-lg border border-gray-300 rounded-md" 
                                required 
                                id="quantityInput"
                            />
                        </div>

                            <!-- Claim Date -->
                            <div class="space-y-2">
                                <label class="block text-md font-medium t">Date to Claim</label>
                                <input type="date" name="claimDate" class="w-full p-3 text-md border border-gray-300 rounded-md" required />
                            </div>

                            <!-- Requestor -->
                            <div class="space-y-2">
                                <label class="block text-md font-semibold ">Requestor</label>
                                <input type="text" name="requestor" class="w-full p-3 text-md border border-gray-300 rounded-md" required />
                            </div>

                            <!-- Remarks -->
                            <div class="space-y-2">
                                <label class="block text-md font-semibold ">Remarks (optional)</label>
                                <textarea name="remarks" rows="3" class="w-full p-3 text-md border border-gray-300 rounded-md resize-none">N/A</textarea>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex justify-end gap-3 pt-4">
                            <button type="button" 
                                    onclick="document.getElementById('reservationModal').close()" 
                                    class="px-4 py-2 cursor-pointer bg-gray-200  font-semibold rounded hover:bg-gray-300">
                                Cancel
                            </button>
                            <button type="submit" name="reserveBtn" 
                                    class="px-4 py-2 cursor-pointer bg-blue-500 text-white font-semibold rounded hover:bg-blue-600">
                                Save
                            </button>
                        </div>

                    </form>
                </dialog>

            </div>

            <!--Reservation Table-->
            <div class="h-full w-full px-5">

                <table id="inventoryTable" class="table-auto border-separate border bg-[C7CFBE] h-fit max-h-full">
                    <thead>
                        <tr class="text-md">
                            <th class="w-md text-[1F2933]">RESERVATION ID</th>
                            <th class="w-md text-[1F2933]">MATERIAL ID</th>
                            <th class="w-md text-[1F2933]">QUANTITY</th>
                            <th class="w-lg text-[1F2933]">REQUESTOR</th>
                            <th class="w-lg text-[1F2933]">PURPOSE</th>
                            <th class="w-md text-[1F2933]">RESERVATION DATE</th>
                            <th class="w-md text-[1F2933]">CLAIMING DATE</th>
                            <th class="w-md text-[1F2933]">STATUS</th>
                            <th class="w-md text-[1F2933]">ACTION</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($materials as $row): ?>
                            <tr class="odd:bg-[C7CFBE] even:bg-[bdc3b2] border-0 text-[1F2933] h-10 text-sm">
                                <td class="text-end"><?= htmlspecialchars($row['RESERVATION_ID']) ?></td>
                                <td><?= htmlspecialchars($row['MATERIAL_ID']) ?></td>
                                <td class="text-end"><?= htmlspecialchars($row['QUANTITY']) ?></td>
                                <td class="text-end"><?= htmlspecialchars($row['REQUESTOR']) ?></td>
                                <td><?= htmlspecialchars($row['PURPOSE']) ?></td>
                                <td><?= htmlspecialchars(date('F j, Y', strtotime($row['RESERVATION_DATE']))) ?></td>
                                <td><?= htmlspecialchars(date('F j, Y', strtotime($row['CLAIMING_DATE']))) ?></td>
                                <td><?= htmlspecialchars($row['STATUS']) ?></td>
                                <td>
                                    <div class="flex justify-center items-center text-lg gap-5">
                                        <div class="cursor-pointer" onclick="document.getElementById('updateModal<?= $row['RESERVATION_ID'] ?>').showModal()">
                                            <i class="fa fa-pencil text-lg text-[1F2933]"></i>
                                        </div>
                                        <div class="cursor-pointer" onclick="document.getElementById('deleteModal<?= $row['RESERVATION_ID'] ?>').showModal()">
                                            <i class="fa fa-trash text-lg text-[1F2933]"></i>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            
                            <!-- Delete Modal -->
                            <dialog id="deleteModal<?= $row['RESERVATION_ID'] ?>" 
                                class="fixed w-sm h-xl p-5 top-1/3 left-1/2 transform -translate-x-1/2 -translate-y-1/2 rounded-md shadow-md bg-[C7CFBE] text-[1F2933] backdrop:bg-black/40 open:animate-fadeIn">

                                <form method="POST" action="./Process/ReservationProcess/deleteItem.php" class="space-y-6">

                                    <!-- Modal Title -->
                                    <div class="text-xl  font-semibold mb-5">
                                        Delete Item
                                    </div>

                                    <!-- Confirmation Text -->
                                    <p class="mb-5">
                                        Are you sure you want to cancel this reservation?
                                        This action cannot be undone.
                                    </p>

                                    <!-- Hidden Reservation ID -->
                                    <input type="hidden" name="reservationId" value="<?= htmlspecialchars($row['RESERVATION_ID'])?>">

                                    <!-- Action Buttons -->
                                    <div class="flex justify-end gap-3 pt-4">
                                        <button type="button" 
                                                onclick="document.getElementById('deleteModal<?= $row['RESERVATION_ID'] ?>').close()" 
                                                class="px-4 py-2 cursor-pointer bg-gray-300 font-semibold rounded hover:bg-gray-400">
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
                            <dialog id="updateModal<?= $row['RESERVATION_ID'] ?>" 
                                class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[36rem] max-w-full p-6 rounded-lg shadow-xl bg-[C7CFBE] text-[1F2933] backdrop:bg-black/40 open:animate-fadeIn">

                                <form method="POST" action="./Process/ReservationProcess/updateStatus.php" class="space-y-6">
                                    <!-- Modal Title -->
                                    <h1 class="text-2xl font-bold ">Reservation Information</h1>

                                    <!-- Material ID -->
                                    <div class="space-y-2">
                                        <label class="block text-md font-medium ">Material</label>
                                        <input disabled type="text" name="materialId" value="<?= htmlspecialchars($row['MATERIAL_ID'])?>" 
                                            class="w-full p-3 text-md bg-white border border-gray-300 rounded-md" />
                                    </div>
                                    
                                    <!-- Quantity -->
                                    <div class="space-y-2">
                                        <label class="block text-md font-medium ">Quantity</label>
                                        <input disabled type="number" min="1" name="quantity" value="<?= $row['QUANTITY'] ?>" 
                                            class="w-full p-3 text-md border border-gray-300 rounded-md" required />
                                    </div>
                                    
                                    <!-- Requestor -->
                                    <div class="space-y-2">
                                        <label class="block text-md font-medium ">Requestor</label>
                                        <input disabled type="text" name="requestor" value="<?= $row['REQUESTOR'] ?>" 
                                            class="w-full p-3 text-md border border-gray-300 rounded-md" />
                                    </div>

                                    <!-- Claiming Date -->
                                    <div class="space-y-2">
                                        <label class="block text-md font-medium ">Claming Date</label>
                                        <input disabled type="date" name="claimingDate" value="<?= $row['CLAIMING_DATE'] ?>" 
                                            class="w-full p-3 text-md border border-gray-300 rounded-md" />
                                    </div>

                                    <!-- Remarks -->
                                    <div class="space-y-2">
                                        <label class="block text-md font-medium ">Remarks</label>
                                        <textarea disabled name="remarks" rows="3" class="w-full p-3 text-md border border-gray-300 rounded-md resize-none"><?= htmlspecialchars($row['PURPOSE']) ?></textarea>
                                    </div>
                                    
                                    <!-- Hidden Reservation ID -->
                                    <input type="hidden" name="reservationId" value="<?= htmlspecialchars($row['RESERVATION_ID'])?>">

                                    <!-- Action Buttons -->
                                    <div class="flex justify-end gap-3 pt-4">
                                        <button type="button" onclick="document.getElementById('updateModal<?= $row['RESERVATION_ID'] ?>').close()"
                                            class="px-4 py-2 cursor-pointer bg-gray-200 font-semibold rounded hover:bg-gray-300">Close</button>
                                        <button type="submit" name="cancelBtn"
                                            class="px-4 py-2 cursor-pointer bg-red-500 font-semibold rounded hover:bg-gray-300">Cancel</button>
                                        <button type="submit" name="reservedBtn" 
                                            class="px-4 py-2 cursor-pointer bg-yellow-500  font-semibold rounded hover:bg-blue-600">Reserved</button>
                                        <button type="submit" name="claimedBtn"
                                            class="px-4 py-2 cursor-pointer bg-green-500 font-semibold rounded hover:bg-blue-600">Claimed</button>
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
