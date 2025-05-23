<?php
// filepath: c:\xampp\htdocs\Archcube_payroll\includes\Schedule.php
$pdo = include '../config/database.php';
session_start();

// Only admin allowed
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../index.php');
    exit();
}

// --- OOP FUNDAMENTALS: Classes for Schedule Management ---

class ScheduleTemplate
{
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }

    // Encapsulation: Add a new schedule template
    public function add($templateName, $workDays, $timeIn, $timeOut, $breakStart, $breakEnd)
    {
        $stmt = $this->pdo->prepare("INSERT INTO schedule_templates (templateName, workDays, timeIn, timeOut, breakStart, breakEnd) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$templateName, $workDays, $timeIn, $timeOut, $breakStart, $breakEnd]);
    }

    // Abstraction: Get all templates
    public function getAll()
    {
        return $this->pdo->query("SELECT * FROM schedule_templates ORDER BY templateId DESC")->fetchAll(PDO::FETCH_ASSOC);
    }

    // Abstraction: Delete a template
    public function delete($templateId)
    {
        $stmt = $this->pdo->prepare("DELETE FROM schedule_templates WHERE templateId = ?");
        $stmt->execute([$templateId]);
    }
}

class EmployeeSchedule
{
    private $pdo;
    public function __construct($pdo) { $this->pdo = $pdo; }

    // Encapsulation: Assign or update schedule
    public function assign($employeeId, $templateId)
    {
        $exists = $this->pdo->prepare("SELECT * FROM employee_schedules WHERE employeeId = ?");
        $exists->execute([$employeeId]);
        if ($exists->fetch()) {
            $stmt = $this->pdo->prepare("UPDATE employee_schedules SET templateId = ?, assignedAt = NOW() WHERE employeeId = ?");
            $stmt->execute([$templateId, $employeeId]);
        } else {
            $stmt = $this->pdo->prepare("INSERT INTO employee_schedules (employeeId, templateId) VALUES (?, ?)");
            $stmt->execute([$employeeId, $templateId]);
        }
    }

    // Abstraction: Delete assignment
    public function delete($employeeScheduleId)
    {
        $stmt = $this->pdo->prepare("DELETE FROM employee_schedules WHERE employeeScheduleId = ?");
        $stmt->execute([$employeeScheduleId]);
    }

    // Abstraction: Get all assignments
    public function getAll()
    {
        return $this->pdo->query(
            "SELECT es.employeeScheduleId, e.name, st.templateName, st.workDays, st.timeIn, st.timeOut, st.breakStart, st.breakEnd, es.assignedAt
             FROM employee_schedules es
             JOIN employees e ON es.employeeId = e.employeeId
             JOIN schedule_templates st ON es.templateId = st.templateId
             ORDER BY es.employeeScheduleId DESC"
        )->fetchAll(PDO::FETCH_ASSOC);
    }
}

// --- END OOP FUNDAMENTALS ---

// Instantiate objects
$templateManager = new ScheduleTemplate($pdo);
$assignmentManager = new EmployeeSchedule($pdo);

// CREATE template
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_template'])) {
    $templateName = trim($_POST['templateName']);
    if (empty($templateName)) die('Template name is required.');
    if (!isset($_POST['workDays']) || !is_array($_POST['workDays'])) die('Please select at least one work day.');
    $workDays = implode(',', $_POST['workDays']);
    $timeIn = $_POST['timeIn'];
    $timeOut = $_POST['timeOut'];
    $breakStart = $_POST['breakStart'] ?: null;
    $breakEnd = $_POST['breakEnd'] ?: null;
    $templateManager->add($templateName, $workDays, $timeIn, $timeOut, $breakStart, $breakEnd);
    header("Location: Schedule.php?template=created");
    exit();
}

// DELETE template
if (isset($_GET['delete_template'])) {
    $templateManager->delete($_GET['delete_template']);
    header("Location: Schedule.php?template=deleted");
    exit();
}

// ASSIGN or UPDATE employee schedule
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_schedule'])) {
    $employeeId = $_POST['employeeId'];
    $templateId = $_POST['templateId'];
    $assignmentManager->assign($employeeId, $templateId);
    header("Location: Schedule.php?assigned=success");
    exit();
}

// DELETE employee schedule assignment
if (isset($_GET['delete_assignment'])) {
    $assignmentManager->delete($_GET['delete_assignment']);
    header("Location: Schedule.php?assignment=deleted");
    exit();
}

