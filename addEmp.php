<html lang="en">

<head>
    <link rel="stylesheet" href="assets/css/user_management_style.css" />
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Employee</title>
</head>

<body>
    <div class="side_bar">
        <h1>Archcube Payroll</h1>
        <div class="side_bar_container">
            <div class="side_bar_item">
                <a href="dashboard.html">Dashboard</a>
            </div>
            <div class="side_bar_item">
                <a href="employee_management.html">Employee Management</a>
            </div>
            <div class="side_bar_item">
                <a href="Attendance.html">Attendance</a>
            </div>
            <div class="side_bar_item">
                <a href="payroll.html">Payroll Management</a>
            </div>
            <div class="side_bar_item">
                <a href="reports.html">Summary Reports</a>
            </div>
            <div class="side_bar_item">
                <a href="settings.html">Settings</a>
            </div>
            <div class="side_bar_item">
                <a href="logout.html" class="logout">Log Out</a>
            </div>
        </div>
    </div>

    <div class="main_content">
        <h2>Add Employee</h2>
        <form action="upload_image.php" method="post" enctype="multipart/form-data">
            <label for="employee_photo">Upload Photo:</label>
            <input type="file" id="employee_photo" name="employee_photo" accept="image/*" required />

            <h3>Personal Information</h3>

            <label for="employee_name">Employee Name:</label>
            <input type="text" id="employee_name" name="employee_name" required />

            <label for="employee_gender">Gender:</label>
            <select id="employee_gender" class="employee_gender">
                <option value="">-- Select Gender --</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
                <option value="Other">Other</option>
            </select required>

            <label for="employee_birthdate">Birthdate:</label>
            <input type="date" id="employee_birthdate" name="employee_birthdate" required />

            <label for="employee_civil">Civil Status:</label>
            <select id="employee_civil" class="employee_civil">
                <option value="">-- Select Civil Status --</option>
                <option value="single">Single</option>
                <option value="married">Married</option>
                <option value="divorced">Divorced</option>
                <option value="widowed">Widowed</option>
            </select required>

            <label for="employee_contact">Contact Number:</label>
            <input type="text" id="employee_contact" name="employee_contact" required />

            <label for="employee_email">Email:</label>
            <input type="text" id="employee_email" name="employee_email" required />

            <h3>Employment Information</h3>

            <label for="employee_position">Position:</label>
            <select id="employee_position" class="employee_position">
                <option value="">-- Select Position --</option>
                <option value="architect">Architect</option>
                <option value="engineer">Engineer</option>
            </select required>

            <label for="employment_status">Employment Status*:</label>
            <select name="employment_status" id="employment_status" required>
                <option value="">-- Select Status --</option>
                <option value="full_time">Full-Time</option>
                <option value="part_time">Part-Time</option>
                <option value="contractual">Contractual</option>
                <option value="probationary">Probationary</option>
                <option value="intern">Intern</option>
                <option value="terminated">Terminated</option>
            </select>

            <label for="employee_dateHired">Date Hired:</label>
            <input type="date" id="employee_dateHired" name="employee_dateHired" required />


            <button type="submit">Submit</button>
        </form>
    </div>

</body>

</html>