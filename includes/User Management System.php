<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>ARCHCUBE PAYROLL Dashboard</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #cbe0f7;
        }
        .sidebar {
            width: 270px;
            height: 100vh;
            background-color: #074799;
            color: white;
            padding: 0;
            position: fixed;
            display: flex;
            flex-direction: column;
            top: 0;
            left: 0;
            z-index: 10;
        }
        .sidebar h3 {
            text-align: left;
            margin-bottom: 60px;
            margin-top: 30px;
            padding: 10px 30px;
            font-size: 1.3em;
            letter-spacing: 1px;
            font-weight: 600;
        }
        .sidebar-buttons {
            display: flex;
            flex-direction: column;
            gap: 20px;
            padding: 0 30px;
        }
        .sidebar-buttons button {
            padding: 16px 10px;
            background-color: #133158;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 8px;
            font-size: 18px;
            text-align: left;
            font-weight: 500;
            transition: background 0.2s;
        }
        .sidebar-buttons button:hover,
        .sidebar-buttons button.active {
            background-color: #0c2950;
        }
        .logout-button {
            margin: 40px 30px 20px 30px;
            padding: 14px;
            background-color: #e74c3c;
            border: none;
            color: white;
            cursor: pointer;
            border-radius: 8px;
            font-size: 18px;
            text-align: left;
            font-weight: 500;
            transition: background 0.2s;
        }
        .logout-button:hover {
            background-color: #c0392b;
        }
        .main-content {
            margin-left: 270px;
            padding: 30px 40px;
            min-height: 100vh;
            background-color: #cbe0f7;
        }
        .top-bar {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            justify-content: space-between; /* Distribute items evenly */
        }
        .top-bar-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .filter-label {
            font-weight: bold;
            margin-right: 8px;
            font-size: 18px;
        }
        .top-bar-right {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .add-worker-btn {
            background: #18b187;
            color: #fff;
            border: none;
            padding: 12px 32px;
            border-radius: 8px;
            font-size: 18px;
            cursor: pointer;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .add-worker-btn:hover {
            background: #14976f;
        }
        /* Style for the filter dropdown */
        .filter-dropdown {
            position: relative;
            display: inline-block;
        }
        .filter-dropdown-content {
            position: absolute;
            background-color: #f9f9f9;
            min-width: 200px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 4px;
            border: 1px solid #ccc;
            display: none;
            padding: 0;
            margin: 0;
            list-style: none;
        }
        /* Style for the list items inside the dropdown */
        .filter-dropdown-content li {
            margin: 0; /* Remove margin */
            padding: 0; /* Remove padding */
        }
        /* Style for the links inside the dropdown */
        .filter-dropdown-content li a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            margin: 0;
        }
        /* Style for the button to trigger the main filter dropdown */
        .filter-button {
            background-color: #f1f1f1;
            color: #333;
            padding: 10px 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            min-width: 150px;
            text-align: left;
        }
        /* Style for the links inside the dropdown */
        .filter-dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
            margin: 0; /* Remove margin */
        }
        /* Style for the hover effect on links */
        .filter-dropdown-content a:hover {
            background-color: #ddd;
        }
        /* Style to show the dropdown content */
        .show {
            display: block;
        }
        /* Style for sub-menu items */
        .sub-menu {
            padding-left: 20px; /* Indent sub-menu items */
        }
        /* Style for sub-menu items */
        .sub-menu {
            padding-left: 20px; /* Indent sub-menu items */
        }
        /* Style for the sub-filter dropdown */
        .sub-filter-dropdown {
            position: relative;
            display: inline-block;
        }
        .sub-filter-dropdown {
            position: relative;
            display: inline-block;
            margin-right: 10px; /* Add some spacing between sub-filters */
        }
        /* Style for the sub-filter button */
        .sub-filter-button {
            background-color: #f1f1f1;
            color: #333;
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
            min-width: 120px;
        }
        .sub-filter-dropdown-content {
            position: absolute;
            background-color: #f9f9f9;
            min-width: 150px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 4px;
            border: 1px solid #ccc;
            display: none;
        }
        .sub-filter-dropdown-content a {
            color: black;
            padding: 10px 12px;
            text-decoration: none;
            display: block;
        }
        .sub-filter-dropdown-content a:hover {
            background-color: #ddd;
        }
        /* Style for the input fields */
        .filter-input {
            margin-top: 5px; /* Add some spacing between the label and input */
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            width: 100%; /* Make the inputs fill the width of their container */
        }
        .search-box {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .search-btn {
            background: #18b187;
            border: none;
            color: #fff;
            padding: 10px 32px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 18px;
            font-weight: bold;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .search-btn:hover {
            background: #14976f;
        }
        .search-input {
            padding: 10px 18px;
            border-radius: 5px;
            border: 1px solid #888;
            font-size: 18px;
            min-width: 220px;
            background: #f8f8f8;
        }
        .data-table-container {
            display: flex;
            justify-content: center;
            margin-top: 10px;
        }
        .data-table {
            width: 90%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 8px 8px 0 0;
            overflow: hidden;
            margin: 0 auto;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .data-table th, .data-table td {
            padding: 10px 8px;
            border: 1px solid #b0b0b0;
            text-align: center;
            font-size: 15px;
        }
        .data-table th {
            background: #1759a4;
            color: #fff;
            font-weight: bold;
        }
        .user-icon {
            width: 24px;
            height: 24px;
            background: #cfd8dc url('https://img.icons8.com/ios-filled/24/000000/user.png') center center no-repeat;
            border-radius: 50%;
            display: inline-block;
            vertical-align: middle;
            background-size: 60% 60%;
        }
        .edit-delete-buttons {
            display: flex;
            justify-content: center;
            gap: 5px;
        }
        .edit-button, .delete-button {
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            border: none;
        }
        .edit-button {
            background-color: #5dade2;
            color: white;
        }
        .delete-button {
            background-color: #e74c3c;
            color: white;
        }
        /*Adding this css here to keep code simpler*/
        .row{
            display: flex;
            gap: 10px
        }
        /*Adding this css here to keep code simpler*/
        .second-row{
            margin-left:10px
        }
        /* Style for the secondary filters */
        .secondary-filters {
            display: flex;
            align-items: center;
            gap: 10px; /* Adjust as needed for spacing */
        }
    </style>
    <script>
        function editRow(button) {
            let row = button.parentNode.parentNode;
            let cells = row.querySelectorAll('td:not(:last-child)'); // Exclude the Action column

            for (let i = 1; i < cells.length; i++) { // Skip the first cell (user icon)
                let cell = cells[i];
                let text = cell.innerText;
                cell.innerHTML = '<input type="text" value="' + text + '">';
            }

            // Change Edit button to Save
            button.innerText = "Save";
            button.onclick = function() { saveRow(this); };

            // Add a Cancel button
            let cancelButton = document.createElement('button');
            cancelButton.innerText = "Cancel";
            cancelButton.className = "delete-button";
            cancelButton.onclick = function() { cancelEdit(this); };
            button.parentNode.appendChild(cancelButton);
        }

        function saveRow(button) {
            let row = button.parentNode.parentNode;
            let cells = row.querySelectorAll('td:not(:last-child)');

            for (let i = 1; i < cells.length; i++) {
                let cell = cells[i];
                let input = cell.querySelector('input');
                cell.innerHTML = input.value;
            }

            // Change Save button back to Edit
            button.innerText = "Edit";
            button.onclick = function() { editRow(this); };

            // Remove Cancel button
            button.parentNode.removeChild(button.parentNode.lastChild);
        }

        function cancelEdit(button) {
            let row = button.parentNode.parentNode;
            let cells = row.querySelectorAll('td:not(:last-child)');

            // For simplicity, reload the row data from the original table (or store it in a data attribute)
            // In a real application, you would need to store the original data before editing
            // This example just reloads the page to revert changes

            window.location.reload();
        }

        function deleteRow(button) {
            let row = button.parentNode.parentNode;
            row.parentNode.removeChild(row);
        }

        function addRow() {
            let table = document.querySelector('.data-table tbody');
            let newRow = table.insertRow();

            // Add cells to the new row (adjust the number based on your table structure)
            newRow.innerHTML = `
        <td><span class="user-icon"></span></td>
        <td>Table Body</td>
        <td>Table Body</td>
        <td>Table Body</td>
        <td>$0</td>
        <td>Table Body</td>
        <td>Table Body</td>
        <td>Table Body</td>
        <td>Table Body</td>
        <td class="edit-delete-buttons">
          <button class="edit-button" onclick="editRow(this)">Edit</button>
          <button class="delete-button" onclick="deleteRow(this)">Delete</button>
        </td>
      `;
        }
        /* Function to toggle the dropdown */
        function toggleDropdown(dropdownId) {
            document.getElementById(dropdownId).classList.toggle("show");
        }
          /* Function to toggle the sub-filter dropdown */
        function toggleSubFilterDropdown(dropdownId) {
            document.getElementById(dropdownId).classList.toggle("show");
        }
          /* Function to handle main filter changes*/
        function handleMainFilterChange(filterValue) {
            console.log('Selected Main Filter:', filterValue);
            // Hide all sub-filter containers initially
            document.getElementById('allUsersSubFilter').style.display = 'none';
            document.getElementById('positionsSubFilter').style.display = 'none';
            document.getElementById('hiredDateInput').style.display = 'none';
            document.getElementById('contractEndDateInput').style.display = 'none';

            // Show the appropriate sub-filter based on the main filter selection
            if (filterValue === 'allUsers') {
                document.getElementById('allUsersSubFilter').style.display = 'inline-block';
            } else if (filterValue === 'position') {
                document.getElementById('positionsSubFilter').style.display = 'inline-block';
            } else if (filterValue === 'hiredDate') {
                document.getElementById('hiredDateInput').style.display = 'inline-block';
            } else if (filterValue === 'contractEndDate') {
                document.getElementById('contractEndDateInput').style.display = 'inline-block';
            }
        }
         /* Close the main filter dropdown if the user clicks outside of it */
        window.onclick = function(event) {
            if (!event.target.matches('.filter-button')) {
                var dropdowns = document.getElementsByClassName("filter-dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
        };
    </script>
</head>
<body>
<div class="sidebar">
    <h3>ARCHCUBE PAYROLL</h3>
    <div class="sidebar-buttons">
        <button class="active">Dashboard</button>
        <button>User Management</button>
        <button>Payroll Management</button>
        <button>Advance Payment</button>
        <button>Summary Report</button>
        <button>Settings</button>
    </div>
    <button class="logout-button">Log out</button>
</div>
<div class="main-content">
    <div class="top-bar">
        <div class="top-bar-left">
            <button class="add-worker-btn" onclick="addRow()">Add Worker</button>
                <!-- Main Filter dropdown -->
            <div class="filter-dropdown">
                <button onclick="toggleDropdown('mainFilterDropdown')" class="filter-button">
                    Filter ▼
                </button>
                <ul id="mainFilterDropdown" class="filter-dropdown-content">
                    <li>
                        <a href="#" onclick="handleMainFilterChange('allUsers')">All Users</a>
                    </li>
                    <li>
                        <a href="#" onclick="handleMainFilterChange('position')">Position</a>
                    </li>
                    <li>
                        <a href="#" onclick="handleMainFilterChange('hiredDate')">Hired Date</a>
                    </li>
                    <li>
                        <a href="#" onclick="handleMainFilterChange('contractEndDate')">Contract End Date</a>
                    </li>
                </ul>
            </div>
               <!-- Secondary filters (initially hidden) -->
        <div class="secondary-filters">
            <!-- All Users Sub-filter -->
            <div id="allUsersSubFilter" class="sub-filter-dropdown" style="display: none;">
                <button onclick="toggleSubFilterDropdown('allUsersDropdown')" class="sub-filter-button">
                    All Users ▼
                </button>
                <div id="allUsersDropdown" class="sub-filter-dropdown-content">
                    <a href="#">Active</a>
                    <a href="#">Inactive</a>
                </div>
            </div>

            <!-- Positions Sub-filter -->
            <div id="positionsSubFilter" class="sub-filter-dropdown" style="display: none;">
                <button onclick="toggleSubFilterDropdown('positionsDropdown')" class="sub-filter-button">
                    Positions ▼
                </button>
                <div id="positionsDropdown" class="sub-filter-dropdown-content">
                    <a href="#">Employee</a>
                    <a href="#">Engineer</a>
                    <a href="#">Architect</a>
                </div>
            </div>

            <!-- Hired Date Input -->
            <div id="hiredDateInput" class="sub-filter-dropdown" style="display: none;">
                <label for="hiredDate">Hired Date:</label>
                <input type="datetime-local" id="hiredDate" class="filter-input">
            </div>

            <!-- Contract End Date Input -->
            <div id="contractEndDateInput" class="sub-filter-dropdown" style="display: none;">
                <label for="contractEndDate">Contract End Date:</label>
                <input type="datetime-local" id="contractEndDate" class="filter-input">
            </div>
        </div>
        </div>
        <div class="top-bar-right">
            <div class="search-box">
                <button class="search-btn">Search</button>
                <input type="text" class="search-input" placeholder="" />
            </div>
        </div>
    </div>
    <div class="data-table-container">
        <table class="data-table">
            <thead>
            <tr>
                <th></th>
                <th>Name</th>
                <th>Contact</th>
                <th>Position</th>
                <th>Base Salary</th>
                <th>Upcoming Payroll</th>
                <th>Attendance</th>
                <th>Status</th>
                <th>Role</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <!-- Example rows, repeat as needed -->
            <tr>
                <td><span class="user-icon"></span></td>
                <td>Table Body</td>
                <td>Table Body</td>
                <td>Table Body</td>
                <td>$50,000</td>
                <td>Table Body</td>
                <td>Table Body</td>
                <td>Table Body</td>
                <td>Table Body</td>
                <td class="edit-delete-buttons">
                    <button class="edit-button" onclick="editRow(this)">Edit</button>
                    <button class="delete-button" onclick="deleteRow(this)">Delete</button>
                </td>
            </tr>
            <tr>
                <td><span class="user-icon"></span></td>
                <td>Table Body</td>
                <td>Table Body</td>
                <td>Table Body</td>
                <td>$60,000</td>
                <td>Table Body</td>
                <td>Table Body</td>
                <td>Table Body</td>
                <td>Table Body</td>
                <td class="edit-delete-buttons">
                    <button class="edit-button" onclick="editRow(this)">Edit</button>
                    <button class="delete-button" onclick="deleteRow(this)">Delete</button>
                </td>
            </tr>
            <tr>
                <td><span class="user-icon"></span></td>
                <td>Table Body</td>
                <td>Table Body</td>
                <td>Table Body</td>
                <td>$70,000</td>
                <td>Table Body</td>
                <td>Table Body</td>
                <td>Table Body</td>
                <td>Table Body</td>
                <td class="edit-delete-buttons">
                    <button class="edit-button" onclick="editRow(this)">Edit</button>
                    <button class="delete-button" onclick="deleteRow(this)">Delete</button>
                </td>
            </tr>
            <tr>
                <td><span class="user-icon"></span></td>
                <td>Table Body</td>
                <td>Table Body</td>
                <td>Table Body</td>
                <td>$80,000</td>
                <td>Table Body</td>
                <td>Table Body</td>
                <td>Table Body</td>
                <td>Table Body</td>
                <td class="edit-delete-buttons">
                    <button class="edit-button" onclick="editRow(this)">Edit</button>
                    <button class="delete-button" onclick="deleteRow(this)">Delete</button>
                </td>
            </tr>
            <tr>
                <td><span class="user-icon"></span></td>
                <td>Table Body</td>
                <td>Table Body</td>
                <td>Table Body</td>
                <td>$90,000</td>
                <td>Table Body</td>
                <td>Table Body</td>
                <td>Table Body</td>
                <td>Table Body</td>
                <td class="edit-delete-buttons">
                    <button class="edit-button" onclick="editRow(this)">Edit</button>
                    <button class="delete-button" onclick="deleteRow(this)">Delete</button>
                </td>
            </tr>
            <tr>
                <td><span class="user-icon"></span></td>
                <td>Table Body</td>
                <td>Table Body</td>
                <td>Table Body</td>
                <td>$100,000</td>
                <td>Table Body</td>
                <td>Table Body</td>
                <td>Table Body</td>
                <td>Table Body</td>
                <td class="edit-delete-buttons">
                    <button class="edit-button" onclick="editRow(this)">Edit</button>
                    <button class="delete-button" onclick="deleteRow(this)">Delete</button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>
