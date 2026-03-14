<script>
document.addEventListener('DOMContentLoaded', function () {

    const materialDropdown = document.getElementById('materialDropdown');
    const additionalFields = document.getElementById('additionalFields');
    const availableQtySpan = document.getElementById('availableQty');
    const quantityInput = document.getElementById('quantityInput');

    // ⛔ Exit if reservation modal does NOT exist on this page
    if (!materialDropdown || !additionalFields || !availableQtySpan || !quantityInput) {
        return;
    }

    materialDropdown.addEventListener('change', function () {
        const selectedOption = materialDropdown.selectedOptions[0];

        if (selectedOption && selectedOption.value) {
            additionalFields.classList.remove('hidden');

            const availableQty = parseInt(selectedOption.dataset.available) || 0;
            availableQtySpan.textContent = availableQty;

            quantityInput.value = availableQty;
            quantityInput.min = 1;
            quantityInput.max = availableQty;
        } else {
            additionalFields.classList.add('hidden');

            quantityInput.value = '';
            quantityInput.min = 0;
            quantityInput.max = 0;
            availableQtySpan.textContent = 0;
        }
    });

});
</script>
