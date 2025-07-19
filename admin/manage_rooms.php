<?php
session_start();
require('../config/db.php');

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit();
}

// Add Room
if (isset($_POST['add_room'])) {
    $room_number = $_POST['room_number'];
    $capacity = intval($_POST['capacity']);
    $stmt = $conn->prepare("INSERT INTO rooms (room_number, capacity) VALUES (?, ?)");
    $stmt->bind_param("si", $room_number, $capacity);
    $stmt->execute();
}

// Delete Room
if (isset($_GET['delete_room'])) {
    $room_id = intval($_GET['delete_room']);
    $conn->query("DELETE FROM rooms WHERE id = $room_id");
}

// Assign Room
$assign_msg = "";
if (isset($_POST['assign_room'])) {
    $student_id = intval($_POST['student_id']);
    $room_id = intval($_POST['room_id']);

    // Check room capacity
    $check = $conn->query("SELECT capacity, occupants FROM rooms WHERE id = $room_id");
    if ($check && $check->num_rows === 1) {
        $data = $check->fetch_assoc();
        if ($data['occupants'] < $data['capacity']) {
            // Assign room
            $conn->query("INSERT INTO room_allocation (student_id, room_id) VALUES ($student_id, $room_id)");
            $conn->query("UPDATE rooms SET occupants = occupants + 1 WHERE id = $room_id");
            $assign_msg = "Room assigned successfully.";
        } else {
            $assign_msg = "Room is full.";
        }
    }
}

// Fetch Data
$rooms = $conn->query("SELECT * FROM rooms");
$students = $conn->query("SELECT id, name FROM users WHERE role='student' AND id NOT IN (SELECT student_id FROM room_allocation)");
include('../navbar.php');?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Rooms</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
    <h2>Manage Rooms</h2>

    <form method="post">
        <h3>Add Room</h3>
        <input type="text" name="room_number" placeholder="Room Number" required>
        <input type="number" name="capacity" placeholder="Capacity" required>
        <input type="submit" name="add_room" value="Add Room">
    </form>

    <h3>Room List</h3>
    <table border="1" cellpadding="10">
        <tr>
            <th>Room #</th><th>Capacity</th><th>Occupants</th><th>Actions</th>
        </tr>
        <?php while($room = $rooms->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($room['room_number']) ?></td>
            <td><?= $room['capacity'] ?></td>
            <td><?= $room['occupants'] ?></td>
            <td>
                <a href="?delete_room=<?= $room['id'] ?>" onclick="return confirm('Delete this room?')">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <h3>Assign Room to Student</h3>
    <?php if ($assign_msg) echo "<p style='color:green;'>$assign_msg</p>"; ?>
    <form method="post">
        <select name="student_id" required>
            <option value="">Select Student</option>
            <?php    
            $students->data_seek(0);   //did change
            while($s = $students->fetch_assoc()): ?>
            <option value="<?= $s['id'] ?>"><?= $s['name'] ?> (ID: <?= $s['id'] ?>)</option>
            <?php endwhile; ?>
        </select>

        <select name="room_id" required>
            <option value="">Select Room</option>
            <?php
            $rooms->data_seek(0); // Reset result pointer
            while($r = $rooms->fetch_assoc()):
            ?>
            <option value="<?= $r['id'] ?>"><?= $r['room_number'] ?> (<?= $r['occupants'] ?>/<?= $r['capacity'] ?>)</option>
            <?php endwhile; ?>
        </select>

        <input type="submit" name="assign_room" value="Assign">
    </form>

    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>
