document.addEventListener("DOMContentLoaded", function () {
    const darkModeToggle = document.getElementById("darkModeToggle");
    const body = document.body;

    // Check if dark mode was enabled previously
    if (localStorage.getItem("darkMode") === "enabled") {
        body.classList.add("dark-mode");
        if (darkModeToggle) darkModeToggle.checked = true;
    }

    // Add event listener for toggle switch
    if (darkModeToggle) {
        darkModeToggle.addEventListener("change", function () {
            if (darkModeToggle.checked) {
                body.classList.add("dark-mode");
                localStorage.setItem("darkMode", "enabled");
            } else {
                body.classList.remove("dark-mode");
                localStorage.setItem("darkMode", "disabled");
            }
        });
    }
});
