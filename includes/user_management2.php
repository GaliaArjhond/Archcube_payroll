<?php
$pdo = include '../config/database.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header('Location: ../index.php');
  exit();
}

// Handle edit submission only on POST with employeeId
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['employeeId'])) {
  // Sanitize and assign variables
  $employeeId = $_POST['employeeId'];
  $name = $_POST['name'] ?? '';
  $phoneNumber = $_POST['phoneNumber'] ?? '';
  $positionId = $_POST['positionId'] ?? null;
  $empStatusId = $_POST['empStatusId'] ?? null;

  try {
    if (!empty($_FILES['profileImage']['name'])) {
      $targetDir = "../uploads/";
      $fileName = basename($_FILES["profileImage"]["name"]);
      $targetFilePath = $targetDir . $fileName;

      if (!move_uploaded_file($_FILES["profileImage"]["tmp_name"], $targetFilePath)) {
        throw new Exception("Failed to upload profile image.");
      }

      $profileImagePath = "uploads/" . $fileName;

      $stmt = $pdo->prepare("UPDATE employees SET name = ?, phoneNumber = ?, positionId = ?, empStatusId = ?, profileImage = ? WHERE employeeId = ?");
      $stmt->execute([$name, $phoneNumber, $positionId, $empStatusId, $profileImagePath, $employeeId]);
    } else {
      $stmt = $pdo->prepare("UPDATE employees SET name = ?, phoneNumber = ?, positionId = ?, empStatusId = ? WHERE employeeId = ?");
      $stmt->execute([$name, $phoneNumber, $positionId, $empStatusId, $employeeId]);
    }

    if (isset($_SESSION['userId'])) {
      $actionTypeId = 4;
      $logStmt = $pdo->prepare("INSERT INTO systemLogs (userId, actionTypeId, timestamp) VALUES (?, ?, NOW())");
      $logStmt->execute([$_SESSION['userId'], $actionTypeId]);
    } else {
      error_log("System log insert skipped: userId not found in session.");
    }

    echo "<script>alert('Employee updated successfully.'); window.location.href='user_management2.php';</script>";
    exit();
  } catch (PDOException $e) {
    error_log("Database error: " . $e->getMessage());
    echo "<script>alert('Database error occurred. Please try again later.'); window.location.href='user_management2.php';</script>";
    exit();
  } catch (Exception $e) {
    error_log("Error: " . $e->getMessage());
    echo "<script>alert('Error: " . addslashes($e->getMessage()) . "'); window.location.href='user_management2.php';</script>";
    exit();
  }
}

// Fetch dropdown data for form selects
$stmtPositions = $pdo->query("SELECT positionId, positionName FROM position ORDER BY positionName");
$positions = $stmtPositions->fetchAll(PDO::FETCH_ASSOC);

$stmtStatuses = $pdo->query("SELECT empStatusId, empStatusName FROM empStatus ORDER BY empStatusName");
$empStatuses = $stmtStatuses->fetchAll(PDO::FETCH_ASSOC);

// Filtering logic for employee list display
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter = $_GET['filter'] ?? 'all';

$where = [];
$params = [];

if ($search !== '') {
  $where[] = "(e.name LIKE ? OR e.employeeId LIKE ? OR e.phoneNumber LIKE ? OR p.positionName LIKE ? OR es.empStatusName LIKE ?)";
  for ($i = 0; $i < 5; $i++) $params[] = "%$search%";
}

if ($filter === 'active') {
  $where[] = "es.empStatusName = 'Active'";
} elseif ($filter === 'inactive') {
  $where[] = "es.empStatusName = 'Inactive'";
}

