<?php
session_start();
if(!isset($_SESSION['USER_ID'])){
    header('Location: ./index.php');
    exit();
}
?>
<body>
<?php
    include __DIR__ . "/Inclusions/Head.php";
    include __DIR__ . "/Inclusions/Methods.php";
    include __DIR__ . "/Classes/Dbh.Class.php";
    include __DIR__ . "/Classes/ItemsView.Class.php";
    include __DIR__ . '/Scripts/mainScript.php';

    $itemsView = new ItemsView();
    $USER_ID = $_SESSION['USER_ID'];
    $allOrganizations = $itemsView->viewAllOrganizations();
    $myOrganizations  = $itemsView->viewMyOrganizations($USER_ID);

    // Add flash keys for organizations to Methods.php renderFlashBox or handle inline
    if (isset($_SESSION['OrganizationMessage'])) {
        $flashError = $_SESSION['OrganizationMessage'];
        unset($_SESSION['OrganizationMessage']);
    }
    if (isset($_SESSION['OrganizationMessageSuccess'])) {
        $flashSuccess = $_SESSION['OrganizationMessageSuccess'];
        unset($_SESSION['OrganizationMessageSuccess']);
    }
?>

<div id="BodyDiv" class="w-full h-full flex bg-[D0DACA] text-[1F2933]">

    <div class="w-1/5">
        <?php include __DIR__ . "/Inclusions/sidebar.php"; ?>
    </div>

    <div class="w-full flex flex-col gap-5">

        <div><?php include __DIR__ . "/Inclusions/navbar.php"; ?></div>

        <?php renderFlashBox(); ?>

        <?php if (!empty($flashError)): ?>
            <div class="mx-5 p-3 bg-red-100 text-red-600 font-semibold rounded"><?= htmlspecialchars($flashError) ?></div>
        <?php endif; ?>
        <?php if (!empty($flashSuccess)): ?>
            <div class="mx-5 p-3 bg-green-100 text-green-600 font-semibold rounded"><?= htmlspecialchars($flashSuccess) ?></div>
        <?php endif; ?>

        <div class="grid gap-1 w-full h-20 my-2 px-5 flex items-center">
            <h1 class="text-3xl font-bold">Organizations</h1>
            <p class="font-medium text-sm">Manage your organizations or browse all available ones.</p>
        </div>

        <!-- Action Buttons -->
        <div class="flex px-5 justify-start gap-5">

            <!-- Create Organization Button -->
            <div id="showCreateOrganization" class="w-1/5 h-15 bg-[D0DACA] border border-[1F2933] shadow-sm flex">
                <div class="w-full flex justify-center items-center gap-3 p-4 hover:cursor-pointer">
                    <i class="fa fa-plus-circle text-xl"></i>
                    <p class="text-sm">Create an Organization</p>
                </div>
            </div>

            <!-- Create Organization Modal -->
            <dialog id="organizationCreationEntry" class="fixed top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-[36rem] max-w-full p-6 rounded-lg shadow-xl bg-[C7CFBE] text-[1F2933] backdrop:bg-black/40">
                <form method="POST" action="./Process/OrganizationProcess/addOrg.php" class="space-y-6">
                    <h1 class="text-2xl font-bold">Create Organization</h1>

                    <div class="space-y-2">
                        <label class="block text-md font-medium">Name</label>
                        <input required type="text" name="organizationName" placeholder="My Organization"
                            class="w-full p-3 text-md border border-gray-300 rounded-md" />
                    </div>

                    <div class="space-y-2">
                        <label class="block text-md font-medium">Address</label>
                        <input required type="text" name="organizationAddress" placeholder="123 Main St"
                            class="w-full p-3 text-md border border-gray-300 rounded-md" />
                    </div>

                    <div class="space-y-2">
                        <label class="block text-md font-medium">Type (School, Office, etc.)</label>
                        <input required type="text" name="organizationType" placeholder="School"
                            class="w-full p-3 text-md border border-gray-300 rounded-md" />
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" onclick="document.getElementById('organizationCreationEntry').close()"
                            class="px-4 py-2 cursor-pointer bg-white font-semibold rounded hover:bg-gray-300">Cancel</button>
                        <button type="submit" name="addOrgBtn"
                            class="px-4 py-2 cursor-pointer bg-blue-500 text-white font-semibold rounded hover:bg-blue-600">Create</button>
                    </div>
                </form>
            </dialog>

        </div>

        <!-- Table Toggle Buttons -->
        <div class="flex gap-3 text-sm px-5 mt-5 -mb-5">
            <button id="showorganizationTable" class="cursor-pointer text-red-500 font-semibold hover:text-red-600">All Organizations</button>
            <button id="showmyorganizationTable" class="cursor-pointer hover:text-red-500">My Organizations</button>
        </div>

        <!-- All Organizations Table -->
        <div id="organizationTablecon" class="h-full w-full px-5">
            <table id="organizationTable" class="table-auto bg-[C7CFBE] border-separate border w-full">
                <thead>
                    <tr class="text-md">
                        <th class="text-[1F2933] p-2">ID</th>
                        <th class="text-[1F2933] p-2">NAME</th>
                        <th class="text-[1F2933] p-2">ADDRESS</th>
                        <th class="text-[1F2933] p-2">TYPE</th>
                        <th class="text-[1F2933] p-2">CREATED BY</th>
                        <th class="text-[1F2933] p-2">DATE CREATED</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($allOrganizations)): ?>
                        <?php foreach ($allOrganizations as $row): ?>
                            <tr class="odd:bg-[C7CFBE] even:bg-[bdc3b2] text-[1F2933] h-10 text-sm">
                                <td class="px-3 text-end"><?= htmlspecialchars($row['ORGANIZATION_ID']) ?></td>
                                <td class="px-3"><?= htmlspecialchars($row['NAME']) ?></td>
                                <td class="px-3"><?= htmlspecialchars($row['ADDRESS']) ?></td>
                                <td class="px-3"><?= htmlspecialchars($row['TYPE']) ?></td>
                                <td class="px-3"><?= htmlspecialchars($row['CREATOR_NAME']) ?></td>
                                <td class="px-3"><?= htmlspecialchars(date('F j, Y', strtotime($row['CREATED_AT']))) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center py-5">No organizations found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- My Organizations Table -->
        <div id="myorganizationTablecon" class="hidden h-full w-full px-5">
            <table id="myorganizationTable" class="table-auto bg-[C7CFBE] border-separate border w-full">
                <thead>
                    <tr class="text-md">
                        <th class="text-[1F2933] p-2">ID</th>
                        <th class="text-[1F2933] p-2">NAME</th>
                        <th class="text-[1F2933] p-2">ADDRESS</th>
                        <th class="text-[1F2933] p-2">TYPE</th>
                        <th class="text-[1F2933] p-2">DATE CREATED</th>
                        <th class="text-[1F2933] p-2">ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($myOrganizations)): ?>
                        <?php foreach ($myOrganizations as $row): ?>
                            <tr class="odd:bg-[C7CFBE] even:bg-[bdc3b2] text-[1F2933] h-10 text-sm">
                                <td class="px-3 text-end"><?= htmlspecialchars($row['ORGANIZATION_ID']) ?></td>
                                <td class="px-3"><?= htmlspecialchars($row['NAME']) ?></td>
                                <td class="px-3"><?= htmlspecialchars($row['ADDRESS']) ?></td>
                                <td class="px-3"><?= htmlspecialchars($row['TYPE']) ?></td>
                                <td class="px-3"><?= htmlspecialchars(date('F j, Y', strtotime($row['CREATED_AT']))) ?></td>
                                <td class="px-3">
                                    <div class="flex justify-center items-center gap-5 text-lg">
                                        <div class="cursor-pointer" onclick="document.getElementById('deleteOrgModal<?= $row['ORGANIZATION_ID'] ?>').showModal()">
                                            <i class="fa fa-trash text-lg text-[1F2933]"></i>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <!-- Delete Modal -->
                            <dialog id="deleteOrgModal<?= $row['ORGANIZATION_ID'] ?>"
                                class="fixed w-sm p-5 top-1/3 left-1/2 transform -translate-x-1/2 -translate-y-1/2 rounded-md bg-[C7CFBE] text-[1F2933] shadow-md backdrop:bg-black/40">
                                <form method="POST" action="./Process/OrganizationProcess/deleteOrg.php" class="space-y-6">
                                    <div class="text-xl font-semibold">Delete Organization</div>
                                    <p>Are you sure you want to delete <strong><?= htmlspecialchars($row['NAME']) ?></strong>? This action cannot be undone.</p>
                                    <input type="hidden" name="organizationId" value="<?= htmlspecialchars($row['ORGANIZATION_ID']) ?>">
                                    <div class="flex justify-end gap-3 pt-4">
                                        <button type="button"
                                            onclick="document.getElementById('deleteOrgModal<?= $row['ORGANIZATION_ID'] ?>').close()"
                                            class="px-4 py-2 cursor-pointer bg-gray-300 font-semibold rounded hover:bg-gray-400">Cancel</button>
                                        <button type="submit" name="deleteOrgBtn"
                                            class="px-4 py-2 cursor-pointer bg-red-500 text-white font-semibold rounded hover:bg-red-600">Confirm</button>
                                    </div>
                                </form>
                            </dialog>

                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="6" class="text-center py-5">You have not created any organizations yet.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    </div>
</div>

</body>