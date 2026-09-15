<?php
require_once __DIR__ . '/../model/User.php';
session_start();

// user management belongs to the admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
	header("Location: ../view/login.php");
	exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$action = $_POST['action'];
	$id = $_POST['id'];
	$user = new User();

	// admin should not lock or delete his own account
	if ($id == $_SESSION['user_id']) {
		$_SESSION['error'] = "You cannot change your own admin account from here";
		header("Location: ../view/admin/users.php");
		exit();
	}

	if ($action == 'activate') {
		$user->updateStatus($id, 'active');
		$_SESSION['message'] = "Account activated";
	}

	if ($action == 'deactivate') {
		$user->updateStatus($id, 'inactive');
		$_SESSION['message'] = "Account deactivated";
	}

	if ($action == 'delete') {
		$user->deleteUser($id);
		$_SESSION['message'] = "Account removed";
	}
}
header("Location: ../view/admin/users.php");
