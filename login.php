<?php
session_start();
include 'dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // If accessed directly, redirect back with alert
    echo '<script>alert("You must submit the login form."); window.location.href="admin_login.html";</script>';
    exit;
}

// reCAPTCHA verification
$recaptchaSecret = "6LdxAcArAAAAABjyMYIAbUkYz_tvlhr3lyTvsk9W"; // 🔹 Replace with your secret key
$recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';

if (!$recaptchaResponse) {
    echo '<script>alert("Please complete the reCAPTCHA verification."); window.location.href="admin_login.html";</script>';
    exit; // stops PHP execution after showing the alert
}


$verifyResponse = file_get_contents(
    "https://www.google.com/recaptcha/api/siteverify?secret="
    . $recaptchaSecret . "&response=" . $recaptchaResponse
);
$responseData = json_decode($verifyResponse);

if (!$responseData->success) {
    echo '<script>alert("reCAPTCHA verification failed. Please try again."); window.location.href="admin_login.html";</script>';
    exit;
}

// Process username and password
$username_input = trim($_POST['username'] ?? '');
$password_input = trim($_POST['password'] ?? '');

$errors = [];
if ($username_input === '') {
    $errors[] = 'Username is required.';
}
if ($password_input === '') {
    $errors[] = 'Password is required.';
}

if (count($errors) > 0) {
    $errorMsg = implode("\\n", $errors); // newline for JS alert
    echo '<script>alert("'.$errorMsg.'"); window.location.href="admin_login.html";</script>';
    exit;
}

try {
    $dsn = "mysql:host=$servername;port=$port;dbname=$database;charset=utf8mb4";
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("SELECT id, username, password FROM user WHERE username = :username LIMIT 1");
    $stmt->execute([':username' => $username_input]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && $password_input === $row['password']) { // plaintext password check
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $row['username'];
        header("Location: admin_menu.php");
        exit;
    } else {
        echo '<script>alert("Invalid credentials. Please try again."); window.location.href="admin_login.html";</script>';
        exit;
    }
} catch (PDOException $e) {
    $dbError = addslashes($e->getMessage()); // escape for JS
    echo '<script>alert("Database error: '.$dbError.'"); window.location.href="admin_login.html";</script>';
    exit;
}
?>