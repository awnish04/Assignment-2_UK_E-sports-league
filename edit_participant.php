<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Participant</title>
  <link href="style/style.css" rel="stylesheet">
</head>
<body>
<?php
session_start();

// Redirect if admin not logged in
if (empty($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.html");
    exit;
}

include 'dbconnect.php';
include 'admin_sidebar.php';

try {
    $dsn = "mysql:host=$servername;port=$port;dbname=$database;charset=utf8mb4";
    $conn = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $kills = filter_input(INPUT_POST, 'kills', FILTER_SANITIZE_NUMBER_INT);
        $deaths = filter_input(INPUT_POST, 'deaths', FILTER_SANITIZE_NUMBER_INT);

        if (!$id) {
            throw new Exception("Invalid participant ID.");
        }

        if ($kills === false || $deaths === false) {
            echo "<div class='card'><div class='card-body'>Kills and deaths must be numeric. <a href='view_participants_edit_delete.php' class='btn btn-primary'>Back</a></div></div>";
            exit;
        }

        $stmt = $conn->prepare("UPDATE participant SET kills = :kills, deaths = :deaths WHERE id = :id");
        $stmt->execute([
            ':kills' => $kills,
            ':deaths' => $deaths,
            ':id' => $id
        ]);

        // Success frontend remains intact
        echo '
        <main class="main-content">
            <div class="header">
                <h1>Update Successful</h1>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h2>Participant Updated</h2>
                </div>
                <div class="card-body text-center">
                    <div class="success-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                    <h3>Participant data has been successfully updated!</h3>
                    <p class="text-muted">The changes have been saved to the database.</p>
                    <a href="view_participants_edit_delete.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Participants List
                    </a>
                </div>
            </div>
        </main>';
        exit;

    } else {
        // GET request info page
        echo '
        <main class="main-content">
            <div class="header">
                <h1>Update Participant</h1>
            </div>
            
            <div class="card">
                <div class="card-header">
                    <h2>Information</h2>
                </div>
                <div class="card-body text-center">
                    <p>This page is used for updating participant information.</p>
                    <a href="view_participants_edit_delete.php" class="btn btn-primary">
                        <i class="fas fa-arrow-left me-2"></i>Back to Participants List
                    </a>
                </div>
            </div>
        </main>';
        exit;
    }

} catch (PDOException $e) {
    echo '
    <main class="main-content">
        <div class="card">
            <div class="card-header">
                <h2>Error</h2>
            </div>
            <div class="card-body">
                <div class="alert alert-danger">Database error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES) . '</div>
                <a href="view_participants_edit_delete.php" class="btn btn-primary">Back to Participants List</a>
            </div>
        </div>
    </main>';
} catch (Exception $e) {
    echo '
    <main class="main-content">
        <div class="card">
            <div class="card-header">
                <h2>Error</h2>
            </div>
            <div class="card-body">
                <div class="alert alert-danger">Error: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES) . '</div>
                <a href="view_participants_edit_delete.php" class="btn btn-primary">Back to Participants List</a>
            </div>
        </div>
    </main>';
}
?>
</body>
</html>
