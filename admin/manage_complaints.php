<?php
session_start();
require('../config/db.php');

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Resolve Complaint
if (isset($_GET['resolve'])) {
    $complaint_id = intval($_GET['resolve']);
    $conn->query("UPDATE complaints SET status = 'Resolved' WHERE id = $complaint_id");
}

// Fetch all complaints
$complaints = $conn->query("SELECT complaints.id, complaints.complaint, complaints.status, complaints.created_at, users.name 
                            FROM complaints 
                            JOIN users ON complaints.student_id = users.id
                            ORDER BY complaints.created_at DESC");
include('../navbar.php');?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Complaints</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <h2>Manage Complaints</h2>

    <table border="1" cellpadding="10">
        <tr>
            <th>Student</th><th>Complaint</th><th>Status</th><th>Date</th><th>Actions</th>
        </tr>
        <?php while ($c = $complaints->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($c['name']) ?></td>
            <td><?= htmlspecialchars($c['complaint']) ?></td>
            <td><?= htmlspecialchars($c['status']) ?></td>
            <td><?= $c['created_at'] ?></td>
            <td>
                <?php if ($c['status'] !== 'Resolved'): ?>
                    <a href="?resolve=<?= $c['id'] ?>" onclick="return confirm('Mark this complaint as resolved?')">Mark as Resolved</a>
                <?php else: ?>
                    <span>Resolved</span>
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>
