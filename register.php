<?php
include 'dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: register_form.php");
    exit;
}

$firstname = trim($_POST['firstname'] ?? '');
$surname   = trim($_POST['surname'] ?? '');
$email     = trim($_POST['email'] ?? '');
$terms     = isset($_POST['terms']) ? 1 : 0;

$errors = [];

// ✅ Validation
if ($firstname === '') {
    $errors[] = 'First name is required.';
}
if ($surname === '') {
    $errors[] = 'Surname is required.';
}
if ($email === '') {
    $errors[] = 'Email is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email address.';
}
if (!$terms) {
    $errors[] = 'You must accept the terms and conditions.';
}

// If errors → show error page
if (count($errors) > 0) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title>Registration Error</title>
        <link href="style/style.css" rel="stylesheet">
    </head>
    <body>
        <div class="card" style="max-width:600px; margin: 2rem auto;">
            <div class="card-header"><h1>Form Errors</h1></div>
            <div class="card-body">
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors as $error) {
                            echo "<li>" . htmlspecialchars($error) . "</li>";
                        } ?>
                    </ul>
                </div>
                <a href="register_form.html" class="btn btn-primary">Back to Register Form</a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}

try {
    // ✅ DB connection
    $dsn = "mysql:host=$servername;port=$port;dbname=$database;charset=utf8mb4";
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ✅ Insert record
    $stmt = $conn->prepare("INSERT INTO merchandise (firstname, surname, email, terms) 
                            VALUES (:firstname, :surname, :email, :terms)");
    $stmt->execute([
        ':firstname' => $firstname,
        ':surname'   => $surname,
        ':email'     => $email,
        ':terms'     => $terms
    ]);

    // ✅ Show success page (same file, styled)
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Registration Successful</title>
        <link href="style/style.css" rel="stylesheet">
        
    </head>
    <body>
        <div id="nav-placeholder"></div>

    <script src="nav.js"></script>
        <section style="min-height: 100vh; display: flex; align-items: center; justify-content: center;">
          <div class="container">
            <div class="form-container">
              <div class="success-message">
                <div class="success-icon">✓</div>
                <h2 class="section-title" style="font-size:2.5rem">Registration Successful!</h2>
                <p class="section-subtitle">Thank you for registering! You'll receive your merchandise info via email soon.</p>
                <a href="index.html" class="btn btn-secondary" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 8px;">
                    <span>←</span>
                    Back to Home
                </a>
              </div>
            </div>
          </div>
        </section>
        
        
    </body>
    </html>
    <?php

} catch (PDOException $e) {
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8" />
        <title>Database Error</title>
        <link rel="stylesheet" href="style.css" />
    </head>
    <body>
        <div class="card" style="max-width:600px; margin: 2rem auto;">
            <div class="card-header"><h1>Database Error</h1></div>
            <div class="card-body text-center">
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($e->getMessage()); ?>
                </div>
                <a href="register_form.php" class="btn btn-primary">Back to Register Form</a>
            </div>
        </div>
    </body>
    </html>
    <?php
}
