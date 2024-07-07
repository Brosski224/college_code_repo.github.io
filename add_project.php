<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: login.html");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $repo_id = $_POST['repo_id'];
    $project_name = $_POST['project_name'];
    $project_description = $_POST['project_description'];
    
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["file"]["name"]);
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    
    if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO projects (repo_id, name, description, file_path) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $repo_id, $project_name, $project_description, $target_file);
        
        if ($stmt->execute()) {
            header("Location: repository.php?repo_id=$repo_id");
        } else {
            echo "Error: " . $stmt->error;
        }
        
        $stmt->close();
    } else {
        echo "Sorry, there was an error uploading your file.";
    }
}
$conn->close();
?>
