<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Reset Password</title>
	<link rel="stylesheet" href="../css/style.css">
</head>
<body>
	<div class="login-box">
		<h2 class="center-text">Reset Password</h2>
		<p>Write your username and the email you registered with.</p>

		<?php require_once __DIR__ . '/message.php'; ?>

		<form action="../controller/password-handler.php" method="post">
			<input type="hidden" name="action" value="reset">

			<label for="username">Username</label>
			<input type="text" id="username" name="username" required>

			<label for="email">Email</label>
			<input type="email" id="email" name="email" required>

			<label for="new_password">New Password</label>
			<input type="password" id="new_password" name="new_password" required>

			<label for="confirm_password">Confirm New Password</label>
			<input type="password" id="confirm_password" name="confirm_password" required>

			<input type="submit" value="Reset Password">
		</form>

		<p><a href="login.php">Back to login</a></p>
	</div>
</body>
</html>
