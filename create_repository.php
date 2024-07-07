<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: login.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $user_id = $_SESSION['user_id'];
    
    $stmt = $conn->prepare("INSERT INTO repositories (name, description, user_id) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $name, $description, $user_id);
    
    if ($stmt->execute()) {
        header("Location: student_dashboard.php");
    } else {
        echo "Error: " . $stmt->error;
    }
    
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Repository</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Create Repository</h2>
        <form action="create_repository.php" method="post">
            <label for="name">Repository Name:</label>
            <input type="text" id="name" name="name" required><br>
            
            <label for="description">Description:</label>
            <textarea id="description" name="description" required></textarea><br>
            
            <button type="submit">Create</button>
        </form>
    </div>
</body>
</html>
