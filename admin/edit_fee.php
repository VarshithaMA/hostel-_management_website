<?php
session_start();
require('../config/db.php');

// Check if the user is logged in and is an admin
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Fetch the fee record to be edited
if (isset($_GET['fee_id'])) {
    $fee_id = intval($_GET['fee_id']);

    // Get the fee details from the database
    $result = $conn->query("SELECT * FROM fees WHERE id = $fee_id");

    if ($result->num_rows > 0) {
        $fee = $result->fetch_assoc();
    } else {
        echo "Fee record not found.";
        exit();
    }
} else {
    echo "Invalid fee ID.";
    exit();
}

// Update the fee details if the form is submitted
if (isset($_POST['update_fee'])) {
    $amount_due = floatval($_POST['amount_due']);
    $due_date = $_POST['due_date'];

    // Update fee record in the database
    $conn->query("UPDATE fees SET amount_due = $amount_due, due_date = '$due_date' WHERE id = $fee_id");

    // Redirect back to manage fees page after successful update
    header("Location: manage_fees.php");
    exit();
}

include('../navbar.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Fee</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <h2>Edit Fee Details</h2>

    <form action="" method="POST">
        <label for="amount_due">Amount Due:</label><br>
        <input type="number" step="0.01" name="amount_due" id="amount_due" value="<?= $fee['amount_due'] ?>" required><br><br>

        <label for="due_date">Due Date:</label><br>
        <input type="date" name="due_date" id="due_date" value="<?= $fee['due_date'] ?>" required><br><br>

        <input type="submit" name="update_fee" value="Update Fee">
    </form>

    <p><a href="manage_fees.php">Back to Manage Fees</a></p>
</body>
</html>
