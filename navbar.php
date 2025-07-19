<!-- navbar.php -->
<?php
$role = $_SESSION['role'] ?? '';
?>

<nav>
    <ul class="navbar">
        <?php if ($role === 'admin'): ?>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="manage_rooms.php">Rooms</a></li>
            <li><a href="manage_fees.php">Fees</a></li>
            <li><a href="manage_complaints.php">Complaints</a></li>
            <li><a href="logs.php">Logs</a></li>
        <?php elseif ($role === 'student'): ?>
            <li><a href="dashboard.php">Dashboard</a></li>
            <li><a href="complaints.php">Submit Complaint</a></li>
            <li><a href="fee.php">My Fees</a></li>
        <?php endif; ?>
        <li><a href="../logout.php">Logout</a></li>
    </ul>
</nav>
