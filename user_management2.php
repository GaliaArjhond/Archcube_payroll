<html lang="en">

<head>
  <link rel="stylesheet" href="assets/css/user_management_style.css" />
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>User Management</title>
</head>

<body>
  <div class="side_bar">
    <h1>Archcube Payroll</h1>
    <div class="side_bar_container">
      <div class="side_bar_item">
        <a href="dashboard.html">Dashboard</a>
      </div>
      <div class="side_bar_item">
        <a href="">Employee Management</a>
      </div>
      <div class="side_bar_item">
        <a href="">Attendance</a>
      </div>
      <div class="side_bar_item">
        <a href="">Payroll Management</a>
      </div>
      <div class="side_bar_item">
        <a href="">Deductions & Benefits Management</a>
      </div>
      <div class="side_bar_item">
        <a href="">Payslip Generator</a>
      </div>
      <div class="side_bar_item">
        <a href="">Summary Reports</a>
      </div>
      <div class="side_bar_item">
        <a href="">Settings</a>
      </div>
      <div class="side_bar_item">
        <a href="" class="logout">Log Out</a>
      </div>
    </div>
  </div>

  <div class="main_content">
    <div class="top_controls">
      <div class="search_filter_group">
        <div class="search_bar">
          <input type="text" placeholder="Search..." class="search_input" />
          <button class="search_button">Search</button>
        </div>
        <div class="filter_section">
          <label for="filter">Filter by:</label>
          <select id="filter" class="filter_select">
            <option value="all">All</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
      </div>
      <div class="add_button">
        <a href="addEmp.php" class="add_employee_button">
          Add Employee
        </a>
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
            <th>Basic Salary</th>
            <th>Upcoming Payroll</th>
            <th>Attendance</th>
            <th>Status</th>
            <th>Actions</th>
          </tr>
        </thead>
      </table>
    </div>
  </div>
</body>

</html>