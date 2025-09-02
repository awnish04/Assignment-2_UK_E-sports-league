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
if (empty($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.html");
    exit;
}
include 'dbconnect.php';

// Include the sidebar
include 'admin_sidebar.php';

// Initialize stats with default 0
$stats = [
    'totalParticipants' => 0,
    'totalTeams' => 0,
    'merchandiseRegistrations' => 0,
    'totalKills' => 0,
    'totalDeaths' => 0,
];

try {
    $dsn = "mysql:host=$servername;port=$port;dbname=$database;charset=utf8mb4";
    $conn = new PDO($dsn, $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Total participants
    $stmt = $conn->query("SELECT COUNT(*) FROM participant");
    $stats['totalParticipants'] = (int)$stmt->fetchColumn();

    // Total teams
    $stmt = $conn->query("SELECT COUNT(*) FROM team");
    $stats['totalTeams'] = (int)$stmt->fetchColumn();

    // Merchandise registrations
    $stmt = $conn->query("SELECT COUNT(*) FROM merchandise");
    $stats['merchandiseRegistrations'] = (int)$stmt->fetchColumn();

    // Total users
    $stmt = $conn->query("SELECT COUNT(*) FROM user");
    $stats['user'] = (int)$stmt->fetchColumn();

    // Total kills
    $stmt = $conn->query("SELECT SUM(kills) FROM participant");
    $stats['totalKills'] = (float)$stmt->fetchColumn() ?: 0;

    // Total deaths
    $stmt = $conn->query("SELECT SUM(deaths) FROM participant");
    $stats['totalDeaths'] = (float)$stmt->fetchColumn() ?: 0;

} catch (PDOException $e) {
    // Log or handle error as needed. Here we keep default 0 values.
}


?>

    <!-- Main Content -->
    <main class="main-content">
        <div class="header">
            <h1>Dashboard Overview</h1>
            
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-3">
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['totalParticipants']; ?></div>
                <div class="stat-label">Total Participants</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['totalTeams']; ?></div>
                <div class="stat-label">Teams</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['merchandiseRegistrations']; ?></div>
                <div class="stat-label">Merchandise Signups</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['user']; ?></div>
                <div class="stat-label">Total Users</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['totalKills']; ?></div>
                <div class="stat-label">Total Kills</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?php echo $stats['totalDeaths']; ?></div>
                <div class="stat-label">Total Deaths</div>
            </div>
        </div>

        <!-- Action Cards -->
        <div class="grid grid-2">
            <div class="card">
                <div class="card-header">
                    <h2>Participant Management</h2>
                </div>
                <div class="card-body">
                    <a href="view_participants_edit_delete.php" class="btn btn-primary mb-3 w-100 justify-content-center">View All Participants</a>
                    <p class="text-muted">View, edit, or delete participant records and update their scores.</p>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2>Search & Analytics</h2>
                </div>
                <div class="card-body">
                    <a href="search_form.php" class="btn btn-secondary mb-3 w-100 justify-content-center">Search Participants & Teams</a>
                    <p class="text-muted">Search for individual participants or teams and view their statistics.</p>
                </div>
            </div>
        </div>
    </main>

    </body>
    </html>
<?php