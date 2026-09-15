<?php
// AJAX + JSON endpoint 4 - dashboard statistics for the logged-in role
require_once __DIR__ . '/../model/Booking.php';
require_once __DIR__ . '/../model/Complaint.php';
require_once __DIR__ . '/../model/Package.php';
require_once __DIR__ . '/../model/Category.php';
require_once __DIR__ . '/../model/User.php';
require_once __DIR__ . '/../model/Portfolio.php';
require_once __DIR__ . '/../model/Availability.php';

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
	echo json_encode(array('success' => false, 'message' => 'Please login first'));
	exit();
}

$role = $_SESSION['role'];
$userId = $_SESSION['user_id'];
$stats = array();

if ($role == 'customer') {
	$booking = new Booking();
	$bookingList = $booking->getBookingsByCustomer($userId);

	$complaint = new Complaint();
	$complaintList = $complaint->getComplaintsByCustomer($userId);

	$pendingCount = 0;
	$completedCount = 0;
	for ($i = 0; $i < count($bookingList); $i++) {
		if ($bookingList[$i]['status'] == 'pending') {
			$pendingCount = $pendingCount + 1;
		}
		if ($bookingList[$i]['status'] == 'completed') {
			$completedCount = $completedCount + 1;
		}
	}

	$stats = array(
		'total_bookings' => count($bookingList),
		'pending_bookings' => $pendingCount,
		'completed_bookings' => $completedCount,
		'my_complaints' => count($complaintList)
	);
} else if ($role == 'photographer') {
	$booking = new Booking();
	$shootList = $booking->getBookingsByPhotographer($userId);

	$portfolio = new Portfolio();
	$itemList = $portfolio->getItemsByPhotographer($userId);

	$availability = new Availability();
	$slotList = $availability->getSlotsByPhotographer($userId);

	$upcomingCount = 0;
	for ($i = 0; $i < count($shootList); $i++) {
		if ($shootList[$i]['booking_date'] >= date('Y-m-d') && $shootList[$i]['status'] != 'completed') {
			$upcomingCount = $upcomingCount + 1;
		}
	}

	$stats = array(
		'total_shoots' => count($shootList),
		'upcoming_shoots' => $upcomingCount,
		'portfolio_items' => count($itemList),
		'free_slots' => count($slotList)
	);
} else if ($role == 'manager') {
	$booking = new Booking();
	$bookingList = $booking->getAllBookings();

	$package = new Package();
	$packageList = $package->getAllPackages();

	$stats = array(
		'all_bookings' => count($bookingList),
		'pending_requests' => $booking->countByStatus('pending'),
		'approved_bookings' => $booking->countByStatus('approved'),
		'packages' => count($packageList)
	);
} else if ($role == 'admin') {
	$user = new User();
	$userList = $user->getAllUsers();

	$category = new Category();
	$categoryList = $category->getAllCategories();

	$booking = new Booking();
	$bookingList = $booking->getAllBookings();

	$complaint = new Complaint();

	$stats = array(
		'total_users' => count($userList),
		'categories' => count($categoryList),
		'bookings' => count($bookingList),
		'open_complaints' => $complaint->countByStatus('open')
	);
} else {
	echo json_encode(array('success' => false, 'message' => 'Unknown role'));
	exit();
}

echo json_encode(array(
	'success' => true,
	'role' => $role,
	'stats' => $stats,
	'message' => 'Dashboard stats refreshed using AJAX + JSON'
));
