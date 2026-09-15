<?php
require_once __DIR__ . '/../model/Complaint.php';
session_start();

if (!isset($_SESSION['user_id'])) {
	header("Location: ../view/login.php");
	exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$action = $_POST['action'];
	$complaint = new Complaint();

	// CREATE - customer files a complaint
	if ($action == 'create') {
		$customerId = $_SESSION['user_id'];
		$bookingId = $_POST['booking_id'];
		$subject = trim($_POST['subject']);
		$message = trim($_POST['message']);

		if (empty($subject) || empty($message)) {
			$_SESSION['error'] = "Subject and message are required";
		} else {
			if (empty($bookingId)) {
				$bookingId = null;
			}
			$complaint->insertComplaint($customerId, $bookingId, $subject, $message);
			$_SESSION['message'] = "Complaint submitted";
		}
		header("Location: ../view/customer/complaints.php");
		exit();
	}

	// UPDATE - admin replies and changes the status
	if ($action == 'update') {
		if ($_SESSION['role'] != 'admin') {
			header("Location: ../view/login.php");
			exit();
		}
		$id = $_POST['id'];
		$status = $_POST['status'];
		$adminReply = trim($_POST['admin_reply']);
		$complaint->updateComplaint($id, $status, $adminReply);
		$_SESSION['message'] = "Complaint #" . $id . " updated";
		header("Location: ../view/admin/complaints.php");
		exit();
	}

	// DELETE - admin removes a complaint
	if ($action == 'delete') {
		if ($_SESSION['role'] != 'admin') {
			header("Location: ../view/login.php");
			exit();
		}
		$id = $_POST['id'];
		$complaint->deleteComplaint($id);
		$_SESSION['message'] = "Complaint deleted";
		header("Location: ../view/admin/complaints.php");
		exit();
	}
}
header("Location: ../index.php");
