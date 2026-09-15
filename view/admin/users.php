<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
	header("Location: ../login.php");
	exit();
}

require_once __DIR__ . '/../../model/User.php';

$user = new User();
$userList = $user->getAllUsers();

$base = "../../";
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>User Management</title>
	<link rel="stylesheet" href="../../css/style.css">
	<link rel="stylesheet" href="../../css/admin.css">
	<script src="../../js/script.js"></script>
	<script src="../../js/admin.js"></script>
</head>
<body>
	<?php require_once __DIR__ . '/../nav.php'; ?>

	<div class="container">
		<h1>User Management</h1>
		<?php require_once __DIR__ . '/../message.php'; ?>

		<div class="box">
			<table>
				<tr>
					<th>ID</th><th>Name</th><th>Username</th><th>Email</th><th>Phone</th>
					<th>Role</th><th>Specialization</th><th>Status</th><th>Joined</th><th>Actions</th>
				</tr>
				<?php
				for ($i = 0; $i < count($userList); $i++) {
					$row = $userList[$i];

					$actions = "";
					if ($row['id'] == $_SESSION['user_id']) {
						$actions = "This is you";
					} else {
						if ($row['status'] == 'active') {
							$actions = $actions .
								"<form action='../../controller/user-handler.php' method='post' style='display:inline'>
									<input type='hidden' name='action' value='deactivate'>
									<input type='hidden' name='id' value='" . $row['id'] . "'>
									<input type='submit' class='small-btn' value='Deactivate'>
								</form> ";
						} else {
							$actions = $actions .
								"<form action='../../controller/user-handler.php' method='post' style='display:inline'>
									<input type='hidden' name='action' value='activate'>
									<input type='hidden' name='id' value='" . $row['id'] . "'>
									<input type='submit' class='small-btn success-btn' value='Activate'>
								</form> ";
						}
						$actions = $actions .
							"<form action='../../controller/user-handler.php' method='post' style='display:inline' onsubmit='return confirmDelete()'>
								<input type='hidden' name='action' value='delete'>
								<input type='hidden' name='id' value='" . $row['id'] . "'>
								<input type='submit' class='small-btn danger' value='Remove'>
							</form>";
					}

					echo "<tr>";
					echo "<td>" . $row['id'] . "</td>";
					echo "<td>" . $row['name'] . "</td>";
					echo "<td>" . $row['username'] . "</td>";
					echo "<td>" . $row['email'] . "</td>";
					echo "<td>" . $row['phone'] . "</td>";
					echo "<td>" . $row['role'] . "</td>";
					echo "<td>" . $row['specialization'] . "</td>";
					echo "<td><span class='status status-" . $row['status'] . "'>" . $row['status'] . "</span></td>";
					echo "<td>" . $row['created_at'] . "</td>";
					echo "<td>" . $actions . "</td>";
					echo "</tr>";
				}
				?>
			</table>
		</div>
	</div>
</body>
</html>
