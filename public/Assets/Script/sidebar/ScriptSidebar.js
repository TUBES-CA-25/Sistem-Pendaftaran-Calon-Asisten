// Active link highlight
document
  .querySelectorAll(".sidebar ul li a, .sidebar-footer a")
  .forEach((link) => {
    link.addEventListener("click", function () {
      document
        .querySelectorAll(".sidebar ul li a, .sidebar-footer a")
        .forEach((l) => l.classList.remove("active"));
      this.classList.add("active");
    });
  });
