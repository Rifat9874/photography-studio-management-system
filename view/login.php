<?php
session_start();
if (isset($_SESSION['user_id'])) {
	header("Location: ../index.php");
	exit();
}

$lastLoginId = "";
if (isset($_COOKIE['last_login_id'])) {
	$lastLoginId = $_COOKIE['last_login_id'];
} else if (isset($_COOKIE['last_username'])) {
	$lastLoginId = $_COOKIE['last_username'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Login - Photography Studio</title>
	<link rel="stylesheet" href="../css/style.css">
</head>
<body class="auth-page">
	<div class="login-wrapper">
		<div class="login-left">
			<div class="camera-logo">[ camera ]</div>
			<h2>PHOTO STUDIO</h2>
			<p>Management System</p>
			<div class="studio-picture"></div>
		</div>

		<div class="login-right">
			<h1>Welcome Back!</h1>
			<p>Login to your account</p>

			<?php require_once __DIR__ . '/message.php'; ?>

			<?php
			if (isset($_SESSION['login_error'])) {
				echo "<div class='error-message'>" . $_SESSION['login_error'] . "</div>";
				unset($_SESSION['login_error']);
			}
			?>

			<form action="../controller/login-handler.php" method="post">
				<label for="login_id">Username or Email</label>
				<input type="text" id="login_id" name="login_id" value="<?php echo htmlspecialchars($lastLoginId) ?>" required>

				<label for="password">Password</label>
				<input type="password" id="password" name="password" required>

				<input type="submit" value="Login">
			</form>

			<p>New here? <a href="register.php">Create an account</a></p>
			<p><a href="reset-password.php">Forgot password?</a></p>
		</div>
	</div>
</body>
</html>
