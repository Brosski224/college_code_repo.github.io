<?php
session_start();
include 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

$project_id = $_GET['project_id'];
$stmt = $conn->prepare("SELECT projects.name, projects.description, projects.file_path, users.username FROM projects JOIN repositories ON projects.repo_id = repositories.repo_id JOIN users ON repositories.user_id = users.user_id WHERE projects.project_id = ?");
$stmt->bind_param("i", $project_id);
$stmt->execute();
$stmt->bind_result($project_name, $project_description, $file_path, $username);
$stmt->fetch();
$stmt->close();

$evaluations = [];
$eval_stmt = $conn->prepare("SELECT comments, score FROM evaluations WHERE project_id = ?");
$eval_stmt->bind_param("i", $project_id);
$eval_stmt->execute();
$eval_stmt->bind_result($comments, $score);
while ($eval_stmt->fetch()) {
    $evaluations[] = ['comments' => $comments, 'score' => $score];
}
$eval_stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($project_name); ?> - Project</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="container">
        <h2><?php echo htmlspecialchars($project_name); ?></h2>
        <p><?php echo nl2br(htmlspecialchars($project_description)); ?></p>
        <p><strong>Submitted by:</strong> <?php echo htmlspecialchars($username); ?></p>
        <a href="<?php echo $file_path; ?>" download>Download File</a>

        <h3>Evaluations</h3>
        <ul>
            <?php foreach ($evaluations as $evaluation): ?>
            <li>
                <strong>Score:</strong> <span class="score"><?php echo $evaluation['score']; ?></span><br>
                <strong>Comments:</strong> <?php echo nl2br(htmlspecialchars($evaluation['comments'])); ?>
            </li>
            <?php endforeach; ?>
        </ul>

        <?php if ($_SESSION['role_id'] == 2): // Only show Add Evaluation form for teachers ?>
        <h3>Add Evaluation</h3>
        <form action="evaluate_project.php" method="post">
            <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
            <label for="score">Score:</label>
            <input type="number" id="score" name="score" required><br>
            
            <label for="comments">Comments:</label>
            <textarea id="comments" name="comments" required></textarea><br>
            
            <button type="submit">Submit Evaluation</button>
        </form>
        <?php endif; ?>

        <a href="<?php echo $_SESSION['role_id'] == 1 ? 'student_dashboard.php' : 'teacher_dashboard.php'; ?>">Back to Dashboard</a>
    </div>
</body>
</html>
