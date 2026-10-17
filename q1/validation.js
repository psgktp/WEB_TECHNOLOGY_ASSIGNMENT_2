// ========================================================
// Client-Side Validation & Dynamic UX Engine
// ========================================================

const registrationForm = document.getElementById("registrationForm");
const fileInput = document.getElementById("document");
const fileSelectedPill = document.getElementById("fileSelectedPill");
const errorBox = document.getElementById("errorBox");
const errorText = document.getElementById("error");

// Dynamic file selection feedback
fileInput.addEventListener("change", function() {
    if (this.files && this.files[0]) {
        const file = this.files[0];
        const sizeKb = (file.size / 1024).toFixed(1);
        fileSelectedPill.textContent = "Selected: " + file.name + " (" + sizeKb + " KB)";
        fileSelectedPill.style.display = "inline-block";
    } else {
        fileSelectedPill.style.display = "none";
    }
});

// Form submit intercept and validation
registrationForm.addEventListener("submit", function(event) {
    const name = document.getElementById("name").value.trim();
    const roll = document.getElementById("roll").value.trim();
    const email = document.getElementById("email").value.trim();
    const course = document.getElementById("course").value;
    const password = document.getElementById("password").value;
    const file = fileInput.files[0];

    // Reset error box
    errorText.textContent = "";
    errorBox.classList.remove("show");

    function showError(msg) {
        errorText.textContent = msg;
        errorBox.classList.add("show");
        event.preventDefault();
    }

    if (!name || !roll || !email || !password || !course) {
        showError("All fields, including course selection, are required.");
    } else if (!/^\d+$/.test(roll)) {
        showError("Roll Number must contain only continuous numeric digits (no alphabets).");
    } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        showError("Enter a valid institutional email address.");
    } else if (
        !/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/.test(password)
    ) {
        showError("Password needs 8 characters, uppercase, lowercase, number and symbol.");
    } else if (file && file.size > 5 * 1024 * 1024) {
        showError("File size must be smaller than 5 MB.");
    } else if (file && !["image/jpeg", "image/png", "application/pdf"].includes(file.type)) {
        showError("Only JPG, PNG and PDF files are allowed.");
    } else {
        event.preventDefault();
        alert("Registration form validated successfully! Student record ready for submission.");
    }
});
