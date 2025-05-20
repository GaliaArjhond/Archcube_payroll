<?php
$pdo = include '../config/database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $defaultPassword = password_hash('changeme', PASSWORD_DEFAULT);

        // Handle photo upload
        $profileImage = 'uploads/default.png'; // default fallback
        if (isset($_FILES['employee_photo']) && $_FILES['employee_photo']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['employee_photo']['name'], PATHINFO_EXTENSION);
            $filename = uniqid('emp_', true) . '.' . $ext;
            $uploadDir = '../uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            move_uploaded_file($_FILES['employee_photo']['tmp_name'], $uploadDir . $filename);
            $profileImage = 'uploads/' . $filename;
        }

        // Insert into employees
        $stmt = $pdo->prepare("INSERT INTO employees (
            rfidCode, name, password, email, phoneNumber, address, birthDate, role,
            genderId, basicSalary, civilStatusId, positionId, empStatusId, payrollTypeId,
            profileImage, createAt, updatedAt
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");

        $stmt->execute([
            $_POST['employee_rfidCode'],
            $_POST['employee_name'],
            $defaultPassword,
            $_POST['employee_email'],
            $_POST['employee_contact'],
            $_POST['employee_address'],
            $_POST['employee_birthdate'],
            $_POST['employee_role'] ?? 'admin',
            $_POST['employee_gender'], // must be genderId
            $_POST['basic_salary'],
            $_POST['employee_civil'],
            $_POST['employee_position'],
            $_POST['employment_status'],
            $_POST['payroll_type'],
            $profileImage
        ]);

        $employeeId = $pdo->lastInsertId();

        // Insert government contributions
        $stmt2 = $pdo->prepare("INSERT INTO govtContributions (employeeId, contributionTypeId, contributionNumber, contributionAmount) VALUES (?, ?, ?, ?)");

        $contributions = [
            ['type' => 1, 'number' => $_POST['sss_number'], 'amount' => $_POST['sss_contribution']],
            ['type' => 2, 'number' => $_POST['philhealth_pin'], 'amount' => $_POST['philhealth_contribution']],
            ['type' => 3, 'number' => $_POST['pagibig_number'], 'amount' => $_POST['pagibig_contribution']],
            ['type' => 4, 'number' => $_POST['tin_number'], 'amount' => 0],
            ['type' => 5, 'number' => '', 'amount' => $_POST['withholding_tax']],
            ['type' => 6, 'number' => '', 'amount' => $_POST['thirteenth_month']]
        ];

        foreach ($contributions as $contribution) {
            $stmt2->execute([
                $employeeId,
                $contribution['type'],
                $contribution['number'],
                $contribution['amount']
            ]);
        }

        header("Location: user_management2.php");
        exit();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        exit();
    }
}

?>

<html lang="en">

<head>
    <link rel="stylesheet" href="../assets/css/addempStyle.css">
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Employee</title>
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
        <h2>Add Employee</h2>
        <form action="" method="post" enctype="multipart/form-data">
            <img id="photoPreview" class="photo-preview" src="assets/img/default-user.png" alt="Photo Preview">

            <label for="employee_photo">Upload Photo:</label>
            <input type="file" id="employee_photo" name="employee_photo" accept="image/*" onchange="previewPhoto(event)" />

            <h3>Personal Information</h3>

            <label for="employee_name">Employee Name:</label>
            <input type="text" id="employee_name" name="employee_name" required />

            <label for="employee_rfidCode">RFID code:</label>
            <input type="text" id="employee_rfidCode" name="employee_rfidCode" required />

            <label for="employee_gender">Gender:</label>
            <select id="employee_gender" class="employee_gender" name="employee_gender" required>
                <option value="">-- Select Gender --</option>
                <option value="1">Male</option>
                <option value="2">Female</option>
                <option value="3">Non-binary</option>
                <option value="4">Prefer not to say</option>
                <option value="5">Others</option>
            </select>

            <label for="employee_birthdate">Birthdate:</label>
            <input type="date" id="employee_birthdate" name="employee_birthdate" required />

            <label for="employee_civil">Civil Status:</label>
            <select name="employee_civil" id="employee_civil" required>
                <option value="">-- Select Status --</option>
                <option value="1">Single</option>
                <option value="2">Married</option>
                <option value="3">Divorced</option>
                <option value="4">Widowed</option>
            </select>


            <label for="employee_contact">Contact Number:</label>
            <input type="text" id="employee_contact" name="employee_contact" required />

            <label for="employee_email">Email:</label>
            <input type="text" id="employee_email" name="employee_email" required />

            <h3>Employment Information</h3>

            <label for="employee_position">Position:</label>
            <select name="employee_position" id="employee_position" required>
                <option value="">-- Select Status --</option>
                <option value="1">Architect</option>
                <option value="2">Engineer</option>
                <option value="3">Foreman</option>
                <option value="4">Laborer</option>
            </select>


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

            <label for="payroll_type">Payroll Type:</label>
            <select name="payroll_type" id="payroll_type" required>
                <option value="">-- Select Status --</option>
                <option value="1">Monthly</option>
                <option value="2">Semi-Monthly</option>
                <option value="3">Weekly</option>
                <option value="4">Daily</option>
            </select>


            <label for="basic_salary">Basic Salary:</label>
            <input type="text" id="basic_salary" name="basic_salary" required />

            <h3>Government Contributions</h3>

            <label for="sss_number">SSS Number:</label>
            <input type="text" id="sss_number" name="sss_number" required />

            <label for="sss_contribution">SSS Contribution:</label>
            <input type="text" id="sss_contribution" name="sss_contribution" required />

            <label for="philhealth_pin">PhilHealth ID Number (PIN):</label>
            <input type="text" id="philhealth_pin" name="philhealth_pin" required />

            <label for="philhealth_contribution">PhilHealth Contribution:</label>
            <input type="text" id="philhealth_contribution" name="philhealth_contribution" required />

            <label for="pagibig_number">Pag-IBIG Number:</label>
            <input type="text" id="pagibig_number" name="pagibig_number" required />

            <label for="pagibig_contribution">Pag-IBIG Contribution:</label>
            <input type="text" id="pagibig_contribution" name="pagibig_contribution" required />

            <label for="tin_number">TIN:</label>
            <input type="text" id="tin_number" name="tin_number" required />

            <label for="withholding_tax">Withholding Tax:</label>
            <input type="text" id="withholding_tax" name="withholding_tax" required />

            <label for="thirteenth_month">13th Month Pay:</label>
            <input type="text" id="thirteenth_month" name="thirteenth_month" required />

            <button type="submit">Submit</button>
        </form>
    </div>

    <script>
        function previewPhoto(event) {
            const [file] = event.target.files;
            if (file) {
                document.getElementById('photoPreview').src = URL.createObjectURL(file);
            }
        }
    </script>

</body>

</html>