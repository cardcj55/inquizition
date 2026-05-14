<?php
require "includes/db.php";

$message = "";
$resetLink = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);

    $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        $token = bin2hex(random_bytes(32));
        $expires = date("Y-m-d H:i:s", time() + 3600);

        $stmt = $pdo->prepare("
            UPDATE users
            SET reset_token = ?, reset_expires = ?
            WHERE email = ?
        ");
        $stmt->execute([$token, $expires, $email]);

        $resetLink = "reset_password.php?token=" . urlencode($token);
        $message = "Password reset link created.";
    } else {
        $message = "If that email exists, a reset link has been created.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password | In-Quiz-ition</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include "includes/header.php"; ?>

<main class="page-content">
    <section class="card auth-card">
        <h2>Forgot Password</h2>

        <?php if ($message): ?>
            <p class="success-message"><?php echo htmlspecialchars($message); ?></p>
        <?php endif; ?>

        <?php if ($resetLink): ?>
            <p>Local testing reset link:</p>
            <p>
                <a href="<?php echo htmlspecialchars($resetLink); ?>">
                    <?php echo htmlspecialchars($resetLink); ?>
                </a>
            </p>
        <?php endif; ?>

        <form method="post">
            <label for="email">Enter your account email:</label>
            <input type="email" id="email" name="email" required>

            <button type="submit" class="btn">Create Reset Link</button>
        </form>
    </section>
</main>

<?php include "includes/footer.php"; ?>

</body>
</html>