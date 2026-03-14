<?php
    // Method to Display Error Messages
    function flashError($Key, $Class = 'text-red-500') {
        if (isset($_SESSION[$Key])) {
            echo '<h1 class="text-lg mb-2 ' . htmlspecialchars($Class) . '">' . htmlspecialchars($_SESSION[$Key]) . '</h1>';
            unset($_SESSION[$Key]);
        }
    }

    // Method to Display Success Messages
    function flashSuccess($Key, $Class = 'text-green-500') {
        if (isset($_SESSION[$Key])) {
            echo '<h1 class="text-lg mb-2 ' . htmlspecialchars($Class) . '">' . htmlspecialchars($_SESSION[$Key]) . '</h1>';
            unset($_SESSION[$Key]);
        }
    }

    //Method for Messages in Home Frames
    function renderFlashBox() {
        if (
            isset($_SESSION['InventoryMessage']) || isset($_SESSION['InventoryMessageSuccess']) ||
            isset($_SESSION['ReservationMessage']) || isset($_SESSION['ReservationMessageSuccess']) ||
            isset($_SESSION['DistributionMessage']) || isset($_SESSION['DistributionMessageSuccess'])
        ) {
            echo '<div id="flashMessageBox" class="w-md p-3 text-md bg-[C7CFBE] shadow-md font-bold flex justify-center items-center place-self-center text-center">';
            flashError('InventoryMessage');
            flashSuccess('InventoryMessageSuccess');
            flashError('ReservationMessage');
            flashSuccess('ReservationMessageSuccess');
            flashError('DistributionMessage');
            flashSuccess('DistributionMessageSuccess');
            echo '</div>
            <script>
                setTimeout(function() {
                    const msg = document.getElementById("flashMessageBox");
                    if (msg) {
                        msg.style.transition = "opacity 0.5s ease";
                        msg.style.opacity = 0;
                        setTimeout(() => msg.style.display = "none", 500);
                    }
                }, 3000);
            </script>';
        }
    }

?>
