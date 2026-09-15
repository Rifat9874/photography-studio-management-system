<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Register - Photography Studio</title>
	<link rel="stylesheet" href="../css/style.css">
	<script src="../js/script.js"></script>
</head>
<body>
	<div class="login-box">
		<h2 class="center-text">Create Account</h2>

		<?php
		if (isset($_SESSION['register_error'])) {
			echo "<div class='error-message'>";
			for ($i = 0; $i < count($_SESSION['register_error']); $i++) {
				echo $_SESSION['register_error'][$i] . "<br>";
			}
			echo "</div>";
			unset($_SESSION['register_error']);
		}
		?>

		<form action="../controller/register-handler.php" method="post" onsubmit="return validateRegister()">
			<label for="name">Full Name</label>
			<input type="text" id="name" name="name" required>
			<span class="error" id="name_error"></span>

			<label for="username">Username</label>
			<input type="text" id="username" name="username" onkeyup="checkUsername()" required>
			<span class="error" id="username_error"></span>

			<label for="email">Email</label>
			<input type="email" id="email" name="email" required>
			<span class="error" id="email_error"></span>

			<label for="phone">Phone (11 digits)</label>
			<input type="text" id="phone" name="phone" required>
			<span class="error" id="phone_error"></span>

			<label for="role">Register as</label>
			<select id="role" name="role" onchange="toggleSpecialization()">
				<option value="customer">Customer</option>
				<option value="photographer">Photographer</option>
			</select>

			<div id="specialization_box" style="display:none;">
				<label for="specialization">Specialization</label>
				<select id="specialization" name="specialization">
					<option value="Wedding">Wedding</option>
					<option value="Portrait">Portrait</option>
					<option value="Event">Event</option>
					<option value="Product">Product</option>
				</select>
			</div>

			<label for="password">Password</label>
			<input type="password" id="password" name="password" required>
			<span class="error" id="password_error"></span>

			<label for="confirm_password">Confirm Password</label>
			<input type="password" id="confirm_password" name="confirm_password" required>
			<span class="error" id="confirm_password_error"></span>

			<input type="submit" value="Create Account">
		</form>

		<p>Already have an account? <a href="login.php">Login</a></p>
	</div>
</body>
</html>
