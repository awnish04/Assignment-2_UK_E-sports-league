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
?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="header">
            <h1>Participant Management</h1>
        </div>

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">All Participants</h2>
        </div>

        <?php
try {
    $dsn = "mysql:host=$servername;port=$port;dbname=$database;charset=utf8mb4";
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->query("SELECT p.id, p.firstname, p.surname, p.email, p.kills, p.deaths, t.name as team_name FROM participant p LEFT JOIN team t ON p.team_id = t.id ORDER BY p.id ASC");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (!$rows) {
        echo "<div class='alert alert-info'>No participants found.</div>";
    } else {
        echo '<div class="table-responsive" style="max-height: 70vh;">';
        echo '<table class="table table-striped table-success table-sm table-bordered border-dark table-hover ">';
        echo '<thead class="table-dark sticky-header">';
        echo '<tr><th>ID</th><th>Name</th><th>Email</th><th>Team</th><th>Kills</th><th>Deaths</th><th>Actions</th></tr>';
        echo '</thead><tbody>';
        foreach ($rows as $r) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($r['id']) . '</td>';
            echo '<td>' . htmlspecialchars($r['firstname'] . ' ' . $r['surname']) . '</td>';
            echo '<td>' . htmlspecialchars($r['email']) . '</td>';
            echo '<td>' . htmlspecialchars($r['team_name']) . '</td>';
            echo '<td>' . htmlspecialchars($r['kills']) . '</td>';
            echo '<td>' . htmlspecialchars($r['deaths']) . '</td>';
            echo '<td style="display:flex;padding: 0rem; ">
                    <a href="edit_participant_form.php?id=' . htmlspecialchars($r['id']) . '"  style="background: var(--gray); color: var(--neon-green); border-radius: 5px; border-color:transparent; padding: 0.3rem 0.5rem; margin:12px">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="delete_confirmation.php?id=' . htmlspecialchars($r['id']) . '" style="background: var(--gray); color: var(--secondary); border-radius: 5px; border-color:transparent; padding: 0.3rem 0.5rem; margin:12px">
                        <i class="fas fa-trash"></i>
                    </a>
                  </td>';
            echo '</tr>';
        }
        echo '</tbody></table></div>'; // close table-responsive
    }
} catch (PDOException $e) {
    echo "<div class='alert alert-danger'>Database error: " . htmlspecialchars($e->getMessage()) . "</div>";
}
?>

    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>