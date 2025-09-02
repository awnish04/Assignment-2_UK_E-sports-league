<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Search results</title>
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

include 'admin_sidebar.php';
?>
<main class="main-content">
    <div class="header mb-4">
        <h1 class="mb-0">Participant Management</h1>
    </div>

    <?php
    try {
        $dsn = "mysql:host=$servername;port=$port;dbname=$database;charset=utf8mb4";
        $conn = new PDO($dsn, $username, $password);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if (isset($_POST['participant']) && $_POST['participant'] == "1") {
            $term = trim($_POST['firstname_surname'] ?? '');
            if ($term === '') {
                echo "<div class='alert alert-warning'>Please enter a name or email to search.</div>";
                exit;
            }

            $stmt = $conn->prepare("SELECT p.id, p.firstname, p.surname, p.email, p.kills, p.deaths, t.name as team_name
                                    FROM participant p
                                    LEFT JOIN team t ON p.team_id = t.id
                                    WHERE p.firstname LIKE :term OR p.surname LIKE :term OR p.email LIKE :term
                                    LIMIT 50");
            $like = '%' . $term . '%';
            $stmt->execute([':term' => $like]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$rows) {
                echo "<div class='alert alert-info'>No participants found for: " . htmlspecialchars($term) . "</div>";
            } else {
                echo "<h3 class='mb-3'>Participants found for: " . htmlspecialchars($term) . "</h3>";
                echo '<div class="table-responsive">';
                echo '<table class="table table-striped table-bordered table-hover">';
                echo '<thead class="table-dark"><tr><th>ID</th><th>Name</th><th>Email</th><th>Team</th><th>K</th><th>D</th><th>K/D</th></tr></thead><tbody>';
                foreach ($rows as $r) {
                    $kd = (float)$r['deaths'] == 0 ? (float)$r['kills'] : (float)$r['kills'] / (float)$r['deaths'];
                    $kd_display = number_format($kd, 2);
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($r['id']) . "</td>";
                    echo "<td>" . htmlspecialchars($r['firstname'] . ' ' . $r['surname']) . "</td>";
                    echo "<td>" . htmlspecialchars($r['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($r['team_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($r['kills']) . "</td>";
                    echo "<td>" . htmlspecialchars($r['deaths']) . "</td>";
                    echo "<td>" . htmlspecialchars($kd_display) . "</td>";
                    echo "</tr>";
                }
                echo "</tbody></table></div>";
            }
        } else {
            $team = trim($_POST['team'] ?? '');
            if ($team === '') {
                echo "<div class='alert alert-warning'>Please enter a team name to search.</div>";
                exit;
            }

            $stmt = $conn->prepare("SELECT id, name, location FROM team WHERE name LIKE :team LIMIT 1");
            $stmt->execute([':team' => '%' . $team . '%']);
            $teamRow = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$teamRow) {
                echo "<div class='alert alert-info'>No team found matching: " . htmlspecialchars($team) . "</div>";
                exit;
            }

            echo "<h3 class='mb-3'>Team: " . htmlspecialchars($teamRow['name']) . " (" . htmlspecialchars($teamRow['location']) . ")</h3>";

            $stmt = $conn->prepare("SELECT id, firstname, surname, kills, deaths FROM participant WHERE team_id = :team_id");
            $stmt->execute([':team_id' => $teamRow['id']]);
            $players = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (!$players) {
                echo "<div class='alert alert-info'>No players found for this team.</div>";
                exit;
            }

            $teamKills = 0.0;
            $teamDeaths = 0.0;
            echo '<div class="table-responsive" style="max-height: 70vh;">';
            echo '<table class="table table-bordered table-hover">';
            echo '<thead class="table-dark"><tr><th>ID</th><th>Name</th><th>Kills</th><th>Deaths</th><th>K/D</th></tr></thead><tbody>';
            foreach ($players as $p) {
                $teamKills += (float)$p['kills'];
                $teamDeaths += (float)$p['deaths'];
                $kd = ((float)$p['deaths'] == 0) ? (float)$p['kills'] : ((float)$p['kills'] / (float)$p['deaths']);
                echo "<tr>";
                echo "<td>" . htmlspecialchars($p['id']) . "</td>";
                echo "<td>" . htmlspecialchars($p['firstname'] . ' ' . $p['surname']) . "</td>";
                echo "<td>" . htmlspecialchars($p['kills']) . "</td>";
                echo "<td>" . htmlspecialchars($p['deaths']) . "</td>";
                echo "<td>" . htmlspecialchars(number_format($kd, 2)) . "</td>";
                echo "</tr>";
            }
            echo "</tbody></table></div>";

            $teamKD = ($teamDeaths == 0) ? $teamKills : ($teamKills / $teamDeaths);
            echo "<div class='alert alert-secondary mt-3'>Team total kills: " . htmlspecialchars(number_format($teamKills,2)) .
                " — team total deaths: " . htmlspecialchars(number_format($teamDeaths,2)) .
                " — Team K/D: " . htmlspecialchars(number_format($teamKD,2)) . "</div>";
        }

    } catch (PDOException $e) {
        echo "<div class='alert alert-danger'>Database error: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
    ?>
</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
