<?php
require "includes/auth.php";
require "includes/db.php";

$userId = $_SESSION["user_id"];

$message = "";
$error = "";

/* Handle profile update */
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    /* Update bio */
    if (isset($_POST["update_bio"])) {
        $bio = trim($_POST["bio"]);

        $stmt = $pdo->prepare("UPDATE users SET bio = ? WHERE user_id = ?");
        $stmt->execute([$bio, $userId]);

        $message = "Bio updated successfully.";
    }

    /* Update password */
    if (isset($_POST["update_password"])) {
        $newPassword = $_POST["new_password"];
        $confirmPassword = $_POST["confirm_password"];

        if ($newPassword !== $confirmPassword) {
            $error = "Passwords do not match.";
        } elseif (strlen($newPassword) < 6) {
            $error = "Password must be at least 6 characters.";
        } else {
            $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
            $stmt->execute([$passwordHash, $userId]);

            $message = "Password updated successfully.";
        }
    }

    /* Update profile picture */
    if (isset($_POST["update_picture"])) {
        if (isset($_FILES["profile_picture"]) && $_FILES["profile_picture"]["error"] === UPLOAD_ERR_OK) {
            $allowedTypes = ["image/jpeg", "image/png", "image/gif"];
            $fileType = mime_content_type($_FILES["profile_picture"]["tmp_name"]);

            if (!in_array($fileType, $allowedTypes)) {
                $error = "Only JPG, PNG, and GIF files are allowed.";
            } else {
                $uploadDir = "uploads/";

                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }

                $extension = pathinfo($_FILES["profile_picture"]["name"], PATHINFO_EXTENSION);
                $fileName = "profile_" . $userId . "_" . time() . "." . $extension;
                $filePath = $uploadDir . $fileName;

                if (move_uploaded_file($_FILES["profile_picture"]["tmp_name"], $filePath)) {
                    $stmt = $pdo->prepare("UPDATE users SET profile_picture = ? WHERE user_id = ?");
                    $stmt->execute([$filePath, $userId]);

                    $message = "Profile picture updated successfully.";
                } else {
                    $error = "Image upload failed.";
                }
            }
        } else {
            $error = "Please choose an image to upload.";
        }
    }
}

/* Get user info */
$userStmt = $pdo->prepare("SELECT username, email, bio, profile_picture, created_at FROM users WHERE user_id = ?");
$userStmt->execute([$userId]);
$user = $userStmt->fetch(PDO::FETCH_ASSOC);

/* Get stats */
$statsStmt = $pdo->prepare("
    SELECT 
        COUNT(*) AS quizzes_taken,
        AVG((score / total_questions) * 100) AS average_score,
        MAX((score / total_questions) * 100) AS best_score
    FROM quiz_attempts
    WHERE user_id = ?
");
$statsStmt->execute([$userId]);
$stats = $statsStmt->fetch(PDO::FETCH_ASSOC);

/* Get quiz history */
$historyStmt = $pdo->prepare("
    SELECT score, total_questions, taken_at
    FROM quiz_attempts
    WHERE user_id = ?
    ORDER BY taken_at DESC
");
$historyStmt->execute([$userId]);
$history = $historyStmt->fetchAll(PDO::FETCH_ASSOC);

$averageScore = $stats["average_score"] !== null ? round($stats["average_score"], 1) : 0;
$bestScore = $stats["best_score"] !== null ? round($stats["best_score"], 1) : 0;
$quizzesTaken = $stats["quizzes_taken"] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>In-Quiz-ition | User Profile</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include "includes/header.php"; ?>

<main class="page-content">

    <?php if ($message): ?>
        <p class="success-message"><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <?php if ($error): ?>
        <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>

    <section class="profile-layout">
        <div class="profile-info card">
            <h2>User Profile</h2>

            <?php if (!empty($user["profile_picture"])): ?>
                <img 
                    src="<?php echo htmlspecialchars($user["profile_picture"]); ?>" 
                    alt="Profile Picture" 
                    class="profile-picture"
                >
            <?php else: ?>
                <div class="profile-picture placeholder-picture">No Image</div>
            <?php endif; ?>

            <p><strong>Username:</strong> <?php echo htmlspecialchars($user["username"]); ?></p>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user["email"]); ?></p>
            <p><strong>Member Since:</strong> <?php echo htmlspecialchars($user["created_at"]); ?></p>

            <p><strong>Bio:</strong></p>
            <p>
                <?php 
                echo !empty($user["bio"]) 
                    ? htmlspecialchars($user["bio"]) 
                    : "No bio added yet."; 
                ?>
            </p>
        </div>

        <div class="card profile-stats">
            <h2>Your Quiz Stats</h2>

            <p><strong>Average Score:</strong> <?php echo $averageScore; ?>%</p>
            <p><strong>Best Score:</strong> <?php echo $bestScore; ?>%</p>
            <p><strong>Quizzes Taken:</strong> <?php echo htmlspecialchars($quizzesTaken); ?></p>
        </div>
    </section>

    <section class="profile-edit-grid">
        <div class="card">
            <h2>Edit Bio</h2>

            <form method="post">
                <label for="bio">Bio</label>
                <textarea id="bio" name="bio" rows="5"><?php echo htmlspecialchars($user["bio"] ?? ""); ?></textarea>

                <button type="submit" name="update_bio" class="btn">Update Bio</button>
            </form>
        </div>

        <div class="card">
            <h2>Change Password</h2>

            <form method="post">
                <label for="new-password">New Password</label>
                <input type="password" id="new-password" name="new_password" required>

                <label for="confirm-password">Confirm Password</label>
                <input type="password" id="confirm-password" name="confirm_password" required>

                <button type="submit" name="update_password" class="btn">Update Password</button>
            </form>
        </div>

        <div class="card">
            <h2>Change Profile Picture</h2>

            <form method="post" enctype="multipart/form-data">
                <label for="profile-picture">Choose Image</label>
                <input type="file" id="profile-picture" name="profile_picture" accept="image/png, image/jpeg, image/gif" required>

                <button type="submit" name="update_picture" class="btn">Upload Picture</button>
            </form>
        </div>
    </section>

    <section class="card history-card">
        <h2>Quiz History</h2>

        <?php if (count($history) > 0): ?>
            <table class="leaderboard-table">
                <tr>
                    <th>Date Taken</th>
                    <th>Score</th>
                    <th>Percentage</th>
                </tr>

                <?php foreach ($history as $attempt): ?>
                    <?php $percentage = round(($attempt["score"] / $attempt["total_questions"]) * 100, 1); ?>

                    <tr>
                        <td><?php echo htmlspecialchars($attempt["taken_at"]); ?></td>
                        <td><?php echo htmlspecialchars($attempt["score"]); ?>/<?php echo htmlspecialchars($attempt["total_questions"]); ?></td>
                        <td><?php echo $percentage; ?>%</td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>You have not taken any quizzes yet.</p>
            <a href="quiz.php" class="btn">Take Your First Quiz</a>
        <?php endif; ?>
    </section>
</main>

<?php include "includes/footer.php"; ?>

</body>
</html>