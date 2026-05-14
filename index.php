<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require "includes/db.php";

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
    <title>In-Quiz-ition | Home</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include "includes/header.php"; ?>

<main class="page-content">
    <section class="hero">
        <h2>Join the In-quiz-ition!</h2>
        <p>Test your knowledge with a relaxing, simple quiz experience.</p>
    </section>

    <section class="banner">
        <h2>Ready to begin?</h2>
        <p>Take a standard 10-question quiz or choose your own quiz length.</p>
    </section>

    <section class="home-grid">
        <div class="card tall-card">
            <h2>Take a Quiz</h2>
            <p>Start a standard 10-question quiz.</p>
            <p>You may also choose a custom number of questions.</p>

            <form action="quiz.php" method="get" class="quiz-options">
                <label for="amount">Number of questions:</label>
                <select name="amount" id="amount">
                    <option value="10">10 Questions</option>
                    <option value="5">5 Questions</option>
                    <option value="15">15 Questions</option>
                    <option value="20">20 Questions</option>
                </select>

                <button type="submit" class="btn">Start Quiz</button>
            </form>
        </div>

        <div class="card tall-card">
            <h2>Your Rank</h2>

			<div class="rank-placeholder">
				<?php if (isset($_SESSION["user_id"])): ?>

					<?php if ($currentUserRank): ?>
						<p>Your current rank is:</p>
						<h3>#<?php echo htmlspecialchars($currentUserRank); ?></h3>
					<?php else: ?>
						<p>You do not have a rank yet.</p>
						<p>Take a quiz to appear on the leaderboard.</p>
					<?php endif; ?>

				<?php else: ?>

					<p>Sign in to view your current leaderboard rank.</p>
					<a href="login.php" class="btn">Sign In</a>

				<?php endif; ?>
			</div>
        </div>
    </section>
</main>

<?php include "includes/footer.php"; ?>

</body>
</html>