$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';
$stmt = $pdo->prepare("
  SELECT e.employeeId, e.name, e.phoneNumber, e.profileImage, p.positionName, es.empStatusName, e.positionId, e.empStatusId
  FROM employees e
  LEFT JOIN position p ON e.positionId = p.positionId
  LEFT JOIN empStatus es ON e.empStatusId = es.empStatusId
  $whereSql
  ORDER BY e.employeeId DESC
");
$stmt->execute($params);
?>


<!DOCTYPE html>
<html lang="en">

<head>
  <link rel="stylesheet" href="../assets/css/user_management_style.css" />
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>User Management</title>
</head>

<body>
  <div class="side_bar">
    <h1>Archcube Payroll</h1>
    <div class="side_bar_container">
      <div class="side_bar_item"><a href="../includes/dashboard.php">Dashboard</a></div>
      <div class="side_bar_item"><a href="user_management2.php">Employee Management</a></div>
      <div class="side_bar_item"><a href="attendance.php">Attendance</a></div>
      <div class="side_bar_item"><a href="Payroll_Mangement.php">Payroll Management</a></div>
      <div class="side_bar_item"><a href="deduc&benefits.php">Deductions & Benefits Management</a></div>
      <div class="side_bar_item"><a href="payslip.php">Payslip Generator</a></div>
      <div class="side_bar_item"><a href="reports.php">Summary Reports</a></div>
      <div class="side_bar_item"><a href="setting.php">Settings</a></div>
      <div class="side_bar_item">
        <a href="../includes/logout.php" class="logout" onclick="return confirmLogout();">Log Out</a>
      </div>
    </div>
  </div>

  <div class="main_content">
    <div class="top_controls">
      <form method="GET" class="search_filter_group">
        <div class="search_bar">
          <input type="text" name="search" placeholder="Search..." class="search_input" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" />
          <button type="submit" class="search_button">Search</button>
        </div>
        <div class="filter_section">
          <label for="filter">Filter by:</label>
          <select id="filter" name="filter" class="filter_select">
            <option value="all" <?= (($_GET['filter'] ?? '') === 'all') ? 'selected' : '' ?>>All</option>
            <option value="active" <?= (($_GET['filter'] ?? '') === 'active') ? 'selected' : '' ?>>Active</option>
            <option value="inactive" <?= (($_GET['filter'] ?? '') === 'inactive') ? 'selected' : '' ?>>Inactive</option>
          </select>
        </div>
      </form>
      <div class="top_control_functions">
        <button type="button" onclick="clearSearchFilter()" class="search_button" style="margin-right:8px;">Clear</button>
        <button type="button" onclick="window.print()" class="search_button">Print List</button>
      </div>
      <div class="add_button">
        <a href="addEmp.php" class="add_employee_button">Add Employee</a>
      </div>
    </div>

    <div class="table_section">
      <table class="data_table">
        <thead>
          <tr>
            <th>Profile</th>
            <th>ID</th>
            <th>Name</th>
            <th>Contact</th>
            <th>Position</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $hasRows = false;
          while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $hasRows = true;

            // Prepare JS safe strings for data attributes
            $jsName = htmlspecialchars(addslashes($row['name']));
            $jsPhone = htmlspecialchars(addslashes($row['phoneNumber']));
            $jsProfileImage = htmlspecialchars($row['profileImage']);

            echo "<tr>";
            echo "<td><img src='../" . htmlspecialchars($row['profileImage']) . "' alt='Profile' width='40' height='40' style='border-radius:50%;'></td>";
            echo "<td>" . htmlspecialchars($row['employeeId']) . "</td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['phoneNumber']) . "</td>";
            echo "<td>" . htmlspecialchars($row['positionName'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($row['empStatusName'] ?? 'N/A') . "</td>";
            echo '<td>
                  <button class="editbutton" 
                    onclick="openOverlay(\'' . htmlspecialchars($row['employeeId']) . '\', \'' . $jsName . '\', \'' . $jsPhone . '\', \'' . ($row['positionId'] ?? '') . '\', \'' . ($row['empStatusId'] ?? '') . '\')">
                    Edit
                  </button>
                  <button class="deletebutton" onclick="if(confirm(\'Are you sure you want to delete this employee?\')) { window.location.href=\'deleteEmp.php?id=' . urlencode($row['employeeId']) . '\'; }">Delete</button>
                </td>';

            echo "</tr>";
          }
          if (!$hasRows) {
            echo "<tr><td colspan='7' style='text-align:center;'>No employees found.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Edit Employee Overlay -->
  <form method="POST" enctype="multipart/form-data">
    <div id="editOverlay" class="overlay">
      <div class="overlay-content">
        <span class="close-btn" onclick="closeOverlay()">&times;</span>
        <h3>Edit Employee</h3>

        <input type="hidden" id="employeeId" name="employeeId">

        <label for="name"><b>Name</b></label>
        <input type="text" id="name" name="name" required style="width:100%; margin-bottom:8px;">

        <label for="phoneNumber"><b>Phone Number</b></label>
        <input type="text" id="phoneNumber" name="phoneNumber" required style="width:100%; margin-bottom:8px;">

        <label for="positionId"><b>Position</b></label>
        <select id="positionId" name="positionId" required style="width:100%; margin-bottom:8px;">
          <option value="">Select Position</option>
          <?php foreach ($positions as $pos): ?>
            <option value="<?= htmlspecialchars($pos['positionId']) ?>"><?= htmlspecialchars($pos['positionName']) ?></option>
          <?php endforeach; ?>
        </select>

        <label for="empStatusId"><b>Status</b></label>
        <select id="empStatusId" name="empStatusId" required style="width:100%; margin-bottom:8px;">
          <option value="">Select Status</option>
          <?php foreach ($empStatuses as $status): ?>
            <option value="<?= htmlspecialchars($status['empStatusId']) ?>"><?= htmlspecialchars($status['empStatusName']) ?></option>
          <?php endforeach; ?>
        </select>

        <label for="profileImage"><b>Profile Image</b></label>
        <input type="file" id="profileImage" name="profileImage" accept="image/*" style="width:100%; margin-bottom:8px;">

        <input type="submit" value="Save Changes" style="width:100%; padding:8px;">
      </div>
    </div>
  </form>



  <script src="../assets/js/empManagement.js"></script>
</body>

</html>