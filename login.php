<?php
session_start();
include 'dbconnect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Show full page for mistake case
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8" />
      <title>Login Error</title>
      <link rel="stylesheet" href="style.css" />
    </head>
    <body>
      <div class="card" style="max-width:600px; margin: 2rem auto;">
        <div class="card-header">
          <h1>Error</h1>
        </div>
        <div class="card-body text-center">
          <p>You're here by mistake. Please login through the login form.</p>
          <a href="admin_login.html" class="btn btn-primary">Back to Login</a>
        </div>
      </div>
    </body>
    </html>
    <?php
    exit;
}

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
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
      <meta charset="UTF-8" />
      <title>Login Errors</title>
      <link rel="stylesheet" href="style.css" />
    </head>
    <body>
      <div class="card" style="max-width:600px; margin: 2rem auto;">
        <div class="card-header">
          <h1>Login Errors</h1>
        </div>
        <div class="card-body">
          <div class="alert alert-error">
            <ul>
              <?php foreach ($errors as $error) {
                echo "<li>" . htmlspecialchars($error) . "</li>";
              } ?>
            </ul>
          </div>
          <a href="admin_login.html" class="btn btn-primary">Back to Login</a>
        </div>
      </div>
    </body>
    </html>
    <?php
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
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
          <meta charset="UTF-8" />
          <title>Login Failed</title>
          <link rel="stylesheet" href="style.css" />
        </head>
        <body>
          <div class="card" style="max-width:600px; margin: 2rem auto;">
            <div class="card-header">
              <h1>Login Failed</h1>
            </div>
            <div class="card-body text-center">
              <div class="alert alert-error">
                Invalid credentials.
              </div>
              <a href="admin_login.html" class="btn btn-primary">Try Again</a>
            </div>
          </div>
        </body>
        </html>
        <?php
        exit;
    }
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
        <div class="card-header">
          <h1>Database Error</h1>
        </div>
        <div class="card-body text-center">
          <div class="alert alert-error">
            <?php echo htmlspecialchars($e->getMessage()); ?>
          </div>
          <a href="admin_login.html" class="btn btn-primary">Back to Login</a>
        </div>
      </div>
    </body>
    </html>
    <?php
}
?>
