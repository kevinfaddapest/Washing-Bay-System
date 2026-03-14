<?php
$folder = "D:/Xamp/htdocs/Car-Wash/Backups";
$file = $folder . "/test.txt";

if (is_writable($folder)) {
    file_put_contents($file, "This is a test.");
    echo "✅ Backup folder is writable!";
} else {
    echo "❌ Backup folder is NOT writable!";
}
?>
