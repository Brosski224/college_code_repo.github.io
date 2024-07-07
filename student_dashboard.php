<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role_id'] != 1) {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT repo_id, name FROM repositories WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($repo_id, $name);

$repositories = [];
while ($stmt->fetch()) {
    $repositories[] = ['repo_id' => $repo_id, 'name' => $name];
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>Student Dashboard</h1>
        </header>
        <section class="content">
            <h2>Welcome, Student</h2>
            <h3>Your Repositories</h3>
            <ul class="repository-list">
                <?php foreach ($repositories as $repository): ?>
                <li class="repository-item">
                    <a class="link" href="repository.php?repo_id=<?php echo $repository['repo_id']; ?>">
                        <?php echo htmlspecialchars($repository['name']); ?>
                    </a>
                    <form action="delete_repository.php" method="post">
                        <input type="hidden" name="repo_id" value="<?php echo $repository['repo_id']; ?>">
                        <button type="submit">Delete Repository</button>
                    </form>
                </li>
                <?php endforeach; ?>
            </ul>
            <div class="actions">
                <a href="create_repository.php" class="button">Create New Repository</a>
                <a href="logout.php" class="button">Logout</a>
            </div>
        </section>
    </div>
</body>
</html>
