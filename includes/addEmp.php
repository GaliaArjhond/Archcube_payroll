<?php

$pdo = include '../config/database.php';
session_start();

$errorMsg = '';
$successMsg = '';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

class Employee
{
    private $pdo;
    public $id;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function add($data)
    {
        $stmt = $this->pdo->prepare("INSERT INTO employees (
            rfidCodeId, name, email, phoneNumber, address, birthDate, role,
            genderId, hiredDate, basicSalary, civilStatusId, positionId, empStatusId, payrollTypeId,
            profileImage, createAt, updatedAt
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())");

        $stmt->execute([
            $data['rfidCodeId'],
            $data['name'],
            $data['email'],
            $data['phoneNumber'],
            $data['address'],
            $data['birthDate'],
            $data['role'],
            $data['genderId'],
            $data['hiredDate'],
            $data['basicSalary'],
            $data['civilStatusId'],
            $data['positionId'],
            $data['empStatusId'],
            $data['payrollTypeId'],
            $data['profileImage']
        ]);
        $this->id = $this->pdo->lastInsertId();
        return $this->id;
    }

    public function assignRFID($rfidCodeId)
    {
        $this->pdo->prepare("UPDATE rfid_cards SET status = 'assigned' WHERE rfidCodeId = ?")->execute([$rfidCodeId]);
    }

    public function assignSchedule($templateId)
    {
        $stmt = $this->pdo->prepare("INSERT INTO employee_schedules (employeeId, templateId) VALUES (?, ?)");
        $stmt->execute([$this->id, $templateId]);
    }
}

class Contribution
{
    private $pdo;
    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function add($employeeId, $type, $number, $amount)
    {
        $stmt = $this->pdo->prepare("INSERT INTO govtContributions (employeeId, contributionTypeId, contributionNumber, contributionAmount) VALUES (?, ?, ?, ?)");
        $stmt->execute([$employeeId, $type, $number, $amount]);
    }
}

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

        $employee = new Employee($pdo);
        $employeeId = $employee->add([
            'rfidCodeId'    => $_POST['employee_rfidCodeId'],
            'name'          => $_POST['employee_name'],
            'email'         => $_POST['employee_email'],
            'phoneNumber'   => $_POST['employee_contact'],
            'address'       => $_POST['employee_address'],
            'birthDate'     => $_POST['employee_birthdate'],
            'role'          => $_POST['employee_role'] ?? 'admin',
            'genderId'      => $_POST['employee_gender'],
            'hiredDate'     => $_POST['hired_date'],
            'basicSalary'   => $_POST['basic_salary'],
            'civilStatusId' => $_POST['employee_civil'],
            'positionId'    => $_POST['employee_position'],
            'empStatusId'   => $_POST['employment_status'],
            'payrollTypeId' => $_POST['payroll_type'],
            'profileImage'  => $profileImage
        ]);

        $employee->assignRFID($_POST['employee_rfidCodeId']);
        $employee->assignSchedule($_POST['templateId']);

        $contribution = new Contribution($pdo);
        $contributions = [
            ['type' => 1, 'number' => $_POST['sss_number'], 'amount' => $_POST['sss_contribution']],
            ['type' => 2, 'number' => $_POST['philhealth_pin'], 'amount' => $_POST['philhealth_contribution']],
            ['type' => 3, 'number' => $_POST['pagibig_number'], 'amount' => $_POST['pagibig_contribution']],
            ['type' => 4, 'number' => $_POST['tin_number'], 'amount' => 0],
            ['type' => 5, 'number' => '', 'amount' => $_POST['withholding_tax']],
        ];
        foreach ($contributions as $c) {
            $contribution->add($employeeId, $c['type'], $c['number'], $c['amount']);
        }

        $actionTypeId = 3;
        if (!isset($_SESSION['userId']) || empty($_SESSION['userId'])) {
            $errorMsg = "Error: User ID missing in session. Cannot log action.";
        } else {
            try {
                $logStmt = $pdo->prepare("INSERT INTO systemLogs (userId, actionTypeId, timestamp) VALUES (?, ?, NOW())");
                $logStmt->execute([$_SESSION['userId'], $actionTypeId]);
            } catch (PDOException $e) {
                $errorMsg = "Log Insert Error: " . $e->getMessage();
            }
        }

        $successMsg = "Employee added successfully!";
    } catch (PDOException $e) {
        $errorMsg = $e->getMessage();
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
                <a href="../includes/logout.php" class="logout" onclick="return confirmLogout();">Log Out</a>
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

            <label for="employee_rfidCodeId">RFID code:</label>
            <select id="employee_rfidCodeId" name="employee_rfidCodeId" required>
                <option value="">-- Select Available RFID --</option>
                <?php
                $rfidStmt = $pdo->query("SELECT rfidCodeId, rfidCode FROM rfid_cards WHERE status = 'available'");
                while ($row = $rfidStmt->fetch()) {
                    echo '<option value="' . htmlspecialchars($row['rfidCodeId']) . '">' . htmlspecialchars($row['rfidCode']) . '</option>';
                }
                ?>
            </select>

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

            <label for="employee_address">Address:</label>
            <input type="text" id="employee_address" name="employee_address" required />

            <h3>Employment Information</h3>

            <label for="hired_date">Hired Date:</label>
            <input type="date" id="hired_date" name="hired_date" value="<?php echo date('Y-m-d'); ?>" required />

            <label for="employee_role">Role:</label>
            <select name="employee_role" id="employee_role">
                <option value="">-- Select Role --</option>
                <option value="admin">Admin</option>
                <option value="user">User</option>
            </select>

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
                <option value="1">Full-Time</option>
                <option value="2">Part-Time</option>
                <option value="3">Contractual</option>
                <option value="4">Probationary</option>
                <option value="5">Intern</option>
                <option value="6">Terminated</option>
            </select>

            <label for="employee_schedule">Schedule</label>
            <label for="templateId">Schedule Template:</label>
            <select name="templateId" id="templateId" required>
                <option value="" disabled selected>Select template</option>
                <?php
                // Fetch schedule templates from the database
                $templates = [];
                try {
                    $tplStmt = $pdo->query("SELECT templateId, templateName FROM schedule_templates");
                    $templates = $tplStmt->fetchAll(PDO::FETCH_ASSOC);
                } catch (PDOException $e) {
                    // Optionally handle error
                }
                foreach ($templates as $tpl): ?>
                    <option value="<?= $tpl['templateId'] ?>"><?= htmlspecialchars($tpl['templateName']) ?></option>
                <?php endforeach; ?>
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
            <input type="number" id="basic_salary" name="basic_salary" step="0.01" required />

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

            <button type="submit">Submit</button>
        </form>

        <?php if ($errorMsg): ?>
            <div class="alert error-alert">
                <strong>Error:</strong> <?php echo htmlspecialchars($errorMsg); ?>
            </div>
        <?php endif; ?>
        <?php if ($successMsg): ?>
            <div class="alert success-alert">
                <strong>Success:</strong> <?php echo htmlspecialchars($successMsg); ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($successMsg): ?>
        <script>
            window.successMsg = "<?= addslashes($successMsg) ?>";
        </script>
    <?php endif; ?>
    <script src="../assets/js/addEmp.js"></script>

</body>

</html>