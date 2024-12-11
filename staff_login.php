<?php
require_once "config.php";
session_name("staff");
session_start();

// Check if the user is already logged in
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: staff_access.php");
    exit;
}

if (isset($_POST['submit'])) {
    $uname = $_POST['uname'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $captcha_response = $_POST['g-recaptcha-response']; // Captcha response from the form

    // Secure input data
    $uname = stripcslashes($uname);
    $password = stripcslashes($password);
    $uname = mysqli_real_escape_string($conn, $uname);
    $password = mysqli_real_escape_string($conn, $password);

    // Verify CAPTCHA
    $secret_key = "6LdoI5gqAAAAACjFJPeTqF3vgte7-lh5P53aqH98"; // Replace with your actual secret key
    $verify_url = "https://www.google.com/recaptcha/api/siteverify";
    $response = file_get_contents($verify_url . "?secret=" . $secret_key . "&response=" . $captcha_response);
    $response_keys = json_decode($response, true);

    if (!$response_keys['success']) {
        echo "CAPTCHA verification failed. Please try again.";
        exit;
    }

    // Query the database
    $query = "SELECT * FROM staff WHERE staff_name='$uname' AND password='$password' AND role='$role'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1 && $role == "admin") {
        echo "You have successfully logged in";
        $_SESSION["loggedin"] = true;
        $_SESSION["role"] = $role;
        $_SESSION["username"] = $uname;

        header("location: staff_access.php");
        exit();
    } else {
        echo "You've entered wrong credentials. Please enter correct credentials.";
        exit();
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <title>Admin login</title>
	<link type="text/css" rel="stylesheet" href="stylesheet.css">
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
<div style="text-align:center">
    <form action="#" method="post">
		<table align='center' style="font-weight:bold;">  
			<tr>
				<td><h1 style='text-align:center'>Admin login</h1></td>
			</tr>
			<tr>
				<td align='right'>
				<label for="role_staff">Role:</label>
				<input type='text' name="role" id="role_staff" required></td>
			</tr>
			<tr>
				<td align='right'>
				<label for="login_staff">name:</label>
				<input type='text' name="uname" id="login_staff" required></td>
			</tr>
			<tr>
				<td>
				<label for="password_staff">Password:</label>
				<input type='password' name="password" id="password_staff" required></td>
			</tr>
                <td align='center'>
                    <div class="g-recaptcha" data-sitekey="6LdoI5gqAAAAAAqL1JocTO8c0qLmiVkox_7RANc_"></div>
                </td>
        </tr>
			<tr>
				<td align='center'><br>
				<input type="submit" id="submit" value="submit" name="submit" style="display:none;">
                <label for="submit"><img src="login1.jpg" alt="submit" width="80" height="30"></label>
				</td>
			</tr>
		</table>
	</form>
</div>
</body>
