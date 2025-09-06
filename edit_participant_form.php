<?php
session_start();

if (empty($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.html");
    exit;
}

include __DIR__ . '/dbconnect.php';
include __DIR__ . '/admin_sidebar.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    $error_message = "Invalid participant ID.";
} else {
    try {
        $dsn = "mysql:host=$servername;port=$port;dbname=$database;charset=utf8mb4";
        $conn = new PDO($dsn, $username, $password, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]);

        $stmt = $conn->prepare("SELECT id, firstname, surname, kills, deaths 
                                FROM participant 
                                WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$row) {
            $error_message = "Participant not found.";
        }
    } catch (PDOException $e) {
        $error_message = "Database error.";
    }
}

function escape($str) {
    return htmlspecialchars($str, ENT_QUOTES);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Edit Participant</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="style/style.css" rel="stylesheet">
</head>
<body>
<main class="main-content">
    <?php if (!empty($error_message)): ?>
        <div class="alert alert-danger"><?= escape($error_message); ?></div>
    <?php else: ?>
        <div class="header">
            <h1>Edit Participant</h1>
        </div>

        <div class="card">
            <div class="card-header">
                <h2>Edit Participant Details</h2>
            </div>
            <div class="card-body">
                <form id="editParticipantForm" method="POST" action="edit_participant.php">
                    
                    <div class="mb-3">
                        <label class="form-label">Participant Firstname</label>
                        <input type="text" class="form-control bg-white text-dark" 
                               name="firstname" value="<?= escape($row['firstname']); ?>" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Participant Surname</label>
                        <input type="text" class="form-control bg-white text-dark" 
                               name="surname" value="<?= escape($row['surname']); ?>" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Kills</label>
                        <input type="number" step="1" min="0" class="form-control bg-white text-dark" 
                               name="kills" value="<?= escape($row['kills']); ?>" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Deaths</label>
                        <input type="number" step="1" min="0" class="form-control bg-white text-dark" 
                               name="deaths" value="<?= escape($row['deaths']); ?>" required>
                    </div>

                    <input type="hidden" name="id" value="<?= escape($row['id']); ?>">

                    <div class="d-flex justify-content-between mt-3">
                        <a href="view_participants_edit_delete.php" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</main>
</body>
</html>
