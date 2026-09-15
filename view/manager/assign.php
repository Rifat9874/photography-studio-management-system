<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'manager') {
	header("Location: ../login.php");
	exit();
}

require_once __DIR__ . '/../../model/Booking.php';

$id = $_GET['id'];

$booking = new Booking();
$row = $booking->getBookingById($id);

if ($row == null) {
	$_SESSION['error'] = "Booking not found";
	header("Location: bookings.php");
	exit();
}

$base = "../../";
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Assign Photographer</title>
	<link rel="stylesheet" href="../../css/style.css">
	<link rel="stylesheet" href="../../css/manager.css">
	<script src="../../js/script.js"></script>
	<script src="../../js/manager.js"></script>
</head>
<body>
	<?php require_once __DIR__ . '/../nav.php'; ?>

	<div class="container">
		<h1>Assign Photographer - Booking #<?php echo $row['id'] ?></h1>
		<?php require_once __DIR__ . '/../message.php'; ?>

		<div class="box">
			<h2>Booking Details</h2>
			<table>
				<tr><th>Customer</th><td><?php echo $row['customer_name'] ?></td></tr>
				<tr><th>Category</th><td><?php echo $row['category_name'] ?></td></tr>
				<tr><th>Package</th><td><?php echo $row['package_name'] ?> (Tk <?php echo $row['price'] ?>)</td></tr>
				<tr><th>Date</th><td><?php echo $row['booking_date'] ?></td></tr>
				<tr><th>Time</th><td><?php echo $row['booking_time'] ?></td></tr>
				<tr><th>Location</th><td><?php echo $row['location'] ?></td></tr>
				<tr><th>Current Photographer</th><td><?php echo $row['photographer_name'] == null ? 'Not assigned' : $row['photographer_name'] ?></td></tr>
			</table>
		</div>

		<div class="box">
			<h2>Available Photographers on <?php echo $row['booking_date'] ?></h2>
			<!-- this list is loaded by AJAX when the page opens -->
			<p id="photographer_info"></p>

			<form action="../../controller/assign-handler.php" method="post">
				<input type="hidden" name="action" value="assign">
				<input type="hidden" name="id" value="<?php echo $row['id'] ?>">

				<label for="photographer_id">Choose a photographer</label>
				<select id="photographer_id" name="photographer_id"></select>

				<input type="submit" value="Assign">
			</form>
			<p><a href="bookings.php">Back to bookings</a></p>
		</div>
	</div>

	<script>
		// call the AJAX function as soon as the page is loaded
		loadPhotographers("<?php echo $row['booking_date'] ?>");
	</script>
</body>
</html>
