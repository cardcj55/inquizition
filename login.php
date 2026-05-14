<?php
session_start();
require "includes/db.php";

$loginError = "";
$signupError = "";
$signupSuccess = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (isset($_POST["login_submit"])) {
        $loginId = trim($_POST["login_id"]);
        $password = $_POST["password"];

        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$loginId, $loginId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user["password_hash"])) {
            $_SESSION["user_id"] = $user["user_id"];
            $_SESSION["username"] = $user["username"];
            header("Location: profile.php");
            exit;
        } else {
            $loginError = "Invalid username/email or password.";
        }
    }

    if (isset($_POST["signup_submit"])) {
        $email = trim($_POST["email"]);
        $username = trim($_POST["username"]);
        $password = $_POST["password"];
        $confirmPassword = $_POST["confirm_password"];

        if ($password !== $confirmPassword) {
            $signupError = "Passwords do not match.";
        } else {
            $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ? OR username = ?");
            $stmt->execute([$email, $username]);

            if ($stmt->fetch()) {
                $signupError = "Email or username already exists.";
            } else {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);

                $stmt = $pdo->prepare(
                    "INSERT INTO users (username, email, password_hash) VALUES (?, ?, ?)"
                );
                $stmt->execute([$username, $email, $passwordHash]);

                $signupSuccess = "Account created. You can now sign in.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>In-Quiz-ition | Login or Sign Up</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<?php include "includes/header.php"; ?>

<main class="page-content">
    <section class="auth-layout">
        <div class="card auth-card">
            <h2>Sign In:</h2>
			
			<?php if ($loginError): ?>
				<p class="error-message"><?php echo htmlspecialchars($loginError); ?></p>
			<?php endif; ?>

            <form action="#" method="post">
                <label for="login-id">Username or Email Address</label>
                <input type="text" id="login-id" name="login_id">

                <label for="login-password">Password</label>
                <input type="password" id="login-password" name="password">

                <button type="submit" name="login_submit" class="btn">Submit</button>
            </form>

            <a href="forgot_password.php" class="small-link">Forgot Password?</a>
        </div>

        <div class="card auth-card">
            <h2>Sign Up</h2>
			
			<?php if ($signupError): ?>
				<p class="error-message"><?php echo htmlspecialchars($signupError); ?></p>
			<?php endif; ?>

			<?php if ($signupSuccess): ?>
				<p class="success-message"><?php echo htmlspecialchars($signupSuccess); ?></p>
			<?php endif; ?>

            <form action="#" method="post">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email">

                <label for="username">Username</label>
                <input type="text" id="username" name="username">

                <label for="password">Password</label>
                <input type="password" id="password" name="password">

                <label for="confirm-password">Confirm Password</label>
                <input type="password" id="confirm-password" name="confirm_password">

                <p class="form-note">Check if email of new user already exists.</p>

                <button type="submit" name="signup_submit" class="btn">Submit</button>
            </form>
        </div>
    </section>
</main>

<?php include "includes/footer.php"; ?>

</body>
</html>