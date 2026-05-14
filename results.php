<?php
	require "includes/auth.php";
	require "includes/db.php";

	$questions = $_POST["questions"] ?? [];
	$answers = $_POST["answers"] ?? [];

	if (count($questions) === 0) {
		header("Location: quiz.php");
		exit;
	}

	$score = 0;
	$total = count($questions);

	foreach ($questions as $index => $q) {
		$userAnswer = $answers[$index] ?? "";

		if ($userAnswer === $q["correct"]) {
			$score++;
		}
	}

	$stmt = $pdo->prepare("
		INSERT INTO quiz_attempts (user_id, score, total_questions)
		VALUES (?, ?, ?)
	");
	$stmt->execute([$_SESSION["user_id"], $score, $total]);

	$attemptId = $pdo->lastInsertId();

	header("Location: view_results.php?attempt_id=" . $attemptId);
	exit;
?>