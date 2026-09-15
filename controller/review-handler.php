<?php
require_once __DIR__ . '/../model/Review.php';
require_once __DIR__ . '/../model/Booking.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
	header("Location: ../view/login.php");
	exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$action = $_POST['action'];
	$review = new Review();
	$customerId = $_SESSION['user_id'];

	// CREATE
	if ($action == 'create') {
		$bookingId = $_POST['booking_id'];
		$rating = $_POST['rating'];
		$comment = trim($_POST['comment']);

		$booking = new Booking();
		$row = $booking->getBookingById($bookingId);

		if ($row['customer_id'] != $customerId) {
			$_SESSION['error'] = "You cannot review this booking";
		} else if ($row['status'] != 'completed') {
			$_SESSION['error'] = "Only completed shoots can be reviewed";
		} else if ($review->getReviewByBooking($bookingId) != null) {
			$_SESSION['error'] = "You already reviewed this shoot";
		} else if ($rating < 1 || $rating > 5) {
			$_SESSION['error'] = "Rating must be between 1 and 5";
		} else {
			$review->insertReview($bookingId, $customerId, $rating, $comment);
			$_SESSION['message'] = "Thank you for your review";
		}
	}

	// UPDATE
	if ($action == 'update') {
		$id = $_POST['id'];
		$rating = $_POST['rating'];
		$comment = trim($_POST['comment']);
		if ($rating < 1 || $rating > 5) {
			$_SESSION['error'] = "Rating must be between 1 and 5";
		} else {
			$review->updateReview($id, $rating, $comment);
			$_SESSION['message'] = "Review updated";
		}
	}

	// DELETE
	if ($action == 'delete') {
		$id = $_POST['id'];
		$review->deleteReview($id);
		$_SESSION['message'] = "Review deleted";
	}
}
header("Location: ../view/customer/reviews.php");
