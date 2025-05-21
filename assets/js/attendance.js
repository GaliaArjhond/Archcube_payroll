function closePopup() {
  document.getElementById("popupMessage").style.display = "none";
}

function showPopup(message) {
  document.getElementById("popupText").textContent = message;
  document.getElementById("popupMessage").style.display = "flex";
}

function confirmLogout() {
  return confirm("Are you sure you want to log out?");
}

function printAttendance() {
  const tableSection = document.querySelector(".table_section");
  if (!tableSection) {
    alert("Attendance table not found.");
    return;
  }
  const printWindow = window.open("", "", "height=600,width=800");
  printWindow.document.write("<html><head><title>Print Attendance</title>");
  printWindow.document.write("<style>");
  printWindow.document.write(
    "table { width: 100%; border-collapse: collapse; }"
  );
  printWindow.document.write(
    "th, td { border: 1px solid black; padding: 8px; }"
  );
  printWindow.document.write("</style></head><body>");
  printWindow.document.write("<h2>Attendance Records</h2>");
  printWindow.document.write(tableSection.innerHTML);
  printWindow.document.write("</body></html>");
  printWindow.document.close();

  printWindow.onload = function () {
    printWindow.focus();
    printWindow.print();
    printWindow.close();
  };
}

// Event listeners
document.addEventListener("DOMContentLoaded", function () {
  // RFID input auto-submit
  var rfidInput = document.getElementById("rfidCode");
  if (rfidInput) {
    rfidInput.addEventListener("input", function () {
      if (this.value.length >= 10) {
        this.form.submit();
      }
    });
  }

  // Auto-submit on filter change
  [
    "show_ent",
    "view_all",
    "view_today",
    "view_week1",
    "view_week2",
    "view_month",
    "view_year",
    "from_date",
    "to_date",
  ].forEach((id) => {
    const el = document.getElementById(id);
    if (el)
      el.addEventListener("change", () =>
        document.getElementById("attendanceForm").submit()
      );
  });

  var searchInput = document.getElementById("search_input");
  if (searchInput) {
    searchInput.addEventListener("keypress", function (e) {
      if (e.key === "Enter") {
        document.getElementById("attendanceForm").submit();
      }
    });
  }

  // Show popup if needed
  if (window.popupMessageText) {
    showPopup(window.popupMessageText);
  }
});
