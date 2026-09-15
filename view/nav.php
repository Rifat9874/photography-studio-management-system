<?php
// Shared sidebar navigation.
// Every view sets $base before requiring this file.
?>
<div class="navbar">
	<div class="brand">PHOTO STUDIO</div>

	<div class="nav-links">
		<?php if ($_SESSION['role'] == 'customer') { ?>
			<a href="<?php echo $base ?>view/customer/dashboard.php">Dashboard</a>
			<a href="<?php echo $base ?>view/customer/packages.php">Packages</a>
			<a href="<?php echo $base ?>view/customer/photographers.php">Photographers</a>
			<a href="<?php echo $base ?>view/customer/new-booking.php">New Booking</a>
			<a href="<?php echo $base ?>view/customer/my-bookings.php">My Bookings</a>
			<a href="<?php echo $base ?>view/customer/reviews.php">Reviews</a>
			<a href="<?php echo $base ?>view/customer/complaints.php">Complaints</a>
		<?php } ?>

		<?php if ($_SESSION['role'] == 'photographer') { ?>
			<a href="<?php echo $base ?>view/photographer/dashboard.php">Dashboard</a>
			<a href="<?php echo $base ?>view/photographer/shoots.php">Assigned Shoots</a>
			<a href="<?php echo $base ?>view/photographer/portfolio.php">Portfolio</a>
			<a href="<?php echo $base ?>view/photographer/availability.php">Availability</a>
		<?php } ?>

		<?php if ($_SESSION['role'] == 'manager') { ?>
			<a href="<?php echo $base ?>view/manager/dashboard.php">Dashboard</a>
			<a href="<?php echo $base ?>view/manager/bookings.php">Booking Request</a>
			<a href="<?php echo $base ?>view/manager/packages.php">Packages</a>
		<?php } ?>

		<?php if ($_SESSION['role'] == 'admin') { ?>
			<a href="<?php echo $base ?>view/admin/dashboard.php">Dashboard</a>
			<a href="<?php echo $base ?>view/admin/users.php">Users</a>
			<a href="<?php echo $base ?>view/admin/categories.php">Categories</a>
			<a href="<?php echo $base ?>view/admin/complaints.php">Complaints</a>
		<?php } ?>
	</div>

	<div class="right-side">
		<div class="user-mini">
			<?php echo $_SESSION['name'] ?><br>
			<?php echo ucfirst($_SESSION['role']) ?>
		</div>
		<a href="<?php echo $base ?>view/profile.php">Profile</a>
		<a href="<?php echo $base ?>view/change-password.php">Password</a>
		<form action="<?php echo $base ?>controller/logout-handler.php" method="post">
			<input type="submit" value="Logout">
		</form>
	</div>
</div>
