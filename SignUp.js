document.addEventListener("DOMContentLoaded", function () {
    const userForm = document.getElementById("UserForm");
    const roleInputs = document.querySelectorAll("input[name='role']");
    
    function showForm() {
        const selectedRole = document.querySelector("input[name='role']:checked").value;
        console.log("Selected Role:", selectedRole); // Debugging line

        if (selectedRole === "Client") {
            userForm.dataset.role = "Client";
        } else {
            userForm.dataset.role = "MakeupArtist";
        }
    }

    roleInputs.forEach(input => input.addEventListener("change", showForm));

    function validateForm() {
        let name = document.getElementById("FullName").value.trim();
        let email = document.getElementById("Email").value.trim();
        let password = document.getElementById("password").value.trim();
        let phone = document.getElementById("phoneNum").value.trim();
        let role = document.querySelector("input[name='role']:checked").value;

        let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        if (!name || !email || !password || !phone) {
            alert("All fields are required!");
            return;
        }

        if (!emailRegex.test(email)) {
            alert("Please enter a valid email address!");
            return;
        }

        let userData = { name, email, password, phone, role };
        localStorage.setItem("userData", JSON.stringify(userData));

        if (role === "MakeupArtist") {
            window.location.href = "MAHomePage.html";
        } else {
            window.location.href = "ClientHomePage.html";
        }
    }

    document.querySelector(".signup").addEventListener("click", validateForm);
});
