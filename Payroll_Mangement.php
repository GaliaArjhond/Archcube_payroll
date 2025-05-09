<!DOCTYPE html>
<!DOCTYPE html>
<html lang="en">

<head>
  <link rel="stylesheet" href="dashboardStyle.css" />
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard</title>
</head>

<body>
  <header>
    <a href="">Archcube Payroll</a>
    <div class="logout">
      <a href="" class=" ">Logout</a>
    </div>
  </header>
  <aside>
    <ul>
      <li><a href="">Dashboard</a></li>
      <li><a href="">User Management</a></li>
      <li><a href="">Payroll Management</a></li>
      <li><a href="">Advance Payment</a></li>
      <li><a href="">Summary Report</a></li>
      <li><a href="">Setting</a></li>
    </ul>
  </aside>

  <main>
    <div class="top">
      <!-- Employee Selection -->
      <div class="employeeSelect">
        <h3>Employee:</h3>
        <select name="employee" id="employee">
          <option value="all">All</option>
          <option value="employee1">Employee 1</option>
          <option value="employee2">Employee 2</option>
          <option value="employee3">Employee 3</option>
        </select>
      </div>

      <!-- Pay Period Selection -->
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

      <!-- Search Bar -->
      <div class="search">
        <input type="text" placeholder="Search..." />
        <button type="submit">Search</button>
      </div>
    </div>

    <div class="attendance">
      <div class="attendance-header"></div>
      <table>
        <thead>
          <tr>
            <th></th>
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
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
          <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
          <tr>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
          </tr>
        </tbody>
      </table>
    </div>
  </main>
</body>

</html>