function confirmLogout() {
  return confirm("Are you sure you want to log out?");
}

function printLogs() {
  const tableSection = document.querySelector(".table_logs");
  if (!tableSection) {
    alert("Logs table not found.");
    return;
  }
  const today = new Date();
  const dateString = today.toLocaleDateString();
  const printTitle = "Archcube System Logs - " + dateString;

  const printWindow = window.open("", "", "height=600,width=800");
  printWindow.document.write("<html><head><title>" + printTitle + "</title>");
  printWindow.document.write("<style>");
  printWindow.document.write(
    "table { width: 100%; border-collapse: collapse; }"
  );
  printWindow.document.write(
    "th, td { border: 1px solid black; padding: 8px; }"
  );
  printWindow.document.write("</style></head><body>");
  printWindow.document.write(tableSection.innerHTML);
  printWindow.document.write("</body></html>");
  printWindow.document.close();

  printWindow.onload = function () {
    printWindow.focus();
    printWindow.print();
    printWindow.close();
  };
}
