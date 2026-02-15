<?php
header('Content-Type: application/json');
$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['file'])) {
    echo json_encode(['success'=>false, 'error'=>'No file specified']);
    exit;
}
$file = basename($input['file']);
$path = 'uploads/' . $file;
if (!file_exists($path)) {
    echo json_encode(['success'=>false, 'error'=>'File not found']);
    exit;
}
if (unlink($path)) {
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false, 'error'=>'Delete failed']);
}
?>
