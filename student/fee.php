<?php
session_start();
require('../config/db.php');

// Check if the user is logged in and is a student
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

// Get the student's ID from session
$student_id = $_SESSION['id'];

// Fetch the student's fee details
$fees = $conn->query("SELECT fees.id, fees.amount_due, fees.due_date, fees.status 
                      FROM fees 
                      WHERE fees.student_id = $student_id");

include('../navbar.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Fees</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <h2>My Fee Details</h2>

    <table border="1" cellpadding="10">
        <tr>
            <th>Amount Due</th><th>Due Date</th><th>Status</th>
        </tr>
        <?php
        if ($fees->num_rows > 0) {
            while ($fee = $fees->fetch_assoc()) {
        ?>
            <tr>
                <td>₹<?= number_format($fee['amount_due'], 2) ?></td>
                <td><?= $fee['due_date'] ?></td>
                <td><?= $fee['status'] ?></td>
            </tr>
        <?php
            }
        } else {
            echo "<tr><td colspan='3'>No fees data found.</td></tr>";
        }
        ?>
    </table>

    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>
