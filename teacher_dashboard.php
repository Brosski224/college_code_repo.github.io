<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 2) {
    header("Location: login.html");
    exit;
}

$stmt = $conn->prepare("SELECT repo_id, name, username FROM repositories JOIN users ON repositories.user_id = users.user_id");
$stmt->execute();
$stmt->bind_result($repo_id, $repo_name, $student_name);

$repositories = [];
while ($stmt->fetch()) {
    $repositories[] = ['repo_id' => $repo_id, 'repo_name' => $repo_name, 'student_name' => $student_name];
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Teacher Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>Teacher Dashboard</h1>
        </header>
        <section class="content">
            <h2>Welcome, Teacher</h2>
            <h3>Student Repositories</h3>
            <ul class="repository-list">
                <?php foreach ($repositories as $repository): ?>
                <li class="repository-item">
                    <a class="link" href="repository.php?repo_id=<?php echo $repository['repo_id']; ?>">
                        <?php echo htmlspecialchars($repository['repo_name']) . " by " . htmlspecialchars($repository['student_name']); ?>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
            <div class="actions">
                <a href="logout.php" class="button">Logout</a>
            </div>
        </section>
    </div>
</body>
</html>
