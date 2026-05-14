<?php
session_start();
require "includes/db.php";

$leaderboardStmt = $pdo->query("
    SELECT 
        u.user_id,
        u.username,
        COUNT(qa.attempt_id) AS quizzes_taken,
        MAX((qa.score / qa.total_questions) * 100) AS best_score,
        AVG((qa.score / qa.total_questions) * 100) AS average_score,
        RANK() OVER (
            ORDER BY AVG((qa.score / qa.total_questions) * 100) DESC
        ) AS user_rank
    FROM users u
    INNER JOIN quiz_attempts qa ON u.user_id = qa.user_id
    GROUP BY u.user_id, u.username
    ORDER BY user_rank
    LIMIT 10
");

$leaderboard = $leaderboardStmt->fetchAll(PDO::FETCH_ASSOC);

$currentUserRank = null;

if (isset($_SESSION["user_id"])) {
    $rankStmt = $pdo->prepare("
        SELECT user_rank
        FROM (
            SELECT 
                user_id,
                RANK() OVER (
                    ORDER BY AVG((score / total_questions) * 100) DESC
                ) AS user_rank
            FROM quiz_attempts
            GROUP BY user_id
        ) ranked_users
        WHERE user_id = ?
    ");

    $rankStmt->execute([$_SESSION["user_id"]]);
    $currentUserRank = $rankStmt->fetchColumn();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>In-Quiz-ition | Leaderboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include "includes/header.php"; ?>

<main class="page-content">
    <section class="card leaderboard-card">
        <h2>Top 10 on the Leaderboard</h2>

        <?php if (count($leaderboard) > 0): ?>
            <table class="leaderboard-table">
                <tr>
                    <th>Rank</th>
                    <th>Username</th>
                    <th>Best Score</th>
                    <th>Average</th>
                    <th>Quizzes Taken</th>
                </tr>

                <?php foreach ($leaderboard as $row): ?>
                    <tr>
                        <td>#<?php echo htmlspecialchars($row["user_rank"]); ?></td>
                        <td><?php echo htmlspecialchars($row["username"]); ?></td>
                        <td><?php echo round($row["best_score"], 1); ?>%</td>
                        <td><?php echo round($row["average_score"], 1); ?>%</td>
                        <td><?php echo htmlspecialchars($row["quizzes_taken"]); ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p>No quiz attempts have been recorded yet.</p>
            <a href="quiz.php" class="btn">Take the First Quiz</a>
        <?php endif; ?>
    </section>

    <section class="card current-rank-card">
        <h2>Current User's Rank</h2>

        <?php if (isset($_SESSION["user_id"])): ?>
            <?php if ($currentUserRank): ?>
                <p>Your current rank is <strong>#<?php echo htmlspecialchars($currentUserRank); ?></strong>.</p>
            <?php else: ?>
                <p>You do not have a rank yet. Take a quiz to appear on the leaderboard.</p>
            <?php endif; ?>
        <?php else: ?>
            <p>Sign in to view your ranking.</p>
            <a href="login.php" class="btn">Sign In</a>
        <?php endif; ?>
    </section>
</main>

<?php include "includes/footer.php"; ?>

</body>
</html>