<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role_id'], [1, 2])) {
    header("Location: login.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $repo_id = $_POST['repo_id'];
    

    $stmt = $conn->prepare("DELETE FROM projects WHERE repo_id = ?");
    $stmt->bind_param("i", $repo_id);
    $stmt->execute();
    $stmt->close();

    
    $stmt = $conn->prepare("DELETE FROM repositories WHERE repo_id = ?");
    $stmt->bind_param("i", $repo_id);
    $stmt->execute();
    $stmt->close();

    header("Location: student_dashboard.php"); 
} else {
    header("Location: student_dashboard.php");
}

$conn->close();
?>
