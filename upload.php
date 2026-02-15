<?php
// Simple JSON response helper
header('Content-Type: application/json');
if (!file_exists('uploads')) mkdir('uploads', 0777, true);

if (!isset($_FILES['file'])) {
    echo json_encode(['success'=>false, 'error'=>'No file sent']);
    exit;
}
$file = $_FILES['file'];
$filename = basename($file['name']);

// basic security: disallow null bytes
if (strpos($filename, "\0") !== false) {
    echo json_encode(['success'=>false, 'error'=>'Invalid filename']);
    exit;
}

$target = 'uploads/' . $filename;

// move uploaded file
if (move_uploaded_file($file['tmp_name'], $target)) {
    echo json_encode(['success'=>true, 'file'=>$filename]);
} else {
    echo json_encode(['success'=>false, 'error'=>'Failed to move uploaded file']);
}
?>
