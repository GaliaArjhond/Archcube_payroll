<?php
$pdo = include '../config/database.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header('Location: ../index.php');
  exit();
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

$where = [];
$params = [];

if ($search !== '') {
  $where[] = "(e.name LIKE ? OR e.employeeId LIKE ? OR e.phoneNumber LIKE ?)";
  $params[] = "%$search%";
  $params[] = "%$search%";
  $params[] = "%$search%";
}

if ($filter === 'active') {
  $where[] = "es.empStatusName = 'Active'";
} elseif ($filter === 'inactive') {
  $where[] = "es.empStatusName = 'Inactive'";
}

$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

$stmt = $pdo->prepare("
  SELECT e.employeeId, e.name, e.phoneNumber, e.profileImage, p.positionName, es.empStatusName
  FROM employees e
  LEFT JOIN position p ON e.positionId = p.positionId
  LEFT JOIN empStatus es ON e.empStatusId = es.empStatusId
  $whereSql
  ORDER BY e.employeeId DESC
");
$stmt->execute($params);
?>




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
      <div class="side_bar_item">
        <a href="../includes/dashboard.php">Dashboard</a>
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
        <a href="../includes/logout.php" class="logout">Log Out</a>
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
            echo "<tr>";
            echo "<td><img src='../" . htmlspecialchars($row['profileImage']) . "' alt='Profile' width='40' height='40' style='border-radius:50%;'></td>";
            echo "<td>" . htmlspecialchars($row['employeeId']) . "</td>";
            echo "<td>" . htmlspecialchars($row['name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['phoneNumber']) . "</td>";
            echo "<td>" . htmlspecialchars($row['positionName'] ?? 'N/A') . "</td>";
            echo "<td>" . htmlspecialchars($row['empStatusName'] ?? 'N/A') . "</td>";
            echo "<td>
                    <a href='editEmp.php?id=" . $row['employeeId'] . "'>Edit</a> |
                    <a href='deleteEmp.php?id=" . $row['employeeId'] . "' onclick=\"return confirm('Are you sure?')\">Delete</a>
                  </td>";
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
</body>

</html>