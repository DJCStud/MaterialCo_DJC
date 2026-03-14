<div class="w-full py-3 px-5 shadow-md bg-[C7CFBE] text-[1F2933] flex justify-end items-center sticky top-0 z-10">
    <button id="sidebarToggle" class="md:hidden mr-auto cursor-pointer text-white px-3 py-1 focus:outline-none">
        <i class="fa fa-bars"></i>
    </button>
    <div class="flex">
        <div class="flex justify-center items-center gap-2"><i class="fa fa-user"></i><h1 class="ml-1"><?php echo $_SESSION['NAME'] ?></h1></div>
    </div>
</div>

<script>
    //Functionality for Panels of Signup and Signin 
    const showNotif = document.getElementById('showNotif');
    const notifPanel = document.getElementById('notifPanel');

    if (showNotif && notifPanel) {
        showNotif.addEventListener('click', () => {
            notifPanel.classList.toggle('hidden');
        });
    }

    // Sidebar expand/collapse on mobile
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('MainSidebar');

    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', () => {
            // Toggle width classes
            sidebar.classList.toggle('w-14');
            sidebar.classList.toggle('w-64');

            // Toggle text visibility for spans that are hidden on mobile
            const hiddenSpans = sidebar.querySelectorAll('span.hidden');
            if (sidebar.classList.contains('w-64')) {
                hiddenSpans.forEach(s => s.classList.remove('hidden'));
            } else {
                hiddenSpans.forEach(s => s.classList.add('hidden'));
            }
        });
    }

</script>