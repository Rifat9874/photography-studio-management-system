<?php
session_start();
if (!isset($_SESSION['user_id'])) {
	header("Location: login.php");
	exit();
}
require_once __DIR__ . '/../model/User.php';

$user = new User();
$currentUser = $user->getUserById($_SESSION['user_id']);
$base = "../";
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>My Profile</title>
	<link rel="stylesheet" href="../css/style.css">
	<script src="../js/script.js"></script>
</head>
<body>
	<?php require_once __DIR__ . '/nav.php'; ?>

	<div class="container">
		<h1>My Profile</h1>
		<?php require_once __DIR__ . '/message.php'; ?>

		<div class="box">
			<h2>Profile Picture</h2>
			<?php
			if ($currentUser['profile_pic'] != '') {
				echo "<img class='profile-pic' src='../images/" . $currentUser['profile_pic'] . "'>";
			} else {
				echo "<p>No picture uploaded yet.</p>";
			}
			?>
			<form action="../controller/profile-handler.php" method="post" enctype="multipart/form-data">
				<input type="hidden" name="action" value="upload">
				<label for="profile_pic">Choose a picture</label>
				<input type="file" id="profile_pic" name="profile_pic" required>
				<input type="submit" value="Upload">
			</form>
		</div>

		<div class="box">
			<h2>Account Information</h2>
			<p>Username: <b><?php echo $currentUser['username'] ?></b> &nbsp;
			   Role: <b><?php echo $currentUser['role'] ?></b> &nbsp;
			   Joined: <?php echo $currentUser['created_at'] ?></p>

			<form action="../controller/profile-handler.php" method="post">
				<input type="hidden" name="action" value="update">

				<label for="name">Full Name</label>
				<input type="text" id="name" name="name" value="<?php echo $currentUser['name'] ?>" required>

				<label for="email">Email</label>
				<input type="email" id="email" name="email" value="<?php echo $currentUser['email'] ?>" required>

				<label for="phone">Phone</label>
				<input type="text" id="phone" name="phone" value="<?php echo $currentUser['phone'] ?>">

				<?php if ($currentUser['role'] == 'photographer') { ?>
					<label for="specialization">Specialization</label>
					<select id="specialization" name="specialization">
						<option value="Wedding" <?php if ($currentUser['specialization'] == 'Wedding') echo 'selected' ?>>Wedding</option>
						<option value="Portrait" <?php if ($currentUser['specialization'] == 'Portrait') echo 'selected' ?>>Portrait</option>
						<option value="Event" <?php if ($currentUser['specialization'] == 'Event') echo 'selected' ?>>Event</option>
						<option value="Product" <?php if ($currentUser['specialization'] == 'Product') echo 'selected' ?>>Product</option>
					</select>
				<?php } ?>

				<input type="submit" value="Save Changes">
			</form>
		</div>

		<div class="box">
			<h2>Delete Account</h2>
			<p>This will remove your account and all data connected to it.</p>
			<form action="../controller/profile-handler.php" method="post" onsubmit="return confirmDelete()">
				<input type="hidden" name="action" value="delete">
				<input type="submit" class="danger" value="Delete My Account">
			</form>
		</div>
	</div>
</body>
</html>
