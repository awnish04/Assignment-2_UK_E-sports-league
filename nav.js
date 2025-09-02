document.addEventListener("DOMContentLoaded", function () {
  fetch("navigation.html")
    .then((response) => response.text())
    .then((data) => {
      document.getElementById("nav-placeholder").innerHTML = data;

      const navLinks = document.querySelector(".main-nav-links");
      const mobileMenuButton = document.getElementById("mobileMenuButton");
      const menuIcon = document.getElementById("menuIcon");
      const closeIcon = document.getElementById("closeIcon");

      // Toggle menu
      mobileMenuButton.addEventListener("click", function () {
        navLinks.classList.toggle("active");
        if (navLinks.classList.contains("active")) {
          menuIcon.style.display = "none";
          closeIcon.style.display = "inline";
        } else {
          menuIcon.style.display = "inline";
          closeIcon.style.display = "none";
        }
      });

      // Close menu when clicking outside
      document.addEventListener("click", function (event) {
        if (
          !event.target.closest(".main-nav-container") &&
          navLinks.classList.contains("active")
        ) {
          navLinks.classList.remove("active");
          menuIcon.style.display = "inline";
          closeIcon.style.display = "none";
        }
      });

      // Close menu when clicking a link
      document.querySelectorAll(".main-nav-link").forEach((link) => {
        link.addEventListener("click", function () {
          navLinks.classList.remove("active");
          menuIcon.style.display = "inline";
          closeIcon.style.display = "none";
        });
      });

      // Highlight active link
      const currentPage = window.location.pathname.split("/").pop();
      document.querySelectorAll(".main-nav-links a").forEach((link) => {
        if (link.getAttribute("href") === currentPage) {
          link.classList.add("active");
        }
      });
    })
    .catch((error) => console.error("Error loading navigation:", error));
});
