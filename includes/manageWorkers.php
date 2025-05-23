<?php
$pdo = include '../config/database.php';
session_start();

$employee = null;
$message = '';

// Ensure only logged-in admins can access
if (!isset($_SESSION['userId']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Load selected employee details
    if (isset($_POST['employee_select'])) {
        $employeeId = $_POST['employee_select'];
        $stmt = $pdo->prepare("SELECT * FROM employees WHERE employeeId = ?");
        $stmt->execute([$employeeId]);
        $employee = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($employee) {
            // Set RFID status to 'available'
            $rfidCode = $employee['rfidCodeId'];
            $stmt = $pdo->prepare("UPDATE rfid_codes SET status = 'available' WHERE rfidCode = ?");
            $stmt->execute([$rfidCode]);
        } else {
            $message = "Employee not found.";
        }

        // Delete employee
    } elseif (isset($_POST['delete']) && !empty($_POST['employeeId'])) {
        $employeeId = $_POST['employeeId'];
        $deletedByUserId = $_SESSION['userId'] ?? null;

        // Fetch employee details before deletion
        $stmt = $pdo->prepare("SELECT * FROM employees WHERE employeeId = ?");
        $stmt->execute([$employeeId]);
        $emp = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($emp && !empty($deletedByUserId)) {
            // Log to deletedEmployeesLog
            $logStmt = $pdo->prepare("INSERT INTO deletedEmployeesLog (employeeId, name, email, deletedByUserId) VALUES (?, ?, ?, ?)");
            $logStmt->execute([
                $emp['employeeId'],
                $emp['name'],
                $emp['email'],
                $deletedByUserId
            ]);

            $pdo->exec("SET @currentUserId = " . (int)$deletedByUserId);

            $stmt = $pdo->prepare("DELETE FROM employees WHERE employeeId = ?");
            $stmt->execute([$employeeId]);

            // Log to systemLogs only if userId is present
            if (!empty($deletedByUserId)) {
                $logStmt = $pdo->prepare("INSERT INTO systemLogs (userId, actionTypeId, timestamp) VALUES (?, ?, NOW())");
                $logStmt->execute([$deletedByUserId, 5]); // 5 = delete action
            } else {
                $message = "Warning: User ID missing in session. Deletion was not logged in systemLogs.";
            }

            $message = "Employee deleted successfully.";
            $employee = null;
        } else {
            $message = "Employee not found for deletion or user not authenticated.";
        }

        // Edit employee
    } elseif (isset($_POST['edit']) && !empty($_POST['employeeId'])) {
        $editedByUserId = $_SESSION['userId'] ?? null;

        if (!empty($editedByUserId)) {
            $logStmt = $pdo->prepare("INSERT INTO systemLogs (userId, actionTypeId, timestamp) VALUES (?, ?, NOW())");
            $logStmt->execute([$editedByUserId, 4]); // 4 = edit action
        }

        header("Location: editEmployee.php?employeeId=" . urlencode($_POST['employeeId']));
        exit();
    }
}
?>

<html lang="en">

<head>
    <link rel="stylesheet" href="../assets/css/manageWorkersStyle.css" />
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard</title>
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
                <a href="../includes/logout.php" class="logout" onclick="return confirmLogout();">Log Out</a>
            </div>
        </div>
    </div>

    <div class="main_content">
        <h2>Manage Workers</h2>

        <?php if (!empty($message)): ?>
            <div class="alert"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <div class="select_employee">
            <label for="employee_select">Select Employee</label>
            <form method="post" action="">
                <select id="employee_select" name="employee_select" required>
                    <option value="">Select Employee</option>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM employees");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<option value='" . htmlspecialchars($row['employeeId']) . "'>" . htmlspecialchars($row['name']) . "</option>";
                    }
                    ?>
                </select>
                <input type="submit" value="View Details" />
            </form>
        </div>

        <div class="employee_details">
            <?php if ($employee) : ?>
                <h3>Employee Details</h3>
                <p><strong>Name:</strong> <?= htmlspecialchars($employee['name']) ?></p>
                <p><strong>Email:</strong> <?= htmlspecialchars($employee['email']) ?></p>
                <p><strong>Phone Number:</strong> <?= htmlspecialchars($employee['phoneNumber']) ?></p>
                <p><strong>Address:</strong> <?= htmlspecialchars($employee['address']) ?></p>
                <p><strong>Birth Date:</strong> <?= htmlspecialchars($employee['birthDate']) ?></p>
                <p><strong>Hired Date:</strong> <?= htmlspecialchars($employee['hiredDate']) ?></p>
                <p><strong>Basic Salary:</strong> ₱<?= htmlspecialchars(number_format($employee['basicSalary'], 2)) ?></p>
            <?php else : ?>
                <p>No employee selected.</p>
            <?php endif; ?>
        </div>

        <?php if ($employee) : ?>
            <div class="employee_actions">
                <h3>Actions</h3>
                <form method="post" action="">
                    <input type="hidden" name="employeeId" value="<?= htmlspecialchars($employee['employeeId']) ?>" />
                    <input type="submit" name="edit" value="Edit Employee" />
                    <input type="submit" name="delete" value="Delete Employee" onclick="return confirm('Are you sure you want to delete this employee?');" />
                </form>
            </div>
        <?php endif; ?>
    </div>

    <script>
        function confirmLogout() {
            return confirm('Are you sure you want to log out?');
        }
    </script>
</body>

</html>