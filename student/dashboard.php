<?php
session_start();
require('../config/db.php');

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

$student_id = $_SESSION['id'];

// Get student info
$stmt = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$stmt->bind_result($name, $email);
$stmt->fetch();
$stmt->close();

// Get room info
$room_query = $conn->query("SELECT rooms.room_number FROM room_allocation 
    JOIN rooms ON room_allocation.room_id = rooms.id 
    WHERE room_allocation.student_id = $student_id");
$room_number = ($room_query->num_rows > 0) ? $room_query->fetch_assoc()['room_number'] : 'Not assigned';

// Get fee info
$fee_query = $conn->query("SELECT amount_due, due_date, status FROM fees WHERE student_id = $student_id");
$fee = ($fee_query->num_rows > 0) ? $fee_query->fetch_assoc() : null;

// Get complaints
$complaints = $conn->query("SELECT complaint, status, created_at FROM complaints WHERE student_id = $student_id ORDER BY created_at DESC");
include('../navbar.php'); 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        /* Adding some styles to enhance visual representation */
        .paid { color: green; }
        .unpaid { color: red; }
    </style>
</head>
<body>
    <h2>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?> (Student)</h2>

    <h3>Your Profile</h3>
    <p><strong>Name:</strong> <?= htmlspecialchars($name) ?></p>
    <p><strong>Email:</strong> <?= htmlspecialchars($email) ?></p>
    <p><strong>Room:</strong> <?= $room_number ?></p>

    <h3>Fee Status</h3>
    <?php if ($fee): ?>
        <p>Amount Due: ₹<?= number_format($fee['amount_due'], 2) ?></p>
        <p>Due Date: <?= $fee['due_date'] ?></p>
        <p>Status: <span class="<?= strtolower($fee['status']) ?>"><?= $fee['status'] ?></span></p>
    <?php else: ?>
        <p>No fee records found.</p>
    <?php endif; ?>

    <h3>Your Complaints</h3>
    <table border="1" cellpadding="8">
        <tr><th>Complaint</th><th>Status</th><th>Date</th></tr>
        <?php while ($c = $complaints->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($c['complaint']) ?></td>
            <td><?= htmlspecialchars($c['status']) ?></td>
            <td><?= $c['created_at'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <p><a href="complaints.php">Submit New Complaint</a></p>
    <p><a href="../logout.php">Logout</a></p>
</body>
</html>
