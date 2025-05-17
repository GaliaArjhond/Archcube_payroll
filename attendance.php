<html lang="en">

<head>
    <link rel="stylesheet" href="assets/css/attendanceStyle.css" />
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Attendance</title>
</head>

<body>
    <div class="side_bar">
        <h1>Archcube Payroll</h1>
        <div class="side_bar_container">
            <div class="side_bar_item">
                <a href="dashboard.php">Dashboard</a>
            </div>
            <div class="side_bar_item">
                <a href="user_management2.php">Employee Management</a>
            </div>
            <div class="side_bar_item">
                <a href="attendance.php">Attendance</a>
            </div>
            <div class="side_bar_item">
                <a href="Payroll_Mangement.php">Payroll Management</a>
            </div>
            <div class="side_bar_item">
                <a href="deduc&benefits.php">Deductions & Benefits Management</a>
            </div>
            <div class="side_bar_item">
                <a href="payslip.php">Payslip Generator</a>
            </div>
            <div class="side_bar_item">
                <a href="reports.php">Summary Reports</a>
            </div>
            <div class="side_bar_item">
                <a href="setting.php">Settings</a>
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
                <div class="tools_group">
                    <label for="show_ent">Show</label>
                    <select name="show_ent" id="show_ent">
                        <?php
                        for ($i = 1; $i <= 20; $i++) {
                            echo "<option value=\"$i\">$i</option>";
                        }
                        ?>
                    </select>
                    <label for="ent_date">Date</label>
                    <input type="date" name="ent_date" id="ent_date" required>

                    <label for="search_input">Search</label>
                    <input type="text" name="search_input" id="search_input" placeholder="Search by Employee ID or Name">
                    </input>
                </div>
            </form>
        </div>
    </div>

</body>

</html>