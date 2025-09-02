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
<main class="main-content">

  <div class="header">
    <h1>Search Participants or Teams</h1>
  </div>


  

  <div class="grid grid-2">
    <div class="card">
      <div class="card-header">
        <h2>Participant Search</h2>
      </div>
      <div class="card-body">
        <form action="search_result.php" method="POST">
          <div class="form-group">
            <label class="form-label">First Name, Surname, or Email</label>
            <input class="form-control bg-white" type="text" name="firstname_surname" placeholder="Enter search term...">
          </div>
          <input type="hidden" name="participant" value="1">
          <button class="btn btn-primary w-100 justify-content-center" type="submit">
            <i class="fas fa-search me-2"></i>Search Participants
          </button>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-header">
        <h2>Team Search</h2>
      </div>
      <div class="card-body">
        <form action="search_result.php" method="POST">
          <div class="form-group">
            <label class="form-label">Team Name</label>
            <input class="form-control bg-white" type="text" name="team" placeholder="Enter team name...">
          </div>
          <button class="btn btn-secondary w-100 justify-content-center" type="submit">
            <i class="fas fa-users me-2"></i>Search Teams
          </button>
        </form>
      </div>
    </div>
  </div>

  <div class="card mt-4">
    <div class="card-header">
      <h2>Search Tips</h2>
    </div>
    <div class="card-body">
      <div class="grid grid-2">
        <div>
          <h3 style="color: var(--primary);"><i class="fas fa-lightbulb me-2"></i>Participant Search</h3>
          <ul class="text-muted">
            <li>Search by first name, last name, or email address</li>
            <li>Partial matches will be returned</li>
            <li>Search is case-insensitive</li>
          </ul>
        </div>
        <div>
          <h3 style="color: var(--primary);"><i class="fas fa-lightbulb me-2"></i>Team Search</h3>
          <ul class="text-muted">
            <li>Search by exact or partial team name</li>
            <li>View team members and statistics</li>
            <li>See overall team performance metrics</li>
          </ul>
        </div>
      </div>
    </div>
  </div>


</main>


</body>
</html>

