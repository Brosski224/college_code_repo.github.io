<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: login.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $project_id = $_POST['project_id'];
    $score = $_POST['score'];
    $comments = $_POST['comments'];
    $teacher_id = $_SESSION['user_id'];
    
    $stmt = $conn->prepare("INSERT INTO evaluations (project_id, teacher_id, comments, score) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iisi", $project_id, $teacher_id, $comments, $score);
    
    if ($stmt->execute()) {
        header("Location: view_repository.php?project_id=$project_id");
    } else {
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
    $conn->close();
}
?>
