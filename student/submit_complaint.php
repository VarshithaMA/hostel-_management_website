<?php
session_start();
require('../config/db.php');

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'student') {
    http_response_code(403);
    echo "Unauthorized";
    exit();
}

$student_id = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $complaint = trim($_POST['complaint']);

    if (empty($complaint)) {
        http_response_code(400);
        echo "Complaint cannot be empty.";
        exit();
    }

    $stmt = $conn->prepare("INSERT INTO complaints (student_id, complaint) VALUES (?, ?)");
    $stmt->bind_param("is", $student_id, $complaint);

    if ($stmt->execute()) {
        echo "Complaint submitted successfully.";
    } else {
        http_response_code(500);
        echo "Error saving complaint.";
    }

    $stmt->close();
 
}   
