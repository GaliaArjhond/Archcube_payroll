<?php
// deduc_benefits_ajax.php
$pdo = include '../config/database.php';
session_start();

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

if (!isset($_GET['employee_id']) || empty($_GET['employee_id'])) {
    echo json_encode(['error' => 'No employee ID provided']);
    exit;
}

$employee_id = $_GET['employee_id'];

// Fetch attendance
$stmt = $pdo->prepare("SELECT leave_credits, late_minutes, absences FROM attendance WHERE employee_id = ? ORDER BY date DESC LIMIT 1");
$stmt->execute([$employee_id]);
$attendance = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

// Fetch government contributions
$stmt = $pdo->prepare("SELECT type, employee_share, employer_share, status FROM government_contributions WHERE employee_id = ?");
$stmt->execute([$employee_id]);
$govData = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch custom deductions
$stmt = $pdo->prepare("SELECT name, amount, status, balance FROM custom_deductions WHERE employee_id = ?");
$stmt->execute([$employee_id]);
$deductions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch latest advance
$stmt = $pdo->prepare("SELECT advance_amount, deduct_amount FROM advances WHERE employee_id = ? ORDER BY created_at DESC LIMIT 1");
$stmt->execute([$employee_id]);
$advance = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

// Fetch last 5 payroll summaries
$stmt = $pdo->prepare("SELECT payroll_period, basic_salary, total_deductions, net_pay, status FROM payroll WHERE employee_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->execute([$employee_id]);
$payrollSummary = $stmt->fetchAll(PDO::FETCH_ASSOC);

header('Content-Type: application/json');
echo json_encode([
    'attendance' => $attendance,
    'govData' => $govData,
    'deductions' => $deductions,
    'advance' => $advance,
    'payrollSummary' => $payrollSummary,
]);
