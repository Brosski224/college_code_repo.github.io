<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $user_unique_id = $_POST['user_unique_id'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role_id = $_POST['role'];
    
    if ($password !== $confirm_password) {
        echo "Passwords do not match.";
        exit();
    }
    
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (role_id, username, user_unique_id, password) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $role_id, $username, $user_unique_id, $hashed_password);

    if ($stmt->execute()) {
        $_SESSION['user_id'] = $stmt->insert_id;
        $_SESSION['role_id'] = $role_id;
        $_SESSION['username'] = $username;
        if ($role_id == 1) {
            header("Location: student_dashboard.php");
        } else {
            header("Location: teacher_dashboard.php");
        }
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
