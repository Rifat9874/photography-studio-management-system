<?php
session_start();
if (!isset($_SESSION['user_id'])) {
	header("Location: ../login.php");
	exit();
}
if ($_SESSION['role'] != 'admin') {
	header("Location: ../../index.php");
	exit();
}

require_once __DIR__ . '/../../model/User.php';
require_once __DIR__ . '/../../model/Category.php';
require_once __DIR__ . '/../../model/Complaint.php';
require_once __DIR__ . '/../../model/Booking.php';

$user = new User();
$userList = $user->getAllUsers();

$category = new Category();
$categoryList = $category->getAllCategories();

$complaint = new Complaint();
$complaintList = $complaint->getAllComplaints();
$openCount = $complaint->countByStatus('open');

$booking = new Booking();
$bookingList = $booking->getAllBookings();

$base = "../../";
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Admin Dashboard</title>
	<link rel="stylesheet" href="../../css/style.css">
	<link rel="stylesheet" href="../../css/admin.css">
	<script src="../../js/script.js"></script>
	<script src="../../js/admin.js"></script>
</head>
<body onload="refreshDashboardStats()">
	<?php require_once __DIR__ . '/../nav.php'; ?>

	<div class="container">
		<h1>Admin Dashboard</h1>
		<?php require_once __DIR__ . '/../message.php'; ?>
		<p id="admin_module_note" class="small-note admin-module-note"></p>
		<p id="ajax_note" class="small-note">Dashboard stats will refresh using AJAX + JSON.</p>

		<div class="card-row">
			<div class="card">
				<div class="number" data-stat="total_users"><?php echo count($userList) ?></div>
				Total Users
			</div>
			<div class="card">
				<div class="number" data-stat="categories"><?php echo count($categoryList) ?></div>
				Categories
			</div>
			<div class="card">
				<div class="number" data-stat="bookings"><?php echo count($bookingList) ?></div>
				Bookings
			</div>
			<div class="card">
				<div class="number" data-stat="open_complaints"><?php echo $openCount ?></div>
				Open Complaints
			</div>
		</div>

		<div class="box">
			<h2>Users by Role</h2>
			<table>
				<tr><th>Role</th><th>How many</th></tr>
				<?php
				// count each role with a simple loop
				$roles = ["customer", "photographer", "manager", "admin"];
				for ($r = 0; $r < count($roles); $r++) {
					$total = 0;
					for ($i = 0; $i < count($userList); $i++) {
						if ($userList[$i]['role'] == $roles[$r]) {
							$total = $total + 1;
						}
					}
					echo "<tr><td>" . $roles[$r] . "</td><td>" . $total . "</td></tr>";
				}
				?>
			</table>
		</div>

		<div class="box">
			<h2>Latest Complaints</h2>
			<table>
				<tr><th>ID</th><th>Customer</th><th>Subject</th><th>Status</th></tr>
				<?php
				$limit = count($complaintList);
				if ($limit > 5) {
					$limit = 5;
				}
				for ($i = 0; $i < $limit; $i++) {
					$row = $complaintList[$i];
					$statusClass = str_replace(' ', '-', $row['status']);
					echo "<tr>";
					echo "<td>" . $row['id'] . "</td>";
					echo "<td>" . $row['customer_name'] . "</td>";
					echo "<td>" . $row['subject'] . "</td>";
					echo "<td><span class='status status-" . $statusClass . "'>" . $row['status'] . "</span></td>";
					echo "</tr>";
				}
				if (count($complaintList) == 0) {
					echo "<tr><td colspan='4'>No complaint.</td></tr>";
				}
				?>
			</table>
			<p><a href="complaints.php">Go to complaint management</a></p>
		</div>
	</div>
</body>
</html>
