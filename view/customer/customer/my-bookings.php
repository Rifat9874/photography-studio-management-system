<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
	header("Location: ../login.php");
	exit();
}

require_once __DIR__ . '/../../model/Booking.php';
require_once __DIR__ . '/../../model/Review.php';

$booking = new Booking();
$bookingList = $booking->getBookingsByCustomer($_SESSION['user_id']);

$review = new Review();

$base = "../../";
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>My Bookings</title>
	<link rel="stylesheet" href="../../css/style.css">
	<link rel="stylesheet" href="../../css/customer.css">
	<script src="../../js/script.js"></script>
	<script src="../../js/customer.js"></script>
</head>
<body>
	<?php require_once __DIR__ . '/../nav.php'; ?>

	<div class="container">
		<h1>My Bookings</h1>
		<?php require_once __DIR__ . '/../message.php'; ?>

		<div class="box">
			<table>
				<tr>
					<th>ID</th>
					<th>Category</th>
					<th>Package</th>
					<th>Price</th>
					<th>Date</th>
					<th>Time</th>
					<th>Location</th>
					<th>Photographer</th>
					<th>Status</th>
					<th>Actions</th>
				</tr>
				<?php
				for ($i = 0; $i < count($bookingList); $i++) {
					$row = $bookingList[$i];
					$photographer = $row['photographer_name'];
					if ($photographer == null) {
						$photographer = "-";
					}

					// build the action buttons for this row
					$actions = "";
					if ($row['status'] == 'pending') {
						$actions = $actions . "<a href='edit-booking.php?id=" . $row['id'] . "'>Edit</a> ";
						$actions = $actions .
							"<form action='../../controller/booking-handler.php' method='post' style='display:inline'>
								<input type='hidden' name='action' value='cancel'>
								<input type='hidden' name='id' value='" . $row['id'] . "'>
								<input type='submit' class='small-btn' value='Cancel'>
							</form> ";
					}
					if ($row['status'] == 'completed') {
						$existingReview = $review->getReviewByBooking($row['id']);
						if ($existingReview == null) {
							$actions = $actions . "<a href='reviews.php?booking_id=" . $row['id'] . "'>Write Review</a> ";
						} else {
							$actions = $actions . "Reviewed ";
						}
					}
					$actions = $actions .
						"<form action='../../controller/booking-handler.php' method='post' style='display:inline' onsubmit='return confirmDelete()'>
							<input type='hidden' name='action' value='delete'>
							<input type='hidden' name='id' value='" . $row['id'] . "'>
							<input type='submit' class='small-btn danger' value='Delete'>
						</form>";

					echo "<tr>";
					echo "<td>" . $row['id'] . "</td>";
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
					echo "<tr><td colspan='10'>You have no booking yet.</td></tr>";
				}
				?>
			</table>
		</div>
	</div>
</body>
</html>
