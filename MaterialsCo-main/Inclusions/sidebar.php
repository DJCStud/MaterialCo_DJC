<!-- Sidebar Container -->
<div class="hidden bg-[C7CFBE] text-[1F2933] text-sm md:flex md:flex-col md:fixed md:w-1/6 md:h-screen shadow-md px-5 py-2 md:overflow-y-auto md:border-r md:border-[1F2933]">
    
    <!-- Logo -->
    <div class="my-3">
        <p class="flex items-center gap-3 p-2 rounded-md">
            <span class="font-bold text-xl">MaterialsCo</span>
        </p>
    </div>
    
    <!-- Dashboard -->
    <div class="my-3">
        <div class="grid gap-1">
            <a href="./Homepage.php" class="flex items-center gap-3 p-2 rounded-md hover:bg-gray-200 transition-all duration-200">
                <i class="fa fa-dashboard"></i>
                <span class="font-medium">Dashboard</span>
            </a>

            <a href="./organization.php" class="flex items-center gap-3 p-2 rounded-md hover:bg-gray-200 transition-all duration-200">
                <i class="fa fa-sitemap" aria-hidden="true"></i>
                <span class="font-medium">Organization</span>
            </a>
        </div>
    </div>

    <!-- Inventory Management -->
    <div class="my-3">
        <h1 class="my-3 font-bold">Inventory Management</h1>
        <div class="grid gap-1">

            <a href="./Inventory.php" class="flex items-center gap-3 p-2 rounded-md hover:bg-gray-200 transition-all duration-200">
                <i class="fa fa-cubes"></i>
                <span>Material Inventory</span>
            </a>

            <a href="./Reservation.php" class="flex items-center gap-3 p-2 rounded-md hover:bg-gray-200 transition-all duration-200">
                <i class="fa fa-calendar"></i>
                <span>Material Reservation</span>
            </a>

            <a href="./Stocks.php" class="flex items-center gap-3 p-2 rounded-md hover:bg-gray-200 transition-all duration-200">
                <i class="fa fa-book"></i>
                <span>Stocks Log</span>
            </a>

        </div>
    </div>

    <!-- Other Services -->
    <div class="my-3">
        <h1 class="my-3 font-bold">Other Services</h1>
        <div class="grid gap-2">
            
            <a href="./Process/Logout.php" class="flex items-center gap-3 p-2 rounded-md hover:bg-red-100 hover:text-red-600 transition-all duration-200 cursor-pointer">
                <i class="fa fa-sign-out"></i>
                <span>Logout</span>
            </a>

        </div>
    </div>

</div>
