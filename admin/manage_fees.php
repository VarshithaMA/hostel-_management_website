<?php
session_start();
require('../config/db.php');

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Update Fee Status (Paid/Unpaid)
if (isset($_GET['update_fee'])) {
    $fee_id = intval($_GET['update_fee']);
    $status = $_GET['status'];

    if ($status === 'Paid' || $status === 'Unpaid') {
        $stmt = $conn->prepare("UPDATE fees SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $fee_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Edit Fee Amount
if (isset($_POST['edit_fee'])) {
    $fee_id = intval($_POST['fee_id']);
    $amount_due = floatval($_POST['amount_due']);
    $due_date = $_POST['due_date'];

    $stmt = $conn->prepare("UPDATE fees SET amount_due = ?, due_date = ? WHERE id = ?");
    $stmt->bind_param("dsi", $amount_due, $due_date, $fee_id);
    $stmt->execute();
    $stmt->close();
}

// Handle Fee Assignment
$assign_msg = '';
if (isset($_POST['assign_fee'])) {
    $student_id = intval($_POST['student_id']);
    $amount_due = floatval($_POST['amount_due']);
    $due_date = $_POST['due_date'];

    $stmt = $conn->prepare("INSERT INTO fees (student_id, amount_due, due_date, status) VALUES (?, ?, ?, 'Unpaid')");
    $stmt->bind_param("ids", $student_id, $amount_due, $due_date);
    if ($stmt->execute()) {
        $assign_msg = "Fee assigned successfully.";
    } else {
        $assign_msg = "Failed to assign fee.";
    }
    $stmt->close();
}

// Fetch fees and students
$fees = $conn->query("SELECT fees.id, fees.amount_due, fees.due_date, fees.status, users.name 
                      FROM fees 
                      JOIN users ON fees.student_id = users.id
                      ORDER BY fees.due_date ASC");

$students = $conn->query("SELECT id, name FROM users WHERE role = 'student'");

include('../navbar.php');
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Fees</title>
    <link rel="stylesheet" href="../assets/style.css">
    <style>
        .paid { color: green; font-weight: bold; }
        .unpaid { color: red; font-weight: bold; }
    </style>
</head>
<body>
    <h2>Manage Fees</h2>

    <table border="1" cellpadding="10">
        <tr>
            <th>Student</th><th>Amount Due</th><th>Due Date</th><th>Status</th><th>Actions</th>
        </tr>
        <?php while ($fee = $fees->fetch_assoc()): ?>
            <?php
                $formatted_due_date = date('d-m-Y', strtotime($fee['due_date']));
                $status_class = ($fee['status'] === 'Paid') ? 'paid' : 'unpaid';
            ?>
            <tr>
                <td><?= htmlspecialchars($fee['name']) ?></td>
                <td>₹<?= number_format($fee['amount_due'], 2) ?></td>
                <td><?= $formatted_due_date ?></td>
                <td class="<?= $status_class ?>"><?= $fee['status'] ?></td>
                <td>
                    <a href="?update_fee=<?= $fee['id'] ?>&status=<?= $fee['status'] === 'Unpaid' ? 'Paid' : 'Unpaid' ?>">
                        Mark as <?= $fee['status'] === 'Unpaid' ? 'Paid' : 'Unpaid' ?>
                    </a> |
                    <a href="edit_fee.php?fee_id=<?= $fee['id'] ?>">Edit</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <h3>Assign Fees to Student</h3>
    <?php if ($assign_msg): ?>
        <p style="color:green;"><?= $assign_msg ?></p>
    <?php endif; ?>

    <form method="post">
        <select name="student_id" required>
            <option value="">Select Student</option>
            <?php while ($s = $students->fetch_assoc()): ?>
                <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?> (ID: <?= $s['id'] ?>)</option>
            <?php endwhile; ?>
        </select>

        <input type="number" name="amount_due" placeholder="Amount Due" step="0.01" required>
        <input type="date" name="due_date" required>
        <input type="submit" name="assign_fee" value="Assign Fee">
    </form>

    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>
