<?php
session_start();
// only a logged in customer can open this page
if (!isset($_SESSION['user_id'])) {
	header("Location: ../login.php");
	exit();
}
if ($_SESSION['role'] != 'customer') {
	header("Location: ../../index.php");
	exit();
}

require_once __DIR__ . '/../../model/Booking.php';
require_once __DIR__ . '/../../model/Complaint.php';

$booking = new Booking();
$bookingList = $booking->getBookingsByCustomer($_SESSION['user_id']);

$complaint = new Complaint();
$complaintList = $complaint->getComplaintsByCustomer($_SESSION['user_id']);

// count the bookings by status with a simple loop
$pendingCount = 0;
$completedCount = 0;
for ($i = 0; $i < count($bookingList); $i++) {
	if ($bookingList[$i]['status'] == 'pending') {
		$pendingCount = $pendingCount + 1;
	}
	if ($bookingList[$i]['status'] == 'completed') {
		$completedCount = $completedCount + 1;
	}
}

$base = "../../";
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Customer Dashboard</title>
	<link rel="stylesheet" href="../../css/style.css">
	<link rel="stylesheet" href="../../css/customer.css">
	<script src="../../js/script.js"></script>
	<script src="../../js/customer.js"></script>
</head>
<body onload="refreshDashboardStats()">
	<?php require_once __DIR__ . '/../nav.php'; ?>

	<div class="container">
		<h1>Welcome, <?php echo $_SESSION['name'] ?></h1>
		<?php require_once __DIR__ . '/../message.php'; ?>
		<p id="customer_module_note" class="small-note customer-module-note"></p>
		<p id="ajax_note" class="small-note">Dashboard stats will refresh using AJAX + JSON.</p>

		<div class="card-row">
			<div class="card">
				<div class="number" data-stat="total_bookings"><?php echo count($bookingList) ?></div>
				Total Bookings
			</div>
			<div class="card">
				<div class="number" data-stat="pending_bookings"><?php echo $pendingCount ?></div>
				Pending
			</div>
			<div class="card">
				<div class="number" data-stat="completed_bookings"><?php echo $completedCount ?></div>
				Completed
			</div>
			<div class="card">
				<div class="number" data-stat="my_complaints"><?php echo count($complaintList) ?></div>
				My Complaints
			</div>
		</div>

		<div class="box">
			<h2>My Latest Bookings</h2>
			<table>
				<tr>
					<th>ID</th>
					<th>Package</th>
					<th>Date</th>
					<th>Time</th>
					<th>Photographer</th>
					<th>Status</th>
				</tr>
				<?php
				// show maximum 5 rows on the dashboard
				$limit = count($bookingList);
				if ($limit > 5) {
					$limit = 5;
				}
				for ($i = 0; $i < $limit; $i++) {
					$row = $bookingList[$i];
					$photographer = $row['photographer_name'];
					if ($photographer == null) {
						$photographer = "Not assigned yet";
					}
					echo "<tr>";
					echo "<td>" . $row['id'] . "</td>";
					echo "<td>" . $row['package_name'] . "</td>";
					echo "<td>" . $row['booking_date'] . "</td>";
					echo "<td>" . $row['booking_time'] . "</td>";
					echo "<td>" . $photographer . "</td>";
					echo "<td><span class='status status-" . $row['status'] . "'>" . $row['status'] . "</span></td>";
					echo "</tr>";
				}
				if (count($bookingList) == 0) {
					echo "<tr><td colspan='6'>You have no booking yet.</td></tr>";
				}
				?>
			</table>
			<p><a href="new-booking.php">Make a new booking</a></p>
		</div>
	</div>
</body>
</html>