// --- FETCH DATA FOR DISPLAY ---
$employees = $pdo->query("SELECT employeeId, name FROM employees ORDER BY name ASC")->fetchAll(PDO::FETCH_ASSOC);
$templates = $templateManager->getAll();
$assignments = $assignmentManager->getAll();
?>

<!DOCTYPE html>
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
                <a href="../includes/logout.php" class="logout" onclick="return confirmLogout();">Log Out</a>
            </div>
        </div>
    </div>

    <div class="main_content">
        <h2>Manage Schedule Templates</h2>
        <form method="post">
            <label for="templateName">Template Name:</label>
            <input type="text" name="templateName" id="templateName" required>

            <label>Work Days:</label>
            <div id="workDaysRange">
                <label><input type="checkbox" name="workDays[]" value="Monday"> Monday</label><br>
                <label><input type="checkbox" name="workDays[]" value="Tuesday"> Tuesday</label><br>
                <label><input type="checkbox" name="workDays[]" value="Wednesday"> Wednesday</label><br>
                <label><input type="checkbox" name="workDays[]" value="Thursday"> Thursday</label><br>
                <label><input type="checkbox" name="workDays[]" value="Friday"> Friday</label><br>
                <label><input type="checkbox" name="workDays[]" value="Saturday"> Saturday</label><br>
                <label><input type="checkbox" name="workDays[]" value="Sunday"> Sunday</label>
            </div>


            <label for="timeIn">Time In:</label>
            <input type="time" name="timeIn" id="timeIn" required>

            <label for="timeOut">Time Out:</label>
            <input type="time" name="timeOut" id="timeOut" required>

            <label for="breakStart">Break Start (optional):</label>
            <input type="time" name="breakStart" id="breakStart">

            <label for="breakEnd">Break End (optional):</label>
            <input type="time" name="breakEnd" id="breakEnd">

            <button type="submit" name="create_template">Add Template</button>
        </form>

        <h3>Existing Schedule Templates</h3>
        <div class="table_section">
            <table class="data_table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Work Days</th>
                        <th>Time In</th>
                        <th>Time Out</th>
                        <th>Break Start</th>
                        <th>Break End</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($templates as $tpl): ?>
                        <tr>
                            <td><?= htmlspecialchars($tpl['templateName']) ?></td>
                            <td><?= htmlspecialchars($tpl['workDays']) ?></td>
                            <td><?= htmlspecialchars($tpl['timeIn']) ?></td>
                            <td><?= htmlspecialchars($tpl['timeOut']) ?></td>
                            <td><?= htmlspecialchars($tpl['breakStart'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($tpl['breakEnd'] ?? '-') ?></td>
                            <td>
                                <a href="?delete_template=<?= $tpl['templateId'] ?>" onclick="return confirm('Delete this template?')">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <h2>Assign Schedule Templates to Employees</h2>
            <form method="post">
                <label for="employeeId">Employee:</label>
                <select name="employeeId" id="employeeId" required>
                    <option value="" disabled selected>Select employee</option>
                    <?php foreach ($employees as $emp): ?>
                        <option value="<?= $emp['employeeId'] ?>"><?= htmlspecialchars($emp['name']) ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="templateId">Schedule Template:</label>
                <select name="templateId" id="templateId" required>
                    <option value="" disabled selected>Select template</option>
                    <?php foreach ($templates as $tpl): ?>
                        <option value="<?= $tpl['templateId'] ?>"><?= htmlspecialchars($tpl['templateName']) ?></option>
                    <?php endforeach; ?>
                </select>

                <button type="submit" name="assign_schedule">Assign Schedule</button>
            </form>

            <h3>Employee Assigned Schedules</h3>
            <div class="table_section">
                <table class="data_table">
                    <thead>
                        <tr>
                            <th>Employee</th>
                            <th>Template Name</th>
                            <th>Work Days</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Break Start</th>
                            <th>Break End</th>
                            <th>Assigned At</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($assignments as $as): ?>
                            <tr>
                                <td><?= htmlspecialchars($as['name']) ?></td>
                                <td><?= htmlspecialchars($as['templateName']) ?></td>
                                <td><?= htmlspecialchars($as['workDays']) ?></td>
                                <td><?= htmlspecialchars($as['timeIn']) ?></td>
                                <td><?= htmlspecialchars($as['timeOut']) ?></td>
                                <td><?= htmlspecialchars($as['breakStart'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($as['breakEnd'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($as['assignedAt']) ?></td>
                                <td>
                                    <a href="?delete_assignment=<?= $as['employeeScheduleId'] ?>" onclick="return confirm('Remove this assignment?')">Remove</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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