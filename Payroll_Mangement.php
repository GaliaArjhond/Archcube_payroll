<!DOCTYPE html>
<html lang="en">

<head>
  <link rel="stylesheet" href="assets/css/Payroll_Mangement_Style.css" />
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Payroll Management</title>
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
    <main>
      <div class="top">
        <div class="employeeSelect">
          <h3>Employee:</h3>
          <select name="employee" id="employee">
            <option value="all">All</option>
            <option value="employee1">Employee 1</option>
            <option value="employee2">Employee 2</option>
            <option value="employee3">Employee 3</option>
          </select>
        </div>


        <div class="payperiod">
          <h3>Pay Period:</h3>
          <select name="payPeriod" id="payPeriod">
            <option value="weekly">Weekly</option>
            <option value="bi-weekly">Bi-Weekly</option>
            <option value="monthly">Monthly</option>
          </select>
        </div>

        <!-- Position Selection -->
        <div class="positionSelect">
          <h3>Position:</h3>
          <select name="position" id="position">
            <option value="all">All</option>
            <option value="engineer">Engineer</option>
            <option value="architect">Architect</option>
            <option value="foreman">Foreman</option>
          </select>
        </div>

        <!-- Project Site Selection -->
        <div class="projectSiteSelect">
          <h3>Project Site:</h3>
          <select name="projectSite" id="projectSite">
            <option value="all">All</option>
            <option value="site1">Site 1</option>
            <option value="site2">Site 2</option>
            <option value="site3">Site 3</option>
          </select>
        </div>


        <div class="top_controls">
          <div class="search_bar">
            <input class="search_input" type="text" placeholder="Search..." />
            <button class="search_button">Search</button>
          </div>
        </div>

      </div>


      <div class="employee-table">
        <div class="employee-table-header"></div>
        <table>
          <thead>
            <tr>
              <th>Employee ID</th>
              <th>Worker name</th>
              <th>Day worked</th>
              <th>OT hours</th>
              <th>Absences</th>
              <th>Base Pay</th>
              <th>OT Pay</th>
              <th>SSS</th>
              <th>PhilHealth</th>
              <th>Pag-IBIG</th>
              <th>Withholding Tax</th>
              <th>Advances</th>
              <th>Other Deductions</th>
              <th>Net income</th>
              <th>Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr>

            </tr>
          </tbody>
        </table>
      </div>
    </main>
  </div>
</body>

</html>