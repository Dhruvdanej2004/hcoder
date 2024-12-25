function confirmDelete(event, fileName) {
    event.preventDefault(); // Prevent the form from submitting immediately

    // Show the modal
    var modal = document.getElementById('deleteModal');
    modal.style.display = 'flex';

    // Handle confirm delete
    var confirmButton = document.getElementById('confirmDeleteBtn');
    confirmButton.onclick = function() {
        var form = event.target.closest('form');
        var input = form.querySelector('input[name="delete"]');
        input.value = fileName; // Set the correct file name for deletion
        form.submit(); // Submit the form to delete the file
    };

    // Handle cancel
    var cancelButton = document.getElementById('cancelDeleteBtn');
    cancelButton.onclick = function() {
        modal.style.display = 'none'; // Hide the modal
    };
}

// Close modal if clicked outside
window.onclick = function(event) {
    var modal = document.getElementById('deleteModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}