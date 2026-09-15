<?php
require_once __DIR__ . '/../model/User.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$action = $_POST['action'];
	$user = new User();

	// logged in user changes his own password
	if ($action == 'change') {
		$userId = $_SESSION['user_id'];
		$oldPassword = $_POST['old_password'];
		$newPassword = $_POST['new_password'];
		$confirmPassword = $_POST['confirm_password'];

		$row = $user->getUserById($userId);
		if ($row['password'] != $oldPassword) {
			$_SESSION['error'] = "Old password is wrong";
		} else if (strlen($newPassword) < 6) {
			$_SESSION['error'] = "New password must be at least 6 characters";
		} else if ($newPassword != $confirmPassword) {
			$_SESSION['error'] = "New password and confirm password do not match";
		} else {
			$user->updatePassword($userId, $newPassword);
			$_SESSION['message'] = "Password changed successfully";
		}
		header("Location: ../view/change-password.php");
		exit();
	}

	// user forgot the password - username + email must match
	if ($action == 'reset') {
		$username = trim($_POST['username']);
		$email = trim($_POST['email']);
		$newPassword = $_POST['new_password'];
		$confirmPassword = $_POST['confirm_password'];

		$row = $user->getUserByUsernameAndEmail($username, $email);
		if ($row == null) {
			$_SESSION['error'] = "No account found with this username and email";
		} else if (strlen($newPassword) < 6) {
			$_SESSION['error'] = "New password must be at least 6 characters";
		} else if ($newPassword != $confirmPassword) {
			$_SESSION['error'] = "New password and confirm password do not match";
		} else {
			$user->updatePassword($row['id'], $newPassword);
			$_SESSION['message'] = "Password reset successfully. Please login.";
			header("Location: ../view/login.php");
			exit();
		}
		header("Location: ../view/reset-password.php");
		exit();
	}
}
header("Location: ../index.php");
