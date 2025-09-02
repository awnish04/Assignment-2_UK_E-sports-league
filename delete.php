<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Search</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="style/style.css" rel="stylesheet">

</head>
<body>
<?php
session_start();
if (empty($_SESSION['admin_logged_in'])) {
    http_response_code(403);
    exit('Unauthorized');
}
include 'dbconnect.php';

include 'admin_sidebar.php';

?>



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
            echo "<div class='alert alert-error'>Only POST requests are supported.</div>";
            exit;
        }

        try {
            $dsn = "mysql:host=$servername;port=$port;dbname=$database;charset=utf8mb4";
            $conn = new PDO($dsn, $username, $password);
            $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            if (isset($_POST['confirm']) && isset($_POST['id'])) {
                $id = intval($_POST['id']);
                if ($id <= 0) {
                    echo "<div class='error-icon'>❌</div>";
                    echo "<div class='alert alert-error'>Invalid participant ID.</div>";
                    exit;
                }
                
                // Check if participant exists
                $checkStmt = $conn->prepare("SELECT id FROM participant WHERE id = :id");
                $checkStmt->execute([':id' => $id]);
                if (!$checkStmt->fetch()) {
                    echo "<div class='error-icon'>❌</div>";
                    echo "<div class='alert alert-error'>Participant not found.</div>";
                    exit;
                }
                
                // Delete participant
                $stmt = $conn->prepare("DELETE FROM participant WHERE id = :id");
                $stmt->execute([':id' => $id]);
                
                echo "<div class='success-icon'>✔️</div>";
                echo "<div class='alert alert-success'>Participant deleted successfully.</div>";
                exit;
            } else {
                echo "<div class='error-icon'>⚠️</div>";
                echo "<div class='alert alert-error'>Required parameters are missing.</div>";
                exit;
            }

        } catch (PDOException $e) {
            echo "<div class='error-icon'>⚠️</div>";
            echo "<h3>Database Error</h3>";
            echo "<div class='alert alert-error'>" . htmlspecialchars($e->getMessage()) . "</div>";
        } catch (Exception $e) {
            echo "<div class='error-icon'>⚠️</div>";
            echo "<h3>Error</h3>";
            echo "<div class='alert alert-error'>" . htmlspecialchars($e->getMessage()) . "</div>";
        }
        ?>
    </div>
</div>

</body>
</html>