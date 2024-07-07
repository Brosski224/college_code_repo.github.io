<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$user_id = $_SESSION['user_id'];
$role_id = $_SESSION['role_id'];  

$repo_id = $_GET['repo_id'];
$stmt = $conn->prepare("SELECT name, description FROM repositories WHERE repo_id = ?");
$stmt->bind_param("i", $repo_id);
$stmt->execute();
$stmt->bind_result($name, $description);
$stmt->fetch();
$stmt->close();

$projects = [];
$project_stmt = $conn->prepare("SELECT project_id, name FROM projects WHERE repo_id = ?");
$project_stmt->bind_param("i", $repo_id);
$project_stmt->execute();
$project_stmt->bind_result($project_id, $project_name);
while ($project_stmt->fetch()) {
    $projects[] = ['project_id' => $project_id, 'name' => $project_name];
}
$project_stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($name); ?> - Repository</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2><?php echo htmlspecialchars($name); ?></h2>
        <p><?php echo nl2br(htmlspecialchars($description)); ?></p>
        
        <h3>Projects</h3>
        <ul>
            <?php foreach ($projects as $project): ?>
            <li>
                <a href="view_repository.php?project_id=<?php echo $project['project_id']; ?>">
                    <?php echo htmlspecialchars($project['name']); ?>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
        
        <?php if ($role_id != 2):  ?>
        <h3>Add Project</h3>
        <form action="add_project.php" method="post" enctype="multipart/form-data">
            <input type="hidden" name="repo_id" value="<?php echo $repo_id; ?>">
            <label for="project_name">Project Name:</label>
            <input type="text" id="project_name" name="project_name" required><br>
            
            <label for="project_description">Description:</label>
            <textarea id="project_description" name="project_description" required></textarea><br>
            
            <label for="file">Choose file:</label>
            <input type="file" id="file" name="file" required><br>
            
            <button type="submit">Add Project</button>
        </form>
        <?php endif; ?>
        
        <a href="student_dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
