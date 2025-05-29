function confirmLogout() {
  return confirm("Are you sure you want to log out?");
}

document.addEventListener("DOMContentLoaded", function () {
  const deleteBtn = document.getElementById("deleteEmployeeBtn");
  const form = deleteBtn ? deleteBtn.closest("form") : null;
  if (deleteBtn && form) {
    deleteBtn.addEventListener("click", function (e) {
      e.preventDefault();
      Swal.fire({
        title: "Are you sure?",
        text: "This will permanently delete the employee.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#d33",
        cancelButtonColor: "#3085d6",
        confirmButtonText: "Yes, delete",
        cancelButtonText: "Cancel",
      }).then((result) => {
        if (result.isConfirmed) {
          // Set the hidden input so PHP knows this is a delete
          document.getElementById("deleteHiddenInput").disabled = false;
          form.submit();
        }
      });
    });
  }
});
