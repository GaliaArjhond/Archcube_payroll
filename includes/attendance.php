<html lang="en">

<head>
    <link rel="stylesheet" href="../assets/css/attendanceStyle.css" />
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
                <a href="" class="logout">Log Out</a>
            </div>
        </div>
    </div>

    <div class="main_content">
        <h2>Attendance Management</h2>
        <div class="attendance_form">
            <form action="attendance_process.php" method="post">

                <div class="download_group">
                    <button type="submit" name="download_attendance" class="download_button">Download Attendance</button>
                    <button type="submit" name="print_attendance" class="download_button">Print Attendance</button>
                </div>

                <div class="tools_group">
                    <div class="left-group">
                        <label for="show_ent">Show</label>
                        <select name="show_ent" id="show_ent">
                            <?php for ($i = 1; $i <= 20; $i++) echo "<option value=\"$i\">$i</option>"; ?>
                        </select>

                        <label for="ent_date">Date</label>
                        <input type="date" name="ent_date" id="ent_date" required value="<?php echo date('Y-m-d'); ?>">
                    </div>

                    <div class="right-group">
                        <label for="search_input">Search</label>
                        <input type="text" name="search_input" id="search_input" placeholder="Search by Employee ID or Name">
                    </div>
                </div>


                <div class="table_section">
                    <table class="data_table">
                        <tr>
                            <th>Date</th>
                            <th>Employee ID</th>
                            <th>Employee Name</th>
                            <th>Position</th>
                            <th>Check In</th>
                            <th>Check Out</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </table>
                </div>
            </form>
        </div>
    </div>

</body>

</html>