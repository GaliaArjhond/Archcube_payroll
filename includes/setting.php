<?php
$pdo = include '../config/database.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}
?>

<html lang="en">

<head>
    <link rel="stylesheet" href="../assets/css/settingStyle.css" />
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Attendance</title>
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
        <div class="settings_form">
            <form action="setting_process.php" method="post">
                <div class="settings_card">
                    <div class="settings_card_title">
                        <span>⚙️</span> Account Settings
                    </div>
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
                    <div class="settings_card_title">
                        <span>🛠️</span> Admin Controls
                    </div>
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
                    <div class="settings_card_title">
                        <span>👷</span> Worker Settings
                    </div>
                    <div class="settings_row">
                        <div class="settings_row_info">
                            <span class="settings_row_title">Edit Positions</span>
                            <span class="settings_row_desc">Update or add new job titles</span>
                        </div>
                        <div class="settings_row_action">
                            <a href="../includes/manageWorkers.php" class="settings_btn">Edit</a>
                        </div>
                    </div>
                    <div class="settings_row">
                        <div class="settings_row_info">
                            <span class="settings_row_title">Wage Management</span>
                            <span class="settings_row_desc">Configure salaries, OT, and deductions</span>
                        </div>
                        <div class="settings_row_action">
                            <a href="../includes/workerBenefits.php" class="settings_btn">Configure</a>
                        </div>
                    </div>
                </div>

                <div class="settings_card">
                    <div class="settings_card_title settings_delete_title">
                        <span>🛡️</span> Delete
                    </div>
                    <div class="settings_row">
                        <div class="settings_row_info">
                            <span class="settings_row_title">Delete Worker</span>
                            <span class="settings_row_desc">Permanently remove account and data</span>
                        </div>
                        <div class="settings_row_action">
                            <a href="../includes/manageWorkers.php" class="settings_btn danger">Delete Account</a>
                        </div>
                    </div>
                    <div class="settings_row">
                        <div class="settings_row_info">
                            <span class="settings_row_title">Delete This Account</span>
                            <span class="settings_row_desc">Disable this account and data</span>
                        </div>
                        <div class="settings_row_action">
                            <a href="../includes/workerBenefits.php" class="settings_btn danger">Delete this Account</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>

</body>

</html>