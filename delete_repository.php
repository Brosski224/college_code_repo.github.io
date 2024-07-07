<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

if (!isset($_POST['repo_id']) || empty($_POST['repo_id'])) {
    die("Repository ID is required.");
}

$repo_id = $_POST['repo_id'];
$user_id = $_SESSION['user_id'];

$conn->begin_transaction();

try {
    $delete_eval_stmt = $conn->prepare("
        DELETE evaluations 
        FROM evaluations 
        INNER JOIN projects ON evaluations.project_id = projects.project_id 
        WHERE projects.repo_id = ?
    ");
    $delete_eval_stmt->bind_param("i", $repo_id);
    $delete_eval_stmt->execute();
    $delete_eval_stmt->close();

    $delete_proj_stmt = $conn->prepare("DELETE FROM projects WHERE repo_id = ?");
    $delete_proj_stmt->bind_param("i", $repo_id);
    $delete_proj_stmt->execute();
    $delete_proj_stmt->close();

    $delete_repo_stmt = $conn->prepare("DELETE FROM repositories WHERE repo_id = ? AND user_id = ?");
    $delete_repo_stmt->bind_param("ii", $repo_id, $user_id);
    $delete_repo_stmt->execute();
    $delete_repo_stmt->close();

    $conn->commit();

    $conn->close();

    header("Location: student_dashboard.php");
    exit;
} catch (Exception $e) {

    $conn->rollback();
    $conn->close();
    die("Error deleting repository: " . $e->getMessage());
}
?>
