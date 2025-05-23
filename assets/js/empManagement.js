function confirmLogout() {
  return confirm("Are you sure you want to log out?");
}

function clearSearchFilter() {
  // This resets the URL by navigating back to the base page (no filters/search)
  window.location.href = "user_management2.php";
}

function openOverlay(
  employeeId,
  name,
  rfidCodeId,
  genderId,
  birthDate,
  civilStatusId,
  phoneNumber,
  email,
  address,
  hiredDate,
  role,
  positionId,
  empStatusId,
  payrollPeriodID // <-- updated parameter name
) {
  document.getElementById("employeeId").value = employeeId;
  document.getElementById("name").value = name;
  document.getElementById("rfidCodeId").value = rfidCodeId;
  document.getElementById("genderId").value = genderId;
  document.getElementById("birthDate").value = birthDate;
  document.getElementById("civilStatusId").value = civilStatusId;
  document.getElementById("phoneNumber").value = phoneNumber;
  document.getElementById("email").value = email;
  document.getElementById("address").value = address;
  document.getElementById("hiredDate").value = hiredDate;
  document.getElementById("role").value = role;
  document.getElementById("positionId").value = positionId;
  document.getElementById("empStatusId").value = empStatusId;
  document.getElementById("payrollPeriodID").value = payrollPeriodID; // <-- updated field
  document.getElementById("editOverlay").style.display = "flex";
}

function closeOverlay() {
  document.getElementById("editOverlay").style.display = "none";
}
