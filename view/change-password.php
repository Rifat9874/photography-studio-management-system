<?php
session_start();
if (!isset($_SESSION['user_id'])) {
	header("Location: login.php");
	exit();
}
$base = "../";
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Change Password</title>
	<link rel="stylesheet" href="../css/style.css">
</head>
<body>
	<?php require_once __DIR__ . '/nav.php'; ?>

	<div class="container">
		<h1>Change Password</h1>
		<?php require_once __DIR__ . '/message.php'; ?>

		<div class="box">
			<form action="../controller/password-handler.php" method="post">
				<input type="hidden" name="action" value="change">

				<label for="old_password">Current Password</label>
				<input type="password" id="old_password" name="old_password" required>

				<label for="new_password">New Password</label>
				<input type="password" id="new_password" name="new_password" required>

				<label for="confirm_password">Confirm New Password</label>
				<input type="password" id="confirm_password" name="confirm_password" required>

				<input type="submit" value="Change Password">
			</form>
		</div>
	</div>
</body>
</html>
