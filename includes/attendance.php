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
                <a href="../includes/logout.php" class="logout">Log Out</a>
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

                        <div class="date_range_group">
                            <label for="from_date">From</label>
                            <input type="date" name="from_date" id="from_date">
                            <label for="to_date">To</label>
                            <input type="date" name="to_date" id="to_date">
                        </div>

                    </div>
                    <div class="right-group">

                        <div class="view_group">
                            <label for="view_select">View: </label>
                            <input type="radio" id="view_all" name="view_select" value="all" checked>
                            <label for="view_all">All</label>
                            <input type="radio" id="view_today" name="view_select" value="today">
                            <label for="view_today">Today</label>
                            <input type="radio" id="view_week1" name="view_select" value="1_week">
                            <label for="view_week1">1 Week</label>
                            <input type="radio" id="view_week2" name="view_select" value="2_week">
                            <label for="view_week2">2 Weeks</label>
                            <input type="radio" id="view_month" name="view_select" value="month">
                            <label for="view_month">Month</label>
                            <input type="radio" id="view_year" name="view_select" value="year">
                            <label for="view_year">Year</label>
                        </div>

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