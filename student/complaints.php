<?php
session_start();
require('../config/db.php');

// Check if the user is logged in and is a student
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'student') {
    header("Location: ../login.php");
    exit();
}

include('../navbar.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Complaint (AJAX)</title>
    <link rel="stylesheet" href="../assets/style.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>
    <h2>Submit a Complaint</h2>

    <!-- Complaint Form -->
    <form id="complaintForm">
        <textarea name="complaint" id="complaint" rows="5" cols="50" placeholder="Write your complaint here..." required></textarea><br>
        <input type="submit" value="Submit Complaint">
    </form>

    <p id="responseMessage"></p>

    <p><a href="dashboard.php">Back to Dashboard</a></p>


    <script>
        $(document).ready(function () {
            // Submit the form using AJAX
            $('#complaintForm').submit(function (e) {
                e.preventDefault(); // Prevent page reload on form submission

                const complaint = $('#complaint').val();

                $.ajax({
                    url: 'submit_complaint.php', // The backend PHP script
                    type: 'POST',
                    data: { complaint: complaint },
                    success: function (response) {
                        // Display the response message
                        $('#responseMessage').html('<span style="color:green;">' + response + '</span>');
                        $('#complaint').val(''); // Clear the complaint textarea after submission
                    },
                    error: function () {
                        // Display error message
                        $('#responseMessage').html('<span style="color:red;">Error submitting complaint. Please try again.</span>');
                    }
                });
            });
        });
    </script>
</body>
</html>
