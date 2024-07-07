<?php
session_start();
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_unique_id = $_POST['user_unique_id'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT user_id, role_id, username, password FROM users WHERE user_unique_id = ?");
    $stmt->bind_param("s", $user_unique_id);
    $stmt->execute();
    $stmt->store_result();
    
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $role_id, $username, $hashed_password);
        $stmt->fetch();
        
        if (password_verify($password, $hashed_password)) {
            $_SESSION['user_id'] = $user_id;
            $_SESSION['role_id'] = $role_id;
            $_SESSION['username'] = $username;
            
            if ($role_id == 1) {
                header("Location: student_dashboard.php");
            } else {
                header("Location: teacher_dashboard.php");
            }
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No user found with that ID.";
    }
}
?>
