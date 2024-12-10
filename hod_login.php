<?php
require_once "config.php";
session_name("hod");
session_start();


if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true) {
    header("location: hod_access2.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
 
    $uuname = trim($_POST['uuname']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);
    $captcha_response = $_POST['g-recaptcha-response'];

 
    $secret_key = "6LdipZMqAAAAAFzD-Af8XnlyxJp0xhPZrr7gbirl"; // Replace with your Secret Key
    $verify_url = "https://www.google.com/recaptcha/api/siteverify";
    $response = file_get_contents($verify_url . "?secret=" . $secret_key . "&response=" . $captcha_response);
    $response_keys = json_decode($response, true);

    if (!$response_keys['success']) {
        echo "CAPTCHA verification failed. Please try again.";
        exit;
    }

  
    $query = "SELECT * FROM staff WHERE staff_name = ? AND password = ? AND role = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $uuname, $ppassword, $rrole);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1 && $role == "hod") {

        $_SESSION["loggedin"] = true;
        $_SESSION["role"] = $role;
        $_SESSION["username"] = $uname;


        header("location: hod_access2.php");
        exit;
    } else {
        echo "Invalid credentials. Please try again.";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HOD Login</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
        }
        form {
            display: inline-block;
            text-align: left;
            background: #f5f5f5;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 10px;
            
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input {
            width: 100%;
            padding: 8px;
            margin: 5px 0 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .g-recaptcha {
            margin-bottom: 15px;
        }
        button {
            background: #11107bff;
            color: #ff;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <h1>HOD Login</h1>
    <form action="" method="post">
        <label for="role">Role:</label>
        <input type="text" id="role" name="role" required>

        <label for="uuname">Username:</label>
        <input type="text" id="uuname" name="uuname" required>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>


        <div class="g-recaptcha" data-sitekey="6LcyrZMqAAAAADtm2fXS3sOfUr3RNnRWwxhGAUJn"></div>

        <button type="submit">Login</button>
    </form>
</body>
</html>
