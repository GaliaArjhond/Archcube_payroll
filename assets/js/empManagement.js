function confirmLogout() {
  return confirm("Are you sure you want to log out?");
}

function clearSearchFilter() {
  // This resets the URL by navigating back to the base page (no filters/search)
  window.location.href = "user_management2.php";
}

function openOverlay(employeeId, name, phone, positionId, empStatusId) {
  console.log("Employee ID received:", employeeId); // Debug
  document.getElementById("employeeId").value = employeeId;
  document.getElementById("name").value = name;
  document.getElementById("phoneNumber").value = phone;
  document.getElementById("positionId").value = positionId;
  document.getElementById("empStatusId").value = empStatusId;
  document.getElementById("editOverlay").style.display = "flex";
}

function closeOverlay() {
  document.getElementById("editOverlay").style.display = "none";
}
