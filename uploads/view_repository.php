<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['repo_id'])) {
    header("Location: login.html");
    exit();
}

$user_id = $_SESSION['user_id'];
$repo_id = $_GET['repo_id'];

// Repository details
$stmt = $conn->prepare("SELECT name, description FROM repositories WHERE repo_id = ?");
$stmt->bind_param("i", $repo_id);
$stmt->execute();
$stmt->bind_result($repo_name, $repo_description);
$stmt->fetch();
$stmt->close();

// Projects in the repository
$stmt = $conn->prepare("SELECT project_id, name, description, file_path FROM projects WHERE repo_id = ?");
$stmt->bind_param("i", $repo_id);
$stmt->execute();
$stmt->bind_result($project_id, $project_name, $project_description, $file_path);
$projects = [];
while ($stmt->fetch()) {
    $projects[] = ['project_id' => $project_id, 'name' => $project_name, 'description' => $project_description, 'file_path' => $file_path];
}
$stmt->close();

// Evaluations
$evaluations = [];
foreach ($projects as &$project) {
    $stmt = $conn->prepare("SELECT comments, grade, score FROM evaluations WHERE project_id = ?");
    $stmt->bind_param("i", $project['project_id']);
    $stmt->execute();
    $stmt->bind_result($comments, $grade, $score);
    while ($stmt->fetch()) {
        $evaluations[] = ['project_id' => $project['project_id'], 'comments' => $comments, 'grade' => $grade, 'score' => $score];
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Repository View</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2>Repository: <?php echo htmlspecialchars($repo_name); ?></h2>
        <p><?php echo htmlspecialchars($repo_description); ?></p>
        
        <h3>Projects</h3>
        <ul>
            <?php foreach ($projects as $project): ?>
                <li>
                    <strong><?php echo htmlspecialchars($project['name']); ?></strong><br>
                    <?php echo htmlspecialchars($project['description']); ?><br>
                    <a href="<?php echo htmlspecialchars($project['file_path']); ?>">Download</a><br>
                    <?php
                    foreach ($evaluations as $evaluation) {
                        if ($evaluation['project_id'] == $project['project_id']) {
                            echo "<p class='evaluation'><strong>Comments:</strong> " . htmlspecialchars($evaluation['comments']) . "</p>";
                            echo "<p class='evaluation'><strong>Grade:</strong> " . htmlspecialchars($evaluation['grade']) . "</p>";
                            echo "<p class='score'><strong>Score:</strong> " . htmlspecialchars($evaluation['score']) . "</p>";
                        }
                    }
                    ?>
                </li>
            <?php endforeach; ?>
        </ul>
        
        <a href="student_dashboard.php">Back to Dashboard</a>
    </div>
</body>
</html>
