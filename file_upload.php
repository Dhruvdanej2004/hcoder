<?php
// file_upload.php

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['fileToUpload'])) {
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);

    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        echo "<div class='success'>The file " . htmlspecialchars(basename($_FILES["fileToUpload"]["name"])) . " has been uploaded.</div>";
    } 
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $file_to_delete = "uploads/" . basename($_POST['delete']);
    if (file_exists($file_to_delete)) {
        unlink($file_to_delete);
        echo "<div class='success'>File " . htmlspecialchars(basename($_POST['delete'])) . " has been deleted.</div>";
    } else {
        echo "<div class='error'>Error: File not found.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
<h2>Upload a File</h2>
    <form action="file_upload.php" method="post" enctype="multipart/form-data">
        Select file to upload:<br>
        <input type="file" name="fileToUpload" id="fileToUpload"><br>
        <input type="submit" value="Upload File" name="submit">
    </form>
    <h2>Manage Files</h2>
    <ul>
        <?php
        $target_dir = "uploads/";
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $files = scandir($target_dir);
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                echo "<li><a href='uploads/$file' download>$file</a>
                <form action='file_upload.php' method='post' style='display:inline;' class='delete-form'>
                    <input type='hidden' name='delete' value='$file'>
                    <button type='button' class='delete-btn' onclick='confirmDelete(event, \"$file\")'>Delete</button>
                </form></li>";
            }
        }
        ?>
    </ul>
    <!-- Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <h3>Are you sure you want to delete this file?</h3>
            <button id="confirmDeleteBtn" class="modal-btn">Yes, Delete</button>
            <button id="cancelDeleteBtn" class="cancel">Cancel</button>
        </div>
    </div>
    <script src="js/main.js"></script>
</body>
</html>