<?php
require_once __DIR__ . '/../model/Booking.php';
session_start();

// only the booking manager can use this controller
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'manager') {
	header("Location: ../view/login.php");
	exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$action = $_POST['action'];
	$booking = new Booking();

	// approve or reject a request
	if ($action == 'approve') {
		$id = $_POST['id'];
		$booking->updateStatus($id, 'approved');
		$_SESSION['message'] = "Booking #" . $id . " approved";
		header("Location: ../view/manager/bookings.php");
		exit();
	}

	if ($action == 'reject') {
		$id = $_POST['id'];
		$booking->updateStatus($id, 'rejected');
		$_SESSION['message'] = "Booking #" . $id . " rejected";
		header("Location: ../view/manager/bookings.php");
		exit();
	}

	// assign a photographer to an approved booking
	if ($action == 'assign') {
		$id = $_POST['id'];
		$photographerId = $_POST['photographer_id'];

		if (empty($photographerId)) {
			$_SESSION['error'] = "Please select a photographer";
			header("Location: ../view/manager/assign.php?id=" . $id);
			exit();
		}

		$row = $booking->getBookingById($id);
		if ($row['status'] != 'approved' && $row['status'] != 'assigned') {
			$_SESSION['error'] = "Only an approved booking can get a photographer";
			header("Location: ../view/manager/bookings.php");
			exit();
		}

		$booking->assignPhotographer($id, $photographerId);
		$_SESSION['message'] = "Photographer assigned to booking #" . $id;
		header("Location: ../view/manager/bookings.php");
		exit();
	}

	// manager can also delete a booking record
	if ($action == 'delete') {
		$id = $_POST['id'];
		$booking->deleteBooking($id);
		$_SESSION['message'] = "Booking deleted";
		header("Location: ../view/manager/bookings.php");
		exit();
	}
}
header("Location: ../view/manager/bookings.php");
