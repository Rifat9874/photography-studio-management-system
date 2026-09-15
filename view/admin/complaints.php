<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
	header("Location: ../login.php");
	exit();
}

require_once __DIR__ . '/../../model/Complaint.php';

$complaint = new Complaint();
$complaintList = $complaint->getAllComplaints();

$base = "../../";
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Complaint Management</title>
	<link rel="stylesheet" href="../../css/style.css">
	<link rel="stylesheet" href="../../css/admin.css">
	<script src="../../js/script.js"></script>
	<script src="../../js/admin.js"></script>
</head>
<body>
	<?php require_once __DIR__ . '/../nav.php'; ?>

	<div class="container">
		<h1>Complaint Management</h1>
		<?php require_once __DIR__ . '/../message.php'; ?>

		<div class="box">
			<table>
				<tr>
					<th>ID</th><th>Customer</th><th>Email</th><th>Booking</th><th>Subject</th>
					<th>Message</th><th>Date</th><th>Status &amp; Reply</th>
				</tr>
				<?php
				for ($i = 0; $i < count($complaintList); $i++) {
					$row = $complaintList[$i];
					$bookingText = $row['booking_id'];
					if ($bookingText == null) {
						$bookingText = "-";
					} else {
						$bookingText = "#" . $bookingText;
					}
					$statusClass = str_replace(' ', '-', $row['status']);

					echo "<tr>";
					echo "<td>" . $row['id'] . "</td>";
					echo "<td>" . $row['customer_name'] . "</td>";
					echo "<td>" . $row['customer_email'] . "</td>";
					echo "<td>" . $bookingText . "</td>";
					echo "<td>" . $row['subject'] . "</td>";
					echo "<td>" . $row['message'] . "</td>";
					echo "<td>" . $row['created_at'] . "</td>";

					// update form inside the row
					$openSelected = $row['status'] == 'open' ? 'selected' : '';
					$progressSelected = $row['status'] == 'in progress' ? 'selected' : '';
					$resolvedSelected = $row['status'] == 'resolved' ? 'selected' : '';
					$closedSelected = $row['status'] == 'closed' ? 'selected' : '';

					echo "<td>
						<span class='status status-" . $statusClass . "'>" . $row['status'] . "</span>
						<form action='../../controller/complaint-handler.php' method='post'>
							<input type='hidden' name='action' value='update'>
							<input type='hidden' name='id' value='" . $row['id'] . "'>
							<select name='status'>
								<option value='open' " . $openSelected . ">open</option>
								<option value='in progress' " . $progressSelected . ">in progress</option>
								<option value='resolved' " . $resolvedSelected . ">resolved</option>
								<option value='closed' " . $closedSelected . ">closed</option>
							</select>
							<textarea name='admin_reply' placeholder='Write the reply for the customer'>" . $row['admin_reply'] . "</textarea>
							<input type='submit' class='small-btn' value='Save'>
						</form>
						<form action='../../controller/complaint-handler.php' method='post' onsubmit='return confirmDelete()'>
							<input type='hidden' name='action' value='delete'>
							<input type='hidden' name='id' value='" . $row['id'] . "'>
							<input type='submit' class='small-btn danger' value='Delete'>
						</form>
					</td>";
					echo "</tr>";
				}
				if (count($complaintList) == 0) {
					echo "<tr><td colspan='8'>No complaint submitted yet.</td></tr>";
				}
				?>
			</table>
		</div>
	</div>
</body>
</html>
