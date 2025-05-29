<?php
session_start();
$pdo = include('../config/database.php');

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Account Settings</title>
    <link rel="stylesheet" href="../assets/css/settingStyle.css" />
</head>

<body>
    <div class="side_bar">
        <h1>Archcube Payroll</h1>
        <div class="side_bar_container">
            <div class="side_bar_item"><a href="../includes/dashboard.php">Dashboard</a></div>
            <div class="side_bar_item"><a href="../includes/user_management2.php">Employee Management</a></div>
            <div class="side_bar_item"><a href="../includes/attendance.php">Attendance</a></div>
            <div class="side_bar_item"><a href="../includes/Payroll_Mangement.php">Payroll Management</a></div>
            <div class="side_bar_item"><a href="../includes/deduc&benefits.php">Deductions & Benefits</a></div>
            <div class="side_bar_item"><a href="../includes/payslip.php">Payslip Generator</a></div>
            <div class="side_bar_item"><a href="../includes/reports.php">Summary Reports</a></div>
            <div class="side_bar_item"><a href="../includes/setting.php">Settings</a></div>
            <div class="side_bar_item"><a href="../includes/logout.php" onclick="return confirmLogout();" class="logout">Log Out</a></div>
        </div>
    </div>

    <div class="main_content">
        <div class="settings_form">
            <div class="settings_card">
                <div class="settings_card_title"><span>⚙️</span> Account Settings</div>
                <div class="settings_row">
                    <div class="settings_row_info">
                        <span class="settings_row_title">Change Password</span>
                        <span class="settings_row_desc">Update your login password</span>
                    </div>
                    <div class="settings_row_action">
                        <a href="../includes/changePassword.php" class="settings_btn">Change</a>
                    </div>
                </div>
            </div>

            <div class="settings_card">
                <div class="settings_card_title"><span>🛠️</span> Admin Controls</div>
                <div class="settings_row">
                    <div class="settings_row_info">
                        <span class="settings_row_title">Manage Admins</span>
                        <span class="settings_row_desc">Add or remove system administrators</span>
                    </div>
                    <div class="settings_row_action">
                        <a href="../includes/adminControls.php" class="settings_btn">Manage</a>
                    </div>
                </div>
                <div class="settings_row">
                    <div class="settings_row_info">
                        <span class="settings_row_title">System Logs</span>
                        <span class="settings_row_desc">View activity history and login records</span>
                    </div>
                    <div class="settings_row_action">
                        <a href="../includes/viewLogs.php" class="settings_btn">View Logs</a>
                    </div>
                </div>
            </div>

            <div class="settings_card">
                <div class="settings_card_title"><span>👷</span> Worker Settings</div>
                <div class="settings_row">
                    <div class="settings_row_info">
                        <span class="settings_row_title">Edit Positions</span>
                        <span class="settings_row_desc">Update or add job titles</span>
                    </div>
                    <div class="settings_row_action">
                        <a href="../includes/manageWorkers.php" class="settings_btn">Edit</a>
                    </div>
                </div>
                <div class="settings_row">
                    <div class="settings_row_info">
                        <span class="settings_row_title">Wage Management</span>
                        <span class="settings_row_desc">Configure salaries and deductions</span>
                    </div>
                    <div class="settings_row_action">
                        <a href="../includes/workerBenefits.php" class="settings_btn">Configure</a>
                    </div>
                </div>
            </div>

            <div class="settings_card">
                <div class="settings_card_title settings_delete_title"><span>🛡️</span> Delete</div>
                <div class="settings_row">
                    <div class="settings_row_info">
                        <span class="settings_row_title">Delete Worker</span>
                        <span class="settings_row_desc">Permanently remove worker</span>
                    </div>
                    <div class="settings_row_action">
                        <a href="../includes/manageWorkers.php" class="settings_btn danger">Delete Worker</a>
                    </div>
                </div>
                <div class="settings_row">
                    <div class="settings_row_info">
                        <span class="settings_row_title">Delete This Account</span>
                        <span class="settings_row_desc">Disable this admin account</span>
                    </div>
                    <div class="settings_row_action">
                        <a href="../includes/workerBenefits.php" class="settings_btn danger">Delete Account</a>
                    </div>
                </div>
            </div>

            <div class="settings_card">
                <div class="settings_card_title"><span>🗄️</span> Backup and Restore</div>

                <!-- Manual Backup -->
                <div class="settings_row">
                    <div class="settings_row_info">
                        <span class="settings_row_title">Download Backup</span>
                        <span class="settings_row_desc">Create and download a full database backup</span>
                    </div>
                    <div class="settings_row_action">
                        <form action="/backup/manualBackup.php" method="POST">
                            <button type="submit" class="settings_btn success">Download Backup</button>
                        </form>
                    </div>
                </div>

                <!-- Restore Backup -->
                <div class="settings_row">
                    <div class="settings_row_info">
                        <span class="settings_row_title">Restore Backup</span>
                        <span class="settings_row_desc">Restore system from an existing .sql backup file</span>
                    </div>
                    <div class="settings_row_action">
                        <form action="/backup/restore.php" method="POST" enctype="multipart/form-data" onsubmit="return confirm('Are you sure you want to restore the database? This will overwrite current data.')">
                            <input type="file" name="backupFile" accept=".sql" required class="settings_file_input">
                            <button type="submit" class="settings_btn danger">Restore Database</button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function confirmLogout() {
            return confirm('Are you sure you want to log out?');
        }
    </script>
</body>

</html>