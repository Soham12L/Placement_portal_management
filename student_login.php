<?php
require_once "config.php";
session_start([
    'cookie_httponly' => true,  
    'cookie_secure' => true,    
    'cookie_samesite' => 'Strict' 
]);


if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: student_profile.php");
    exit;
}


if (!isset($_SESSION['failed_attempts'])) {
    $_SESSION['failed_attempts'] = 0;
    $_SESSION['lockout_time'] = 0;
}

$error_message = ""; // 

if (isset($_POST['submit'])) {
    
    if ($_SESSION['failed_attempts'] >= 3 && time() < $_SESSION['lockout_time']) {
        $remaining_time = $_SESSION['lockout_time'] - time();
        $error_message = "Account is locked. Please wait $remaining_time seconds before trying again.";
    } else {
        $uname = trim($_POST['uname']);
        $password = trim($_POST['password']);
        $regdno = trim($_POST['regdno']);
        $captcha_response = $_POST['g-recaptcha-response']; 

       
        $secret_key = "6LdoI5gqAAAAACjFJPeTqF3vgte7-lh5P53aqH98"; 
        $verify_url = "https://www.google.com/recaptcha/api/siteverify";
        $response = file_get_contents($verify_url . "?secret=" . $secret_key . "&response=" . $captcha_response);
        $response_keys = json_decode($response, true);

        if (!$response_keys['success']) {
            $error_message = "CAPTCHA verification failed. Please try again.";
        } else {
            
            $query = "SELECT * FROM student WHERE regdno='$regdno' AND name='$uname' AND password='$password'";
            $result = mysqli_query($conn, $query);

            if (mysqli_num_rows($result) == 1) {
                $_SESSION["loggedin"] = true;
                $_SESSION["username"] = $uname;

                $row1 = mysqli_fetch_assoc($result);
                $_SESSION["num"] = $row1['regdno'];

                
                $_SESSION['failed_attempts'] = 0;
                $_SESSION['lockout_time'] = 0;

                header("location: student_profile.php");
                exit();
            } else {
                
                $_SESSION['failed_attempts']++;
                if ($_SESSION['failed_attempts'] >= 3) {
                    $_SESSION['lockout_time'] = time() + 180; // Lock for 3 minutes
                    $error_message = "Too many failed attempts. Account is locked for 3 minutes.";
                } else {
                    $remaining_attempts = 3 - $_SESSION['failed_attempts'];
                    $error_message = "Invalid credentials. You have $remaining_attempts attempts remaining.";
                }
            }
        }
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <title>student login</title>
    <link type="text/css" rel="stylesheet" href="stylesheet.css">
    <style>
        .error-message {
            color: red;
            font-weight: bold;
            margin-bottom: 10px;
            text-align: center;
        }
    </style>
    
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
<div style="text-align:center">
    
    <?php if (!empty($error_message)) : ?>
        <div class="error-message"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form action="#" method="post">
        <table align='center' style="font-weight:bold;">  
        <tr>
            <td><h1 style='text-align:center'> student login</h1></td>
        </tr>
        <tr>
            <td align='right'>
            <label for="student_regd">Regdno:</label>
            <input type='text' name="regdno" id="student_regd" required></td>
        </tr>
        <tr>
            <td align='right'>
            <label for="student_name">Name:</label>
            <input type='text' name="uname" id="student_name" required></td>
        </tr>
        <tr>
            <td>
            <label for="student_password">Password:</label>
            <input type='password' id="student_password" name="password" required></td>
        </tr>
        
        <tr>
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
</body>
</html>
