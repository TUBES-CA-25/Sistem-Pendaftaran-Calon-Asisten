// Active link highlight
document.querySelectorAll(".sidebar .nav-link").forEach((link) => {
    link.addEventListener("click", function () {
        document.querySelectorAll(".sidebar .nav-link").forEach((l) => l.classList.remove("active"));
        this.classList.add("active");
    });
});

// Sidebar Toggle Logic with Icon Animation
let btn = document.querySelector("#btn");
let sidebar = document.querySelector(".sidebar");
let mainContent = document.querySelector("#content");

if (btn) {
    btn.onclick = function() {
        sidebar.classList.toggle("collapsed");
        
        // Toggle chevron direction: left when expanded, right when collapsed
        if (sidebar.classList.contains("collapsed")) {
            btn.classList.remove("bx-chevron-left");
            btn.classList.add("bx-chevron-right");
        } else {
            btn.classList.remove("bx-chevron-right");
            btn.classList.add("bx-chevron-left");
        }
        
        // Adjust main content
        if (mainContent) {
            mainContent.classList.toggle("expanded");
        }
    };
}
