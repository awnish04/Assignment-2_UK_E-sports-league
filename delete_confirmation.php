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
    header("Location: admin_login.html");
    exit;
}
include 'dbconnect.php';

// Include the sidebar
include 'admin_sidebar.php';


$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    echo '<main class="main-content"><div class="alert alert-danger">Invalid participant ID</div></main>';
    exit;
}

try {
    $dsn = "mysql:host=$servername;port=$port;dbname=$database;charset=utf8mb4";
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->prepare("SELECT id, firstname, surname FROM participant WHERE id = :id LIMIT 1");
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo '<main class="main-content"><div class="alert alert-danger">Participant not found</div></main>';
        exit;
    }
} catch (PDOException $e) {
    echo '<main class="main-content"><div class="alert alert-danger">Database error</div></main>';
    exit;
}
?>

<main class="main-content">
    <div class="header">
        <h1>Delete Participant</h1>
    </div>
    
    <div class="card">
        <div class="card-header">
            <h2>Confirm Deletion</h2>
        </div>
        <div class="card-body">
            <form id="deleteParticipantForm" method="POST" action="delete.php">
                <p class="text-light">Are you sure you want to delete <strong class="text-danger"><?php echo htmlspecialchars($row['firstname'] . ' ' . $row['surname']); ?></strong>?</p>
                <p class="text-muted">This action cannot be undone.</p>
                
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                <input type="hidden" name="confirm" value="1">
                
                <div class="d-flex justify-content-between mt-3">
                    <a href="view_participants_edit_delete.php" class="btn btn-primary">Cancel</a>
                    <button type="submit" class="btn btn-danger">Yes, Delete</button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php
?>
</body>
</html>