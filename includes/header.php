<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$headerUser = null;

if (isset($_SESSION["user_id"])) {
    require_once __DIR__ . "/db.php";

    $stmt = $pdo->prepare("
        SELECT profile_picture
        FROM users
        WHERE user_id = ?
    ");
    $stmt->execute([$_SESSION["user_id"]]);

    $headerUser = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<header class="site-header">
    <div class="brand-area">
        <a href="index.php" class="logo-circle">
			<img 
				src="images/inquizition_logo_circle.png" 
				alt="In-quiz-ition Logo"
				class="site-logo"
    >
</a>
        <h1 class="site-title">In-Quiz-ition</h1>
    </div>

    <nav class="main-nav">
        <a href="index.php">Home</a>
        <span>|</span>
        <a href="quiz.php">Take a Quiz</a>
        <span>|</span>
        <a href="leaderboard.php">Leaderboard</a>

        <?php if (isset($_SESSION["user_id"])): ?>
            <span>|</span>
            <a href="logout.php">Logout</a>
        <?php endif; ?>
    </nav>

    <a 
        class="profile-circle-link" 
        href="<?php echo isset($_SESSION["user_id"]) ? "profile.php" : "login.php"; ?>"
    >
        <?php if (isset($_SESSION["user_id"]) && !empty($headerUser["profile_picture"])): ?>
            <img 
                src="<?php echo htmlspecialchars($headerUser["profile_picture"]); ?>" 
                alt="Profile Picture"
                class="header-profile-picture"
            >
        <?php else: ?>
            <div class="header-profile-placeholder">
                Sign In<br>/ Up
            </div>
        <?php endif; ?>
    </a>
</header>

