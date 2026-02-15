<?php
if (isset($_GET['file'])) {
    $file = basename($_GET['file']);
    $filepath = "uploads/" . $file;
    if (file_exists($filepath)) {
        header("Content-Type: application/octet-stream");
        header('Content-Disposition: attachment; filename="'.basename($filepath).'"');
        readfile($filepath);
        exit;
    } else {
        http_response_code(404);
        echo "File not found!";
    }
} else {
    http_response_code(400);
    echo "No file specified";
}
?>
