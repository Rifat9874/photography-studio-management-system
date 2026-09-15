<?php
require_once __DIR__ . '/../model/Booking.php';
session_start();

if (!isset($_SESSION['user_id'])) {
	header("Location: ../view/login.php");
	exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$action = $_POST['action'];
	$booking = new Booking();

	// CREATE - customer sends a new booking request
	if ($action == 'create') {
		$customerId = $_SESSION['user_id'];
		$packageId = $_POST['package_id'];
		$bookingDate = $_POST['booking_date'];
		$bookingTime = $_POST['booking_time'];
		$location = trim($_POST['location']);
		$note = trim($_POST['note']);

		if (empty($packageId) || empty($bookingDate) || empty($bookingTime) || empty($location)) {
			$_SESSION['error'] = "All fields except note are required";
			header("Location: ../view/customer/new-booking.php");
			exit();
		}
		if ($bookingDate < date('Y-m-d')) {
			$_SESSION['error'] = "Booking date cannot be in the past";
			header("Location: ../view/customer/new-booking.php");
			exit();
		}

		$result = $booking->insertBooking($customerId, $packageId, $bookingDate, $bookingTime, $location, $note);
		if (is_int($result)) {
			$_SESSION['message'] = "Booking request sent. Please wait for manager approval.";
		} else {
			$_SESSION['error'] = "Unable to create booking: " . $result;
		}
		header("Location: ../view/customer/my-bookings.php");
		exit();
	}

	// UPDATE - customer edits a booking that is still pending
	if ($action == 'update') {
		$id = $_POST['id'];
		$packageId = $_POST['package_id'];
		$bookingDate = $_POST['booking_date'];
		$bookingTime = $_POST['booking_time'];
		$location = trim($_POST['location']);
		$note = trim($_POST['note']);

		$row = $booking->getBookingById($id);
		if ($row['customer_id'] != $_SESSION['user_id']) {
			$_SESSION['error'] = "You cannot edit this booking";
		} else if ($row['status'] != 'pending') {
			$_SESSION['error'] = "Only pending bookings can be edited";
		} else if (empty($location) || empty($bookingDate)) {
			$_SESSION['error'] = "Date and location are required";
		} else {
			$booking->updateBooking($id, $packageId, $bookingDate, $bookingTime, $location, $note);
			$_SESSION['message'] = "Booking updated successfully";
		}
		header("Location: ../view/customer/my-bookings.php");
		exit();
	}

	// DELETE - customer removes his own booking
	if ($action == 'delete') {
		$id = $_POST['id'];
		$row = $booking->getBookingById($id);
		if ($row['customer_id'] == $_SESSION['user_id']) {
			$booking->deleteBooking($id);
			$_SESSION['message'] = "Booking deleted";
		} else {
			$_SESSION['error'] = "You cannot delete this booking";
		}
		header("Location: ../view/customer/my-bookings.php");
		exit();
	}

	// UPDATE status - customer cancels
	if ($action == 'cancel') {
		$id = $_POST['id'];
		$row = $booking->getBookingById($id);
		if ($row['customer_id'] == $_SESSION['user_id']) {
			$booking->updateStatus($id, 'cancelled');
			$_SESSION['message'] = "Booking cancelled";
		}
		header("Location: ../view/customer/my-bookings.php");
		exit();
	}

	// UPDATE status - photographer marks the shoot completed / in progress
	if ($action == 'shoot_status') {
		$id = $_POST['id'];
		$status = $_POST['status'];
		$row = $booking->getBookingById($id);
		if ($row['photographer_id'] == $_SESSION['user_id']) {
			$booking->updateStatus($id, $status);
			$_SESSION['message'] = "Shoot status updated to " . $status;
		} else {
			$_SESSION['error'] = "This shoot is not assigned to you";
		}
		header("Location: ../view/photographer/shoots.php");
		exit();
	}
}
header("Location: ../index.php");
