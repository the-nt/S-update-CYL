function checkFields() {
    // Getting the fields
    var password = document.getElementById('password');
    var vpassword = document.getElementById('vpassword');

    // Checking the passwords
    if(password.value == vpassword.value) {
        password.style.backgroundColor = "blue";
        vpassword.style.backgroundColor = "blue";
    }
    else {
        // Setting the background of the fields red
        password.style.backgroundColor = "red";
        vpassword.style.backgroundColor = "red";
    }
}

function checkAndSubmit() {
    // If the fields passwords are the same
    if(document.getElementById('password').value == document.getElementById('vpassword').value)
        // Submiting the form
        document.getElementById('passwordForm').submit();
}
