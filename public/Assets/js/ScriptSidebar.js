// Active link highlight and Dropdown Toggle
document.addEventListener('DOMContentLoaded', function() {
    // Handle Dropdown Toggles
    const dropdowns = document.querySelectorAll('.sidebar .dropdown-toggle');
    
    dropdowns.forEach(dropdown => {
        dropdown.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Toggle the submenu
            const submenu = this.nextElementSibling;
            if (submenu && submenu.classList.contains('submenu')) {
                submenu.classList.toggle('show');
                this.classList.toggle('show');
            }
        });
    });

    // Handle Link Activation
    const links = document.querySelectorAll(".sidebar ul li a:not(.dropdown-toggle), .sidebar-footer a");
    
    links.forEach((link) => {
        link.addEventListener("click", function () {
            // Remove active class from all links
            document
                .querySelectorAll(".sidebar ul li a, .sidebar-footer a")
                .forEach((l) => l.classList.remove("active"));
            
            // Add active class to clicked link
            this.classList.add("active");

            // If inside a submenu, also highlight the parent dropdown? 
            // Optional: typically we just highlight the child or keep the menu open.
            // The menu state (open/closed) is preserved by the 'show' class on ul.submenu
        });
    });
});
