<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'manager') {
	header("Location: ../login.php");
	exit();
}

require_once __DIR__ . '/../../model/Booking.php';

$booking = new Booking();
$bookingList = $booking->getAllBookings();

$base = "../../";
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>All Bookings</title>
	<link rel="stylesheet" href="../../css/style.css">
	<link rel="stylesheet" href="../../css/manager.css">
	<script src="../../js/script.js"></script>
	<script src="../../js/manager.js"></script>
</head>
<body>
	<?php require_once __DIR__ . '/../nav.php'; ?>

	<div class="container">
		<h1>Booking Approval &amp; Assignment</h1>
		<?php require_once __DIR__ . '/../message.php'; ?>

		<div class="box">
			<table>
				<tr>
					<th>ID</th><th>Customer</th><th>Phone</th><th>Category</th><th>Package</th>
					<th>Price</th><th>Date</th><th>Time</th><th>Location</th>
					<th>Photographer</th><th>Status</th><th>Actions</th>
				</tr>
				<?php
				for ($i = 0; $i < count($bookingList); $i++) {
					$row = $bookingList[$i];
					$photographer = $row['photographer_name'];
					if ($photographer == null) {
						$photographer = "-";
					}

					// decide which buttons this row should show
					$actions = "";
					if ($row['status'] == 'pending') {
						$actions = $actions .
							"<form action='../../controller/assign-handler.php' method='post' style='display:inline'>
								<input type='hidden' name='action' value='approve'>
								<input type='hidden' name='id' value='" . $row['id'] . "'>
								<input type='submit' class='small-btn success-btn' value='Approve'>
							</form>
							<form action='../../controller/assign-handler.php' method='post' style='display:inline'>
								<input type='hidden' name='action' value='reject'>
								<input type='hidden' name='id' value='" . $row['id'] . "'>
								<input type='submit' class='small-btn danger' value='Reject'>
							</form> ";
					}
					if ($row['status'] == 'approved') {
						$actions = $actions . "<a href='assign.php?id=" . $row['id'] . "'>Assign Photographer</a> ";
					}
					if ($row['status'] == 'assigned') {
						$actions = $actions . "<a href='assign.php?id=" . $row['id'] . "'>Change Photographer</a> ";
					}
					$actions = $actions .
						"<form action='../../controller/assign-handler.php' method='post' style='display:inline' onsubmit='return confirmDelete()'>
							<input type='hidden' name='action' value='delete'>
							<input type='hidden' name='id' value='" . $row['id'] . "'>
							<input type='submit' class='small-btn danger' value='Delete'>
						</form>";

					echo "<tr>";
					echo "<td>" . $row['id'] . "</td>";
					echo "<td>" . $row['customer_name'] . "</td>";
					echo "<td>" . $row['customer_phone'] . "</td>";
					echo "<td>" . $row['category_name'] . "</td>";
					echo "<td>" . $row['package_name'] . "</td>";
					echo "<td>" . $row['price'] . "</td>";
					echo "<td>" . $row['booking_date'] . "</td>";
					echo "<td>" . $row['booking_time'] . "</td>";
					echo "<td>" . $row['location'] . "</td>";
					echo "<td>" . $photographer . "</td>";
					echo "<td><span class='status status-" . $row['status'] . "'>" . $row['status'] . "</span></td>";
					echo "<td>" . $actions . "</td>";
					echo "</tr>";
				}
				if (count($bookingList) == 0) {
					echo "<tr><td colspan='12'>No booking in the system.</td></tr>";
				}
				?>
			</table>
		</div>
	</div>
</body>
</html>
