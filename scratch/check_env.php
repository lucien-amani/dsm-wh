<?php
require_once 'config/database.php';
echo "GD extension: " . (extension_loaded('gd') ? "LOADED" : "MISSING") . "\n";
$upload_dir = 'uploads/';
echo "Uploads dir: " . $upload_dir . "\n";
echo "Exists: " . (file_exists($upload_dir) ? "YES" : "NO") . "\n";
echo "Writable: " . (is_writable($upload_dir) ? "YES" : "NO") . "\n";
