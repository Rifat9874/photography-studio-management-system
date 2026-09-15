<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
	header("Location: ../login.php");
	exit();
}

require_once __DIR__ . '/../../model/Complaint.php';
require_once __DIR__ . '/../../model/Booking.php';

$customerId = $_SESSION['user_id'];

$complaint = new Complaint();
$complaintList = $complaint->getComplaintsByCustomer($customerId);

$booking = new Booking();
$bookingList = $booking->getBookingsByCustomer($customerId);

$base = "../../";
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>My Complaints</title>
	<link rel="stylesheet" href="../../css/style.css">
	<link rel="stylesheet" href="../../css/customer.css">
</head>
<body>
	<?php require_once __DIR__ . '/../nav.php'; ?>

	<div class="container">
		<h1>Complaints</h1>
		<?php require_once __DIR__ . '/../message.php'; ?>

		<div class="box">
			<h2>Submit a Complaint</h2>
			<form action="../../controller/complaint-handler.php" method="post">
				<input type="hidden" name="action" value="create">

				<label for="booking_id">Related Booking (optional)</label>
				<select id="booking_id" name="booking_id">
					<option value="">-- not related to a booking --</option>
					<?php
					for ($i = 0; $i < count($bookingList); $i++) {
						$b = $bookingList[$i];
						echo "<option value='" . $b['id'] . "'>#" . $b['id'] . " - " .
							 $b['package_name'] . " (" . $b['booking_date'] . ")</option>";
					}
					?>
				</select>

				<label for="subject">Subject</label>
				<input type="text" id="subject" name="subject" required>

				<label for="message">Message</label>
				<textarea id="message" name="message" required></textarea>

				<input type="submit" value="Submit Complaint">
			</form>
		</div>

		<div class="box">
			<h2>My Complaints and Admin Replies</h2>
			<table>
				<tr><th>ID</th><th>Booking</th><th>Subject</th><th>Message</th><th>Status</th><th>Admin Reply</th><th>Date</th></tr>
				<?php
				for ($i = 0; $i < count($complaintList); $i++) {
					$row = $complaintList[$i];
					$bookingText = $row['booking_id'];
					if ($bookingText == null) {
						$bookingText = "-";
					} else {
						$bookingText = "#" . $bookingText;
					}
					$reply = $row['admin_reply'];
					if ($reply == null || $reply == '') {
						$reply = "No reply yet";
					}
					$statusClass = str_replace(' ', '-', $row['status']);

					echo "<tr>";
					echo "<td>" . $row['id'] . "</td>";
					echo "<td>" . $bookingText . "</td>";
					echo "<td>" . $row['subject'] . "</td>";
					echo "<td>" . $row['message'] . "</td>";
					echo "<td><span class='status status-" . $statusClass . "'>" . $row['status'] . "</span></td>";
					echo "<td>" . $reply . "</td>";
					echo "<td>" . $row['created_at'] . "</td>";
					echo "</tr>";
				}
				if (count($complaintList) == 0) {
					echo "<tr><td colspan='7'>You have not submitted any complaint.</td></tr>";
				}
				?>
			</table>
		</div>
	</div>
</body>
</html>
