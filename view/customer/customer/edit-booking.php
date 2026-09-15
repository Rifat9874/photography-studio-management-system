<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
	header("Location: ../login.php");
	exit();
}

require_once __DIR__ . '/../../model/Booking.php';
require_once __DIR__ . '/../../model/Package.php';

$id = $_GET['id'];

$booking = new Booking();
$row = $booking->getBookingById($id);

// a customer may only edit his own pending booking
if ($row == null || $row['customer_id'] != $_SESSION['user_id'] || $row['status'] != 'pending') {
	$_SESSION['error'] = "This booking cannot be edited";
	header("Location: my-bookings.php");
	exit();
}

$package = new Package();
$packageList = $package->getAllPackages();

$base = "../../";
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Edit Booking</title>
	<link rel="stylesheet" href="../../css/style.css">
	<link rel="stylesheet" href="../../css/customer.css">
</head>
<body>
	<?php require_once __DIR__ . '/../nav.php'; ?>

	<div class="container">
		<h1>Edit Booking #<?php echo $row['id'] ?></h1>

		<div class="box">
			<form action="../../controller/booking-handler.php" method="post">
				<input type="hidden" name="action" value="update">
				<input type="hidden" name="id" value="<?php echo $row['id'] ?>">

				<label for="package_id">Package</label>
				<select id="package_id" name="package_id">
					<?php
					for ($i = 0; $i < count($packageList); $i++) {
						$p = $packageList[$i];
						$selected = "";
						if ($p['id'] == $row['package_id']) {
							$selected = "selected";
						}
						echo "<option value='" . $p['id'] . "' " . $selected . ">" .
							 $p['category_name'] . " - " . $p['name'] . " (Tk " . $p['price'] . ")</option>";
					}
					?>
				</select>

				<label for="booking_date">Shoot Date</label>
				<input type="date" id="booking_date" name="booking_date" value="<?php echo $row['booking_date'] ?>" required>

				<label for="booking_time">Time Slot</label>
				<select id="booking_time" name="booking_time">
					<option value="Morning" <?php if ($row['booking_time'] == 'Morning') echo 'selected' ?>>Morning</option>
					<option value="Afternoon" <?php if ($row['booking_time'] == 'Afternoon') echo 'selected' ?>>Afternoon</option>
					<option value="Evening" <?php if ($row['booking_time'] == 'Evening') echo 'selected' ?>>Evening</option>
				</select>

				<label for="location">Location</label>
				<input type="text" id="location" name="location" value="<?php echo $row['location'] ?>" required>

				<label for="note">Note</label>
				<textarea id="note" name="note"><?php echo $row['note'] ?></textarea>

				<input type="submit" value="Update Booking">
			</form>
			<p><a href="my-bookings.php">Back to my bookings</a></p>
		</div>
	</div>
</body>
</html>
