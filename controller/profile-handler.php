<?php
require_once __DIR__ . '/../model/User.php';
session_start();

if (!isset($_SESSION['user_id'])) {
	header("Location: ../view/login.php");
	exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$userId = $_SESSION['user_id'];
	$action = $_POST['action'];
	$user = new User();

	// UPDATE profile information
	if ($action == 'update') {
		$name = trim($_POST['name']);
		$email = trim($_POST['email']);
		$phone = trim($_POST['phone']);
		$specialization = isset($_POST['specialization']) ? $_POST['specialization'] : '';

		if (empty($name) || strlen($name) < 3) {
			$_SESSION['error'] = "Name must be at least 3 characters";
		} else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$_SESSION['error'] = "Email pattern is invalid";
		} else {
			$user->updateProfile($userId, $name, $email, $phone, $specialization);
			$_SESSION['name'] = $name;
			$_SESSION['message'] = "Profile updated successfully";
		}
	}

	// UPLOAD profile picture (same way as the lab class)
	if ($action == 'upload') {
		$tmpLocation = $_FILES['profile_pic']['tmp_name'];
		$fileName = $_SESSION['username'] . '_' . $_FILES['profile_pic']['name'];
		$targetFolder = __DIR__ . '/../images/';
		$targetFilePath = $targetFolder . $fileName;

		if (move_uploaded_file($tmpLocation, $targetFilePath)) {
			$user->updateProfilePic($userId, $fileName);
			$_SESSION['message'] = "Profile picture uploaded";
		} else {
			$_SESSION['error'] = "Image upload failed";
		}
	}

	// DELETE own account
	if ($action == 'delete') {
		$user->deleteUser($userId);
		session_unset();
		session_destroy();
		header("Location: ../view/login.php");
		exit();
	}

	header("Location: ../view/profile.php");
	exit();
}
header("Location: ../view/profile.php");
