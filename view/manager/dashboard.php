<?php
session_start();
if (!isset($_SESSION['user_id'])) {
	header("Location: ../login.php");
	exit();
}
if ($_SESSION['role'] != 'manager') {
	header("Location: ../../index.php");
	exit();
}

require_once __DIR__ . '/../../model/Booking.php';
require_once __DIR__ . '/../../model/Package.php';

$booking = new Booking();
$bookingList = $booking->getAllBookings();

$package = new Package();
$packageList = $package->getAllPackages();

$pendingCount = $booking->countByStatus('pending');
$approvedCount = $booking->countByStatus('approved');
$assignedCount = $booking->countByStatus('assigned');

$base = "../../";
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Manager Dashboard</title>
	<link rel="stylesheet" href="../../css/style.css">
	<link rel="stylesheet" href="../../css/manager.css">
	<script src="../../js/script.js"></script>
	<script src="../../js/manager.js"></script>
</head>
<body onload="refreshDashboardStats()">
	<?php require_once __DIR__ . '/../nav.php'; ?>

	<div class="container">
		<h1>Booking Manager Dashboard</h1>
		<?php require_once __DIR__ . '/../message.php'; ?>
		<p id="manager_module_note" class="small-note manager-module-note"></p>
		<p id="ajax_note" class="small-note">Dashboard stats will refresh using AJAX + JSON.</p>

		<div class="card-row">
			<div class="card">
				<div class="number" data-stat="all_bookings"><?php echo count($bookingList) ?></div>
				All Bookings
			</div>
			<div class="card">
				<div class="number" data-stat="pending_requests"><?php echo $pendingCount ?></div>
				Waiting for Approval
			</div>
			<div class="card">
				<div class="number" data-stat="approved_bookings"><?php echo $approvedCount ?></div>
				Approved, Not Assigned
			</div>
			<div class="card">
				<div class="number" data-stat="packages"><?php echo count($packageList) ?></div>
				Packages
			</div>
		</div>

		<div class="box">
			<h2>New Booking Requests</h2>
			<table>
				<tr><th>ID</th><th>Customer</th><th>Package</th><th>Date</th><th>Time</th><th>Location</th><th>Action</th></tr>
				<?php
				$pendingShown = 0;
				for ($i = 0; $i < count($bookingList); $i++) {
					$row = $bookingList[$i];
					if ($row['status'] == 'pending') {
						$pendingShown = $pendingShown + 1;
						echo "<tr>";
						echo "<td>" . $row['id'] . "</td>";
						echo "<td>" . $row['customer_name'] . "</td>";
						echo "<td>" . $row['package_name'] . "</td>";
						echo "<td>" . $row['booking_date'] . "</td>";
						echo "<td>" . $row['booking_time'] . "</td>";
						echo "<td>" . $row['location'] . "</td>";
						echo "<td><a href='bookings.php'>Review it</a></td>";
						echo "</tr>";
					}
				}
				if ($pendingShown == 0) {
					echo "<tr><td colspan='7'>No pending request right now.</td></tr>";
				}
				?>
			</table>
		</div>
	</div>
</body>
</html>
