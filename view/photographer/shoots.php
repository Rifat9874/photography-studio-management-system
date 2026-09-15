<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'photographer') {
	header("Location: ../login.php");
	exit();
}

require_once __DIR__ . '/../../model/Booking.php';

$booking = new Booking();
$shootList = $booking->getBookingsByPhotographer($_SESSION['user_id']);

$today = date('Y-m-d');
$base = "../../";
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>My Shoots</title>
	<link rel="stylesheet" href="../../css/style.css">
	<link rel="stylesheet" href="../../css/photographer.css">
</head>
<body>
	<?php require_once __DIR__ . '/../nav.php'; ?>

	<div class="container">
		<h1>Assigned Shoots</h1>
		<?php require_once __DIR__ . '/../message.php'; ?>

		<div class="box">
			<h2>Upcoming Shoots</h2>
			<table>
				<tr><th>ID</th><th>Customer</th><th>Phone</th><th>Package</th><th>Date</th><th>Time</th><th>Location</th><th>Note</th><th>Status</th><th>Update Status</th></tr>
				<?php
				$upcoming = 0;
				for ($i = 0; $i < count($shootList); $i++) {
					$row = $shootList[$i];
					if ($row['booking_date'] >= $today && $row['status'] != 'completed') {
						$upcoming = $upcoming + 1;
						echo "<tr>";
						echo "<td>" . $row['id'] . "</td>";
						echo "<td>" . $row['customer_name'] . "</td>";
						echo "<td>" . $row['customer_phone'] . "</td>";
						echo "<td>" . $row['package_name'] . "</td>";
						echo "<td>" . $row['booking_date'] . "</td>";
						echo "<td>" . $row['booking_time'] . "</td>";
						echo "<td>" . $row['location'] . "</td>";
						echo "<td>" . $row['note'] . "</td>";
						echo "<td><span class='status status-" . $row['status'] . "'>" . $row['status'] . "</span></td>";
						echo "<td>
							<form action='../../controller/booking-handler.php' method='post'>
								<input type='hidden' name='action' value='shoot_status'>
								<input type='hidden' name='id' value='" . $row['id'] . "'>
								<select name='status'>
									<option value='assigned'>assigned</option>
									<option value='completed'>completed</option>
								</select>
								<input type='submit' class='small-btn' value='Save'>
							</form>
						</td>";
						echo "</tr>";
					}
				}
				if ($upcoming == 0) {
					echo "<tr><td colspan='10'>No upcoming shoot assigned to you.</td></tr>";
				}
				?>
			</table>
		</div>

		<div class="box">
			<h2>Past / Completed Shoots</h2>
			<table>
				<tr><th>ID</th><th>Customer</th><th>Package</th><th>Date</th><th>Location</th><th>Status</th></tr>
				<?php
				$past = 0;
				for ($i = 0; $i < count($shootList); $i++) {
					$row = $shootList[$i];
					if ($row['booking_date'] < $today || $row['status'] == 'completed') {
						$past = $past + 1;
						echo "<tr>";
						echo "<td>" . $row['id'] . "</td>";
						echo "<td>" . $row['customer_name'] . "</td>";
						echo "<td>" . $row['package_name'] . "</td>";
						echo "<td>" . $row['booking_date'] . "</td>";
						echo "<td>" . $row['location'] . "</td>";
						echo "<td><span class='status status-" . $row['status'] . "'>" . $row['status'] . "</span></td>";
						echo "</tr>";
					}
				}
				if ($past == 0) {
					echo "<tr><td colspan='6'>No past shoot.</td></tr>";
				}
				?>
			</table>
		</div>
	</div>
</body>
</html>
