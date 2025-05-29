<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

date_default_timezone_set('Asia/Manila');

$backupDir = '../uploads/backups/';
if (!is_dir($backupDir)) {
    mkdir($backupDir, 0777, true);
}
$backupFile = $backupDir . 'backup_' . date('Y-m-d_H-i-s') . '.sql';

$host = 'localhost';
$dbname = 'archcubeV1';
$username = 'root';
$password = 'admin';

$mysqldumpPath = 'C:\\xampp\\mysql\\bin\\mysqldump.exe';
$logFile = $backupDir . 'backup_error_log.txt';
$command = "\"$mysqldumpPath\" -h $host -u $username -p$password $dbname > \"$backupFile\" 2> \"$logFile\"";
exec($command, $output, $return_var);

if ($return_var !== 0) {
    echo "Backup failed. Check error log:<br>";
    if (file_exists($logFile)) {
        echo nl2br(htmlspecialchars(file_get_contents($logFile)));
    } else {
        echo "No error log found.";
    }
    exit;
}

if (file_exists($backupFile)) {
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($backupFile) . '"');
    readfile($backupFile);
    unlink($backupFile);
    exit;
} else {
    echo "Backup failed: backup file was not created.";
}

$output = [];
$return_var = 0;
exec('echo testing > ' . escapeshellarg('../uploads/backups/test.txt'), $output, $return_var);
echo "Return var: $return_var<br>";
echo "Output: " . implode('<br>', $output) . "<br>";

if (file_exists('../uploads/backups/test.txt')) {
    echo "Test file created successfully.";
} else {
    echo "Failed to create test file.";
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
