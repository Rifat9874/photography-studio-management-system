<?php
session_start();
if (!isset($_SESSION['user_id'])) {
	header("Location: ../login.php");
	exit();
}
if ($_SESSION['role'] != 'photographer') {
	header("Location: ../../index.php");
	exit();
}

require_once __DIR__ . '/../../model/Booking.php';
require_once __DIR__ . '/../../model/Portfolio.php';
require_once __DIR__ . '/../../model/Availability.php';
require_once __DIR__ . '/../../model/Review.php';

$photographerId = $_SESSION['user_id'];

$booking = new Booking();
$shootList = $booking->getBookingsByPhotographer($photographerId);

$portfolio = new Portfolio();
$itemList = $portfolio->getItemsByPhotographer($photographerId);

$availability = new Availability();
$slotList = $availability->getSlotsByPhotographer($photographerId);

$review = new Review();
$reviewList = $review->getReviewsByPhotographer($photographerId);

// count upcoming shoots (date is today or later and not completed)
$upcomingCount = 0;
for ($i = 0; $i < count($shootList); $i++) {
	if ($shootList[$i]['booking_date'] >= date('Y-m-d') && $shootList[$i]['status'] != 'completed') {
		$upcomingCount = $upcomingCount + 1;
	}
}

$base = "../../";
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Photographer Dashboard</title>
	<link rel="stylesheet" href="../../css/style.css">
	<link rel="stylesheet" href="../../css/photographer.css">
	<script src="../../js/script.js"></script>
	<script src="../../js/photographer.js"></script>
</head>
<body onload="refreshDashboardStats()">
	<?php require_once __DIR__ . '/../nav.php'; ?>

	<div class="container">
		<h1>Welcome, <?php echo $_SESSION['name'] ?></h1>
		<?php require_once __DIR__ . '/../message.php'; ?>
		<p id="photographer_module_note" class="small-note photographer-module-note"></p>
		<p id="ajax_note" class="small-note">Dashboard stats will refresh using AJAX + JSON.</p>

		<div class="card-row">
			<div class="card">
				<div class="number" data-stat="total_shoots"><?php echo count($shootList) ?></div>
				Total Shoots
			</div>
			<div class="card">
				<div class="number" data-stat="upcoming_shoots"><?php echo $upcomingCount ?></div>
				Upcoming
			</div>
			<div class="card">
				<div class="number" data-stat="portfolio_items"><?php echo count($itemList) ?></div>
				Portfolio Items
			</div>
			<div class="card">
				<div class="number" data-stat="free_slots"><?php echo count($slotList) ?></div>
				Free Slots
			</div>
		</div>

		<div class="box">
			<h2>Next Shoots</h2>
			<table>
				<tr><th>ID</th><th>Customer</th><th>Package</th><th>Date</th><th>Time</th><th>Location</th><th>Status</th></tr>
				<?php
				$shown = 0;
				for ($i = 0; $i < count($shootList); $i++) {
					$row = $shootList[$i];
					if ($row['booking_date'] >= date('Y-m-d') && $shown < 5) {
						echo "<tr>";
						echo "<td>" . $row['id'] . "</td>";
						echo "<td>" . $row['customer_name'] . "</td>";
						echo "<td>" . $row['package_name'] . "</td>";
						echo "<td>" . $row['booking_date'] . "</td>";
						echo "<td>" . $row['booking_time'] . "</td>";
						echo "<td>" . $row['location'] . "</td>";
						echo "<td><span class='status status-" . $row['status'] . "'>" . $row['status'] . "</span></td>";
						echo "</tr>";
						$shown = $shown + 1;
					}
				}
				if ($shown == 0) {
					echo "<tr><td colspan='7'>No upcoming shoot.</td></tr>";
				}
				?>
			</table>
		</div>

		<div class="box">
			<h2>What Customers Said About My Work</h2>
			<table>
				<tr><th>Customer</th><th>Package</th><th>Rating</th><th>Comment</th></tr>
				<?php
				for ($i = 0; $i < count($reviewList); $i++) {
					$row = $reviewList[$i];
					echo "<tr>";
					echo "<td>" . $row['customer_name'] . "</td>";
					echo "<td>" . $row['package_name'] . "</td>";
					echo "<td>" . $row['rating'] . " / 5</td>";
					echo "<td>" . $row['comment'] . "</td>";
					echo "</tr>";
				}
				if (count($reviewList) == 0) {
					echo "<tr><td colspan='4'>No review yet.</td></tr>";
				}
				?>
			</table>
		</div>
	</div>
</body>
</html>
