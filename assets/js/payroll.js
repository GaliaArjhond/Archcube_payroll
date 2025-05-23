function confirmLogout() {
  return confirm("Are you sure you want to log out?");
}

function openOverlay() {
  document.getElementById("payrollOverlay").style.display = "flex";
}

function closeOverlay() {
  document.getElementById("payrollOverlay").style.display = "none";
}

// Close overlay when clicking outside the modal content
document.addEventListener("DOMContentLoaded", function () {
  const overlay = document.getElementById("payrollOverlay");
  if (overlay) {
    overlay.addEventListener("click", function (e) {
      if (e.target === overlay) {
        closeOverlay();
      }
    });
  }
});
