<?php
// admin_sidebar.php
// This file contains the sidebar navigation
?>
<!-- Admin Sidebar -->
<aside class="admin-sidebar">
    <div class="admin-sidebar-header">
        <h2>ESL ADMIN</h2>
        <i class="fas fa-gamepad gaming-icon"></i> 
    </div>

    <nav class="admin-sidebar-nav">
        <a href="admin_menu.php" class="admin-nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'admin_menu.php' ? 'active' : ''; ?>">
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
       
        <a href="view_participants_edit_delete.php" 
            class="admin-nav-link <?php echo (in_array(basename($_SERVER['PHP_SELF']), ['view_participants_edit_delete.php', 'edit_participant_form.php', 'edit_participant.php','delete_confirmation.php','delete.php'])) ? 'active' : ''; ?>">
            <i class="fas fa-users"></i>
            <span>Participants</span>
        </a>

        <a href="search_form.php" 
        class="admin-nav-link <?php echo (in_array(basename($_SERVER['PHP_SELF']), ['search_form.php', 'search_result.php'])) ? 'active' : ''; ?>">
            <i class="fas fa-search"></i>
            <span>Search & Analytics</span>
        </a>
        <a href="#" class="admin-nav-link">
            <i class="fas fa-trophy"></i>
            <span>Tournaments</span>
        </a>
        <a href="#" class="admin-nav-link">
            <i class="fas fa-store"></i>
            <span>Merchandise</span>
        </a>
        <a href="#" class="admin-nav-link">
            <i class="fas fa-cog"></i>
            <span>Settings</span>
        </a>
    </nav>

    <div class="admin-sidebar-footer" >
        <div class="user-info" >
            <a href="logout.php" class="admin-nav-link">
                <i class="fas fa-sign-out-alt signout"></i>
                <span class="logout-text">Logout, 
                    <span class="username">
                        <?php echo htmlspecialchars($_SESSION['admin_username']); ?>
                    </span>
                </span>
            </a>
        </div>
    </div>
</aside>