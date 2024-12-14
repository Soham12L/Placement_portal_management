<?php
require_once "config.php";
session_name("hod");
session_start([
    'cookie_httponly' => true,  
    'cookie_secure' => true,    
    'cookie_samesite' => 'Strict' 
]);

if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: hod_access2.php");
    exit;
}


if (!isset($_SESSION['failed_attempts'])) {
    $_SESSION['failed_attempts'] = 0;
    $_SESSION['lockout_time'] = 0;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    if ($_SESSION['failed_attempts'] >= 3 && time() < $_SESSION['lockout_time']) {
        $remaining_time = $_SESSION['lockout_time'] - time();
        echo "Account is locked. Please wait $remaining_time seconds before trying again.";
        exit;
    }

    $uname = trim($_POST['uname']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);
    $captcha_response = $_POST['g-recaptcha-response'];

    
    $secret_key = "6LdoI5gqAAAAACjFJPeTqF3vgte7-lh5P53aqH98";
    $verify_url = "https://www.google.com/recaptcha/api/siteverify";

   
    $response = file_get_contents($verify_url . "?secret=" . $secret_key . "&response=" . $captcha_response);
    $response_keys = json_decode($response, true);

    if ($response_keys['success']) {
        
        $query = "SELECT * FROM staff WHERE staff_name = ? AND password = ? AND role = ?";
        $stmt = $conn->prepare($query);

        if ($stmt) {
            $stmt->bind_param("sss", $uname, $password, $role);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1 && $role == "hod") {
                
                $_SESSION["loggedin"] = true;
                $_SESSION["role"] = $role;
                $_SESSION["username"] = $uname;

                
                $_SESSION['failed_attempts'] = 0;
                $_SESSION['lockout_time'] = 0;

                session_set_cookie_params([
                    'lifetime' => 0,
                    'secure' => true,
                    'httponly' => true,
                    'samesite' => 'Strict'
                ]);

                header("location: hod_access2.php");
                exit;
            } else {
                
                $_SESSION['failed_attempts']++;
                if ($_SESSION['failed_attempts'] >= 3) {
                    $_SESSION['lockout_time'] = time() + 180; 
                    echo "Too many failed attempts. Account is locked for 3 minutes.";
                } else {
                    echo "Invalid credentials. You have " . (3 - $_SESSION['failed_attempts']) . " attempts remaining.";
                }
            }
        } else {
            echo "Database query preparation failed.";
        }
    } else {
        echo "CAPTCHA verification failed. Please try again.";
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
            <td><h1 style='text-align:center'>Hod login</h1></td>
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
