<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
	header("Location: ../login.php");
	exit();
}

require_once __DIR__ . '/../../model/Category.php';

$category = new Category();
$categoryList = $category->getAllCategories();

$base = "../../";
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>New Booking</title>
	<link rel="stylesheet" href="../../css/style.css">
	<link rel="stylesheet" href="../../css/customer.css">
	<script src="../../js/script.js"></script>
	<script src="../../js/customer.js"></script>
</head>
<body>
	<?php require_once __DIR__ . '/../nav.php'; ?>

	<div class="container">
		<h1>New Booking Request</h1>
		<?php require_once __DIR__ . '/../message.php'; ?>

		<div class="box">
			<form action="../../controller/booking-handler.php" method="post" onsubmit="return validateBooking()">
				<input type="hidden" name="action" value="create">

				<label for="category_id">Photography Category</label>
				<select id="category_id" name="category_id" onchange="loadPackages()">
					<option value="">-- select a category --</option>
					<?php
					for ($i = 0; $i < count($categoryList); $i++) {
						echo "<option value='" . $categoryList[$i]['id'] . "'>" . $categoryList[$i]['name'] . "</option>";
					}
					?>
				</select>

				<label for="package_id">Package</label>
				<select id="package_id" name="package_id"></select>

				<!-- the package comparison table is filled by AJAX -->
				<div id="package_details"></div>

				<label for="booking_date">Shoot Date</label>
				<input type="date" id="booking_date" name="booking_date" required>

				<label for="booking_time">Time Slot</label>
				<select id="booking_time" name="booking_time">
					<option value="Morning">Morning</option>
					<option value="Afternoon">Afternoon</option>
					<option value="Evening">Evening</option>
				</select>

				<label for="location">Location</label>
				<input type="text" id="location" name="location" required>
				<span class="error" id="location_error"></span>

				<label for="note">Note (optional)</label>
				<textarea id="note" name="note"></textarea>

				<input type="submit" value="Send Booking Request">
			</form>
		</div>
	</div>

	<script>
		// small validation only for this page
		function validateBooking() {
			var packageId = document.getElementById("package_id").value;
			var location = document.getElementById("location").value;

			if (packageId == "" || packageId == null) {
				alert("Please select a category and a package first");
				return false;
			}
			if (location.length < 5) {
				document.getElementById("location_error").innerText = "Please write the full location";
				return false;
			}
			return true;
		}
	</script>
</body>
</html>
