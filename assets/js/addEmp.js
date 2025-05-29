document.addEventListener("DOMContentLoaded", function () {
  if (window.successMsg) {
    // Reset the form if success
    const form = document.querySelector("form");
    if (form) form.reset();

    // Optionally, reset the photo preview
    const photoPreview = document.getElementById("photoPreview");
    if (photoPreview) photoPreview.src = "assets/img/default-user.png";
  }
});

function previewPhoto(event) {
  try {
    const [file] = event.target.files;
    if (file) {
      document.getElementById("photoPreview").src = URL.createObjectURL(file);
    }
  } catch (error) {
    alert("Failed to preview photo: " + error.message);
  }
}

function confirmLogout() {
  return confirm("Are you sure you want to log out?");
}

window.onload = function () {
  if (window.successMsg) {
    alert(window.successMsg);
  }
};
