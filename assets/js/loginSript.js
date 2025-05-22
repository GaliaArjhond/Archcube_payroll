function showPopup(message) {
  const popup = document.createElement("div");
  popup.className = "popup";
  popup.textContent = message;
  document.body.appendChild(popup);
  popup.style.display = "block";

  // Close the popup when clicked
  popup.addEventListener("click", () => {
    popup.style.display = "none";
    document.body.removeChild(popup);
  });
}
