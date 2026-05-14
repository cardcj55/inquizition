<?php
require "includes/auth.php";
require "includes/db.php";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>In-Quiz-ition | Take Quiz</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include "includes/header.php"; ?>

<main class="page-content">
    <h2 class="page-heading">Quiz here:</h2>

    <form action="results.php" method="post" class="quiz-form">
        <?php
        $amount = isset($_GET["amount"]) ? intval($_GET["amount"]) : 10;
        $amount = max(1, min($amount, 20));

        $json = file_get_contents("questions.json");
        $questions = json_decode($json, true);

        foreach ($questions as $index => &$question) {
            $question["id"] = $index;
        }
        unset($question);

        $lastQuestionIds = $_SESSION["last_question_ids"] ?? [];

        $unusedQuestions = array_filter($questions, function ($question) use ($lastQuestionIds) {
            return !in_array($question["id"], $lastQuestionIds);
        });

        if (count($unusedQuestions) >= $amount) {
            $questionPool = array_values($unusedQuestions);
        } else {
            $questionPool = $questions;
        }

        shuffle($questionPool);
        $selectedQuestions = array_slice($questionPool, 0, $amount);

        $_SESSION["last_question_ids"] = array_column($selectedQuestions, "id");

        foreach ($selectedQuestions as $index => $q) {
            echo '<div class="question-card">';
            echo '<h3>Question ' . ($index + 1) . ':</h3>';
            echo '<p>' . htmlspecialchars($q["question"]) . '</p>';

            echo '<input type="hidden" name="questions[' . $index . '][question]" value="' . htmlspecialchars($q["question"]) . '">';
            echo '<input type="hidden" name="questions[' . $index . '][correct]" value="' . htmlspecialchars($q["answer"]) . '">';

            foreach (["A", "B", "C", "D"] as $choice) {
                echo '<label class="answer-option">';
                echo '<input type="radio" name="answers[' . $index . ']" value="' . $choice . '" required>';
                echo $choice . '. ' . htmlspecialchars($q[$choice]);
                echo '</label>';
            }

            echo '</div>';
        }
        ?>

        <button type="submit" class="btn large-btn">Submit</button>
    </form>
</main>

<?php include "includes/footer.php"; ?>

</body>
</html>