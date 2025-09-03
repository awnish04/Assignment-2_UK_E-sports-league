<?php
session_start();

if (empty($_SESSION['admin_logged_in'])) {
    http_response_code(403);
    exit('Unauthorized');
}

include __DIR__ . '/dbconnect.php';
include __DIR__ . '/admin_sidebar.php';

function escape($str) {
    return htmlspecialchars($str, ENT_QUOTES);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Delete Participant</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="style/style.css" rel="stylesheet">
</head>
<body>
<div class="main-content">
    <div class="header">
        <h1>Deleting Participant</h1>
    </div>
    <div class="card text-center">
        <?php
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo "<div class='error-icon'>⚠️</div>";
            echo "<h3>Method Not Allowed</h3>";
            echo "<div class='alert alert-danger'>Only POST requests are supported.</div>";
            exit;
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $confirm = filter_input(INPUT_POST, 'confirm', FILTER_SANITIZE_STRING);

        if (!$id || !$confirm) {
            echo "<div class='error-icon'>⚠️</div>";
            echo "<div class='alert alert-danger'>Required parameters are missing.</div>";
            exit;
        }

        try {
            $dsn = "mysql:host=$servername;port=$port;dbname=$database;charset=utf8mb4";
            $conn = new PDO($dsn, $username, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
            ]);

            // Check if participant exists
            $checkStmt = $conn->prepare("SELECT id FROM participant WHERE id = :id");
            $checkStmt->execute([':id' => $id]);
            if (!$checkStmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<div class='error-icon'>❌</div>";
                echo "<div class='alert alert-danger'>The participant with the provided ID does not exist.</div>";
                exit;
            }

            // Delete participant
            $stmt = $conn->prepare("DELETE FROM participant WHERE id = :id");
            $stmt->execute([':id' => $id]);

            echo "<div class='success-icon'>✔️</div>";
            echo "<div class='alert alert-success'>Participant has been deleted successfully.</div>";
            

        } catch (PDOException $e) {
            echo "<div class='error-icon'>⚠️</div>";
            echo "<div class='alert alert-danger'>" . escape($e->getMessage()) . "</div>";
        } catch (Exception $e) {
            echo "<div class='error-icon'>⚠️</div>";
            echo "<h3>Error</h3>";
            echo "<div class='alert alert-danger'>" . escape($e->getMessage()) . "</div>";
        }
        ?>
    </div>
</div>
</body>
</html>
