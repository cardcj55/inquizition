<?php
	require "includes/auth.php";
	require "includes/db.php";

	$attemptId = $_GET["attempt_id"] ?? 0;

	$stmt = $pdo->prepare("
		SELECT score, total_questions, taken_at
		FROM quiz_attempts
		WHERE attempt_id = ?
		AND user_id = ?
	");
	$stmt->execute([$attemptId, $_SESSION["user_id"]]);
	$attempt = $stmt->fetch(PDO::FETCH_ASSOC);

	if (!$attempt) {
		header("Location: profile.php");
		exit;
	}

	$score = $attempt["score"];
	$total = $attempt["total_questions"];
	$percent = $total > 0 ? round(($score / $total) * 100) : 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>In-Quiz-ition | Results</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include "includes/header.php"; ?>

<main class="page-content">
    <section class="results-layout">
        <div class="card results-card">
            <h2>Quiz Results</h2>

            <p><strong>Date Taken:</strong> <?php echo htmlspecialchars($attempt["taken_at"]); ?></p>
            <p><strong>Score:</strong> <?php echo htmlspecialchars($score); ?> out of <?php echo htmlspecialchars($total); ?></p>
            <p><strong>Percentage:</strong> <?php echo htmlspecialchars($percent); ?>%</p>

            <a href="quiz.php" class="btn">Take another quiz</a>
            <a href="profile.php" class="btn">View Profile</a>
        </div>

        <aside class="results-side">
            <div class="card score-card">
                <h2>Score:</h2>
                <p><?php echo htmlspecialchars($score); ?> out of <?php echo htmlspecialchars($total); ?></p>
                <p><?php echo htmlspecialchars($percent); ?>%</p>
            </div>
        </aside>
    </section>
</main>

<?php include "includes/footer.php"; ?>

</body>
</html>