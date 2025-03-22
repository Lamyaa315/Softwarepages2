function validateLogin() {
    let email = document.getElementById("email").value.trim();
    let password = document.getElementById("password").value.trim();
    let role = document.querySelector("input[name='role']:checked").value;

    let emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!email || !password) {
        alert("All fields are required!");
        return;
    }

    if (!emailRegex.test(email)) {
        alert("Please enter a valid email address!");
        return;
    }

    let storedData = JSON.parse(localStorage.getItem("userData"));

    if (storedData && storedData.email === email && storedData.password === password && storedData.role === role) {
        alert("Login successful!");

        if (role === "MakeupArtist") {
            window.location.href = "MAHomePage.html";
        } else {
            window.location.href = "ClientHomePage.html";
        }
    } else {
        alert("Invalid email, password, or role selection!");
    }
}
