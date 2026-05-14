<?php
require "includes/db.php";

$token = $_GET["token"] ?? "";
$error = "";
$success = "";

if (!$token) {
    $error = "Invalid reset link.";
} else {
    $stmt = $pdo->prepare("
        SELECT user_id
        FROM users
        WHERE reset_token = ?
        AND reset_expires > NOW()
    ");
    $stmt->execute([$token]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        $error = "This reset link is invalid or expired.";
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && !$error) {
    $password = $_POST["password"];
    $confirmPassword = $_POST["confirm_password"];

    if ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            UPDATE users
            SET password_hash = ?,
                reset_token = NULL,
                reset_expires = NULL
            WHERE reset_token = ?
        ");
        $stmt->execute([$passwordHash, $token]);

        $success = "Password updated. You can now sign in.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password | In-Quiz-ition</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include "includes/header.php"; ?>

<main class="page-content">
    <section class="card auth-card">
        <h2>Reset Password</h2>

        <?php if ($error): ?>
            <p class="error-message"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <?php if ($success): ?>
            <p class="success-message"><?php echo htmlspecialchars($success); ?></p>
            <a href="login.php" class="btn">Return to Login</a>
        <?php elseif (!$error): ?>
            <form method="post">
                <label for="password">New Password</label>
                <input type="password" id="password" name="password" required>

                <label for="confirm-password">Confirm New Password</label>
                <input type="password" id="confirm-password" name="confirm_password" required>

                <button type="submit" class="btn">Reset Password</button>
            </form>
        <?php endif; ?>
    </section>
</main>

<?php include "includes/footer.php"; ?>

</body>
</html>