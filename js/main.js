// Load files from local storage
document.addEventListener("DOMContentLoaded", loadFilesFromLocalStorage);

// Handle file upload
document.getElementById("uploadForm").addEventListener("submit", function(event) {
    event.preventDefault();

    const fileInput = document.getElementById("fileToUpload");
    if (fileInput.files.length > 0) {
        const file = fileInput.files[0];
        const fileName = file.name;

        // Store file information in local storage (simulating file upload)
        let files = JSON.parse(localStorage.getItem("files")) || [];
        files.push(fileName);
        localStorage.setItem("files", JSON.stringify(files));

        // Refresh file list
        loadFilesFromLocalStorage();
        fileInput.value = '';  // Reset file input
    }
});

// Load files from local storage and display them
function loadFilesFromLocalStorage() {
    const fileListElement = document.getElementById("fileList");
    fileListElement.innerHTML = '';  // Clear current list

    let files = JSON.parse(localStorage.getItem("files")) || [];
    files.forEach(fileName => {
        const li = document.createElement("li");
        li.innerHTML = `<a href="#" onclick="downloadFile('${fileName}')">${fileName}</a>
                        <button class="delete-btn" onclick="confirmDelete('${fileName}')">Delete</button>`;
        fileListElement.appendChild(li);
    });
}

// Simulate downloading the file
function downloadFile(fileName) {
    alert("Simulating download for: " + fileName);
}

// Show the confirmation modal before deleting a file
function confirmDelete(fileName) {
    const modal = document.getElementById('deleteModal');
    modal.style.display = 'flex';

    // Confirm delete action
    document.getElementById('confirmDeleteBtn').onclick = function() {
        deleteFile(fileName);
        modal.style.display = 'none';
    };

    // Cancel delete action
    document.getElementById('cancelDeleteBtn').onclick = function() {
        modal.style.display = 'none';
    };
}

// Delete a file from local storage
function deleteFile(fileName) {
    let files = JSON.parse(localStorage.getItem("files")) || [];
    files = files.filter(file => file !== fileName);  // Remove the file
    localStorage.setItem("files", JSON.stringify(files));  // Update local storage

    // Refresh the file list
    loadFilesFromLocalStorage();
}

// Close modal if clicked outside
window.onclick = function(event) {
    var modal = document.getElementById('deleteModal');
    if (event.target == modal) {
        modal.style.display = 'none';
    }
}