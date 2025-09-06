<?php
include 'dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: register_form.html");
    exit;
}

$firstname = trim($_POST['firstname'] ?? '');
$surname   = trim($_POST['surname'] ?? '');
$email     = trim($_POST['email'] ?? '');
$terms     = isset($_POST['terms']) ? 1 : 0;

$errors = [];

// ✅ Validate firstname
if ($firstname === '') {
    $errors[] = 'First name is required.';
} elseif (!preg_match("/^[a-zA-Z\s'-]{2,50}$/", $firstname)) {
    $errors[] = 'First name must be 2–50 letters only (letters, spaces, apostrophes, dashes allowed).';
}

// ✅ Validate surname
if ($surname === '') {
    $errors[] = 'Surname is required.';
} elseif (!preg_match("/^[a-zA-Z\s'-]{2,50}$/", $surname)) {
    $errors[] = 'Surname must be 2–50 letters only (letters, spaces, apostrophes, dashes allowed).';
}

// ✅ Validate email
if ($email === '') {
    $errors[] = 'Email is required.';
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = 'Invalid email address.';
} elseif (strlen($email) > 100) {
    $errors[] = 'Email must not exceed 100 characters.';
}

// ✅ Terms must be accepted
if (!$terms) {
    $errors[] = 'You must accept the terms and conditions.';
}

// If errors → show them
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
            <div class="card-header"><h2>Form Errors</h2></div>
            <div class="card-body">
                <div class="alert alert-error">
                    <ul>
                        <?php foreach ($errors as $error) {
                            echo "<li>" . htmlspecialchars($error) . "</li>";
                        } ?>
                    </ul>
                </div>
                <div style="text-align: center">
                    <a href="register_form.html" class="btn btn-primary">Back to Register Form</a>
                </div>
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

    // ✅ Check if email already exists
    $stmt = $conn->prepare("SELECT COUNT(*) FROM merchandise WHERE email = :email");
    $stmt->execute([':email' => $email]);
    if ($stmt->fetchColumn() > 0) {
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <title>Registration Error</title>
            <link href="style/style.css" rel="stylesheet">
        </head>
        <body>
            <div class="card" style="max-width:600px; margin: 2rem auto;">
                <div class="card-header"><h2>Duplicate Email</h2></div>
                <div class="card-body">
                    <div class="alert alert-error">
                        <p>This email is already registered. Please use another email.</p>
                    </div>
                    <div style="text-align: center">
                        <a href="register_form.html" class="btn btn-primary">Back to Register Form</a>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
        exit;
    }

    // ✅ Insert new record
    $stmt = $conn->prepare("INSERT INTO merchandise (firstname, surname, email, terms) 
                            VALUES (:firstname, :surname, :email, :terms)");
    $stmt->execute([
        ':firstname' => $firstname,
        ':surname'   => $surname,
        ':email'     => $email,
        ':terms'     => $terms
    ]);

    // ✅ Success page
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Registration Successful</title>
        <link href="style/style.css" rel="stylesheet">
    </head>
    <body>
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
        <link href="style/style.css" rel="stylesheet">
    </head>
    <body>
        <div class="card" style="max-width:600px; margin: 2rem auto;">
            <div class="card-header"><h1>Database Error</h1></div>
            <div class="card-body text-center">
                <div class="alert alert-error">
                    <?= htmlspecialchars($e->getMessage()); ?>
                </div>
                <a href="register_form.html" class="btn btn-primary">Back to Register Form</a>
            </div>
        </div>
    </body>
    </html>
    <?php
    
}
    