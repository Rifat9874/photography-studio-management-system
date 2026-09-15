<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
	header("Location: ../login.php");
	exit();
}

require_once __DIR__ . '/../../model/Review.php';
require_once __DIR__ . '/../../model/Booking.php';

$customerId = $_SESSION['user_id'];

$review = new Review();
$reviewList = $review->getReviewsByCustomer($customerId);

$booking = new Booking();
$bookingList = $booking->getBookingsByCustomer($customerId);

// which booking should the form open with (comes from the My Bookings page)
$selectedBookingId = isset($_GET['booking_id']) ? $_GET['booking_id'] : '';

$base = "../../";
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>My Reviews</title>
	<link rel="stylesheet" href="../../css/style.css">
	<link rel="stylesheet" href="../../css/customer.css">
	<script src="../../js/script.js"></script>
	<script src="../../js/customer.js"></script>
</head>
<body>
	<?php require_once __DIR__ . '/../nav.php'; ?>

	<div class="container">
		<h1>Reviews &amp; Feedback</h1>
		<?php require_once __DIR__ . '/../message.php'; ?>

		<div class="box">
			<h2>Write a Review</h2>
			<form action="../../controller/review-handler.php" method="post">
				<input type="hidden" name="action" value="create">

				<label for="booking_id">Completed Shoot</label>
				<select id="booking_id" name="booking_id" required>
					<option value="">-- select a completed shoot --</option>
					<?php
					for ($i = 0; $i < count($bookingList); $i++) {
						$b = $bookingList[$i];
						if ($b['status'] == 'completed' && $review->getReviewByBooking($b['id']) == null) {
							$selected = "";
							if ($b['id'] == $selectedBookingId) {
								$selected = "selected";
							}
							echo "<option value='" . $b['id'] . "' " . $selected . ">#" . $b['id'] . " - " .
								 $b['package_name'] . " (" . $b['booking_date'] . ")</option>";
						}
					}
					?>
				</select>

				<label for="rating">Rating (1 to 5)</label>
				<select id="rating" name="rating">
					<option value="5">5 - Excellent</option>
					<option value="4">4 - Good</option>
					<option value="3">3 - Average</option>
					<option value="2">2 - Poor</option>
					<option value="1">1 - Very Bad</option>
				</select>

				<label for="comment">Comment</label>
				<textarea id="comment" name="comment"></textarea>

				<input type="submit" value="Submit Review">
			</form>
		</div>

		<div class="box">
			<h2>My Reviews</h2>
			<table>
				<tr><th>ID</th><th>Package</th><th>Shoot Date</th><th>Rating</th><th>Comment</th><th>Actions</th></tr>
				<?php
				for ($i = 0; $i < count($reviewList); $i++) {
					$row = $reviewList[$i];
					echo "<tr>";
					echo "<td>" . $row['id'] . "</td>";
					echo "<td>" . $row['package_name'] . "</td>";
					echo "<td>" . $row['booking_date'] . "</td>";
					echo "<td>" . $row['rating'] . " / 5</td>";
					echo "<td>" . $row['comment'] . "</td>";
					echo "<td><a href='edit-review.php?id=" . $row['id'] . "'>Edit</a>
						<form action='../../controller/review-handler.php' method='post' style='display:inline' onsubmit='return confirmDelete()'>
							<input type='hidden' name='action' value='delete'>
							<input type='hidden' name='id' value='" . $row['id'] . "'>
							<input type='submit' class='small-btn danger' value='Delete'>
						</form></td>";
					echo "</tr>";
				}
				if (count($reviewList) == 0) {
					echo "<tr><td colspan='6'>You have not written any review yet.</td></tr>";
				}
				?>
			</table>
		</div>
	</div>
</body>
</html>
