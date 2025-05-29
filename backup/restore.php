<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['backupFile'])) {
    $file = $_FILES['backupFile']['tmp_name'];
    $ext = strtolower(pathinfo($_FILES['backupFile']['name'], PATHINFO_EXTENSION));

    if ($ext !== 'sql') {
        echo "❌ Invalid file format. Only .sql files allowed.";
        exit();
    }

    $host = 'localhost';
    $dbname = 'archcubeV1';
    $username = 'root';
    $password = 'admin';

    // Use escapeshellarg for safety
    $command = "mysql -u " . escapeshellarg($username) . " -p" . escapeshellarg($password) . " " . escapeshellarg($dbname) . " < " . escapeshellarg($file);
    system($command, $result);

    if ($result === 0) {
        echo "✅ Database restored successfully!";
    } else {
        echo "❌ Restore failed. Please check the file.";
    }
} else {
    // Simple upload form if accessed directly
?>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="backupFile" accept=".sql" required>
        <button type="submit">Restore Database</button>
    </form>
<?php
}
