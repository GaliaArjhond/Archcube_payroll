<?php
$pdo = include '../config/database.php';
session_start();
$message = '';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $username = $_POST['admin_username'];
        $password = password_hash($_POST['admin_password'], PASSWORD_DEFAULT);
        $role = 'admin';
        $secQuesId = $_POST['security_question'];
        $secQuesAnswer = password_hash($_POST['security_answer'], PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO users (username, password, role, secQuesId, secQuesAnswer, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$username, $password, $role, $secQuesId, $secQuesAnswer]);

        $newUserId = $pdo->lastInsertId();

        $actionTypeId = 16;
        $logStmt = $pdo->prepare("INSERT INTO systemLogs (userId, actionTypeId, timestamp) VALUES (?, ?, NOW())");
        $logStmt->execute([$newUserId, $actionTypeId]);

        $message = "Admin account created successfully.";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}
?>


<html lang="en">

<head>
    <link rel="stylesheet" href="../assets/css/adminControlStyle.css" />
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Control</title>
</head>

<body>
    <div class="side_bar">
        <h1>Archcube Payroll</h1>
        <div class="side_bar_container">
            <div class="side_bar_item">
                <a href="../includes/dashboard.php">Dashboard</a>
            </div>
            <div class="side_bar_item">
                <a href="../includes/user_management2.php">Employee Management</a>
            </div>
            <div class="side_bar_item">
                <a href="../includes/attendance.php">Attendance</a>
            </div>
            <div class="side_bar_item">
                <a href="../includes/Payroll_Mangement.php">Payroll Management</a>
            </div>
            <div class="side_bar_item">
                <a href="../includes/deduc&benefits.php">Deductions & Benefits Management</a>
            </div>
            <div class="side_bar_item">
                <a href="../includes/payslip.php">Payslip Generator</a>
            </div>
            <div class="side_bar_item">
                <a href="../includes/reports.php">Summary Reports</a>
            </div>
            <div class="side_bar_item">
                <a href="../includes/setting.php">Settings</a>
            </div>
            <div class="side_bar_item">
                <a href="../includes/logout.php" class="logout">Log Out</a>
            </div>
        </div>
    </div>

    <div class="main_content">
        <div class="header">
            <h1>Admin Control</h1>
        </div>
        <div class="panel">
            <?php if ($message): ?>
                <div class="admin-message"><?= htmlspecialchars($message) ?></div>
            <?php endif; ?>
            <form method="post" action="">
                <label for="admin_username">Admin Username:</label>
                <input type="text" id="admin_username" name="admin_username" required>

                <label for="admin_password">Password:</label>
                <input type="password" id="admin_password" name="admin_password" required>

                <label for="security_question">Security Question:</label>
                <select id="security_question" name="security_question" required>
                    <option value="">--Select a question--</option>
                    <option value="1">What is your mother's maiden name?</option>
                    <option value="2">What was the name of your first pet?</option>
                    <option value="3">What is the name of your first school?</option>
                    <option value="4">What is your favorite book?</option>
                </select>

                <label for="security_answer">Answer:</label>
                <input type="text" id="security_answer" name="security_answer" required>

                <button type="submit">Submit</button>
            </form>
        </div>
    </div>

</body>

</html>