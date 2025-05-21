<?php
$pdo = include '../config/database.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
  header('Location: ../index.php');
  exit();
}

// Optional: Fetch employees list for the dropdown
$stmt = $pdo->query("SELECT employeeId, name FROM employees ORDER BY name ASC");
$employees = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Add Deductions & Benefits</title>
  <link rel="stylesheet" href="../assets/css/deductions_benefits.css">
</head>

<body>
  <div class="container">
    <h1>Add Deductions & Benefits</h1>

    <form action="processAddDeduction.php" method="POST">
      <label for="employee">Select Employee:</label>
      <select name="employeeId" required>
        <option value="">-- Select Employee --</option>
        <?php foreach ($employees as $emp): ?>
          <option value="<?= $emp['employeeId'] ?>"><?= htmlspecialchars($emp['name']) ?></option>
        <?php endforeach; ?>
      </select>

      <label>SSS Deduction (₱):</label>
      <input type="number" name="sss" step="0.01" min="0" required>

      <label>PhilHealth Deduction (₱):</label>
      <input type="number" name="philhealth" step="0.01" min="0" required>

      <label>PAG-IBIG Deduction (₱):</label>
      <input type="number" name="pagibig" step="0.01" min="0" required>

      <label>Other Deductions (₱):</label>
      <input type="number" name="other_deductions" step="0.01" min="0">

      <label>Benefits (Allowances, etc.) (₱):</label>
      <input type="number" name="benefits" step="0.01" min="0">

      <button type="submit">Save</button>
      <a href="deduc&benefits.php" class="btn">Cancel</a>
    </form>
  </div>
</body>

</html>