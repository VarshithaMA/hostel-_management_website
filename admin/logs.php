<?php
session_start();
require('../config/db.php');

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch logs
//$logs = $conn->query("SELECT action, details, timestamp FROM logs ORDER BY timestamp DESC");
include('../navbar.php');
$logs = $conn->query("SELECT action, created_at FROM logs ORDER BY created_at DESC");

?>

<!DOCTYPE html>
<html>
<head>
    <title>System Logs</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <h2>System Logs</h2>

    <table border="1" cellpadding="10">
    <tr>
        <th>Action</th><th>Timestamp</th>
    </tr>
    <?php while ($log = $logs->fetch_assoc()): ?>
    <tr>
        <td><?= htmlspecialchars($log['action']) ?></td>
        <td><?= $log['created_at'] ?></td>
    </tr>
    <?php endwhile; ?>
</table>

    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>
