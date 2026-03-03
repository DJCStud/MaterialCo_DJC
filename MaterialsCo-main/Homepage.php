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
        include __DIR__ . "/Classes/Dbh.Class.php";
        include __DIR__ . "/Classes/ItemsView.Class.php";

        $itemsView = new ItemsView();
        $USER_ID = $_SESSION['USER_ID'];
        $pendingCount = $itemsView->viewPendingCount($USER_ID);
        $outofstockCount = $itemsView->viewOutOfStockCount($USER_ID);
        $lowstockCount = $itemsView->viewLowStockCount($USER_ID);
    ?>

    <!--Main Body for Homepage Page-->
    <div id="BodyDiv" class="w-full min-h-screen flex bg-[D0DACA] text-[1F2933]">

        <!--Sidebar-->
        <div class="md:w-1/6 border-r bg-white shadow-sm">
            <?php include "./Inclusions/sidebar.php"; ?>
        </div>

        <!--Home Content-->
        <div class="flex-1 ml-14 md:ml-0">

            <!--NavBar-->
            <div class="mb-6">
                <?php include __DIR__ . "/Inclusions/navbar.php";?>
            </div>

            <!--User Header-->
            <div class="w-full flex flex-col items-center mb-10">
                <h1 class="text-2xl md:text-4xl font-bold tracking-wide">
                    <?php echo $_SESSION['NAME']; ?>
                </h1>
                <p class="text-md mt-2">
                    Welcome back! Here’s today’s activity summary.
                </p>
            </div>

            <!--Status Cards Grid-->
            <div class="flex w-full px-5 justify-center gap-5 place-self-center mb-10">

                <!--Pendings-->
                <div class="w-1/5 h-15 bg-[D0DACA] border border-[1F2933] shadow-sm flex items-center justify-between p-4">
                    <div class="flex items-center">
                        <p class="text-sm md:text-sm">Pendings</p>
                    </div>
                    <div class="flex items-center">
                        <p class="text-md md:text-md font-bold"><?php echo $pendingCount ?></p>
                    </div>
                </div>

                <!--Low Stock-->
                <div class="w-1/5 h-15 bg-[D0DACA] border border-[1F2933] shadow-sm flex items-center justify-between p-4">
                    <div class="flex items-center">
                        <p class="text-sm md:text-sm">Low Stock <br class="hidden md:block"> Products</p>
                    </div>
                    <div class="flex items-center">
                        <p class="text-md md:text-md font-bold"><?php echo $lowstockCount; ?></p>
                    </div>
                </div>

                <!--Out of stock-->
                <div class="w-1/5 h-15 bg-[D0DACA] border border-[1F2933] shadow-sm flex items-center justify-between p-4">
                    <div class="flex items-center">
                        <p class="text-sm md:text-sm">Out of Stock <br class="hidden md:block"> Products</p>
                    </div>
                    <div class="flex items-center">
                        <p class="text-md md:text-md font-bold"><?php echo $outofstockCount; ?></p>
                    </div>
                </div>

            </div>

        </div>
    </div>

    <!--Scripts-->
    <?php 
        include __DIR__ . '/Scripts/mainScript.php';
    ?>
</body>
