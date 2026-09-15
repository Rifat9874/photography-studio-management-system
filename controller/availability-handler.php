<?php
require_once __DIR__ . '/../model/Availability.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'photographer') {
	header("Location: ../view/login.php");
	exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$action = $_POST['action'];
	$availability = new Availability();
	$photographerId = $_SESSION['user_id'];

	// CREATE
	if ($action == 'create') {
		$availableDate = $_POST['available_date'];
		$timeSlot = $_POST['time_slot'];

		if (empty($availableDate)) {
			$_SESSION['error'] = "Please choose a date";
		} else if ($availableDate < date('Y-m-d')) {
			$_SESSION['error'] = "You cannot add a past date";
		} else if ($availability->slotExists($photographerId, $availableDate, $timeSlot)) {
			$_SESSION['error'] = "This date and slot is already in your list";
		} else {
			$availability->insertSlot($photographerId, $availableDate, $timeSlot);
			$_SESSION['message'] = "Availability added";
		}
	}

	// UPDATE
	if ($action == 'update') {
		$id = $_POST['id'];
		$availableDate = $_POST['available_date'];
		$timeSlot = $_POST['time_slot'];

		$row = $availability->getSlotById($id);
		if ($row['photographer_id'] != $photographerId) {
			$_SESSION['error'] = "This slot does not belong to you";
		} else {
			$availability->updateSlot($id, $availableDate, $timeSlot);
			$_SESSION['message'] = "Availability updated";
		}
	}

	// DELETE
	if ($action == 'delete') {
		$id = $_POST['id'];
		$row = $availability->getSlotById($id);
		if ($row['photographer_id'] == $photographerId) {
			$availability->deleteSlot($id);
			$_SESSION['message'] = "Availability removed";
		}
	}
}
header("Location: ../view/photographer/availability.php");
