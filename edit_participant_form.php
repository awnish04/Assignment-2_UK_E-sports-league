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

    $stmt = $conn->prepare("SELECT id, firstname, surname, kills, deaths FROM participant WHERE id = :id LIMIT 1");
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
        <h1>Edit Participant</h1>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Edit Participant Details</h2>
        </div>
        <div class="card-body">
            <form id="editParticipantForm" method="POST" action="edit_participant.php">
                <div class="mb-2">
                    <label class="form-label">Participant Firstname</label>
                    <input type="text" class="form-control bg-white text-dark" name="firstname" disabled value="<?php echo htmlspecialchars($row['firstname']); ?>">
                </div>
                <div class="mb-2">
                    <label class="form-label">Participant Surname</label>
                    <input type="text" class="form-control bg-white text-dark" name="surname" disabled value="<?php echo htmlspecialchars($row['surname']); ?>">
                </div>
                <div class="mb-2">
                    <label class="form-label">Kills</label>
                    <input type="number" step="any" min="0" class="form-control bg-white text-dark" name="kills" value="<?php echo htmlspecialchars($row['kills']); ?>" required>
                </div>
                <div class="mb-2">
                    <label class="form-label">Deaths</label>
                    <input type="number" step="any" min="0" class="form-control bg-white text-dark" name="deaths" value="<?php echo htmlspecialchars($row['deaths']); ?>" required>
                </div>
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                <div class="d-flex justify-content-between mt-3">
                    <a href="view_participants_edit_delete.php" class="btn btn-secondary">Cancel</a>
                    <button type="submit" class="btn btn-primary">Update Player</button>
                </div>
            </form>
        </div>
    </div>
</main>


</body>
</html>