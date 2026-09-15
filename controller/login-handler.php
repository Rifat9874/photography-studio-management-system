<?php
require_once __DIR__ . '/../model/User.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	// login_id can be a username or an email address
	$loginId = isset($_POST['login_id']) ? trim($_POST['login_id']) : '';
	if ($loginId == '' && isset($_POST['username'])) {
		$loginId = trim($_POST['username']);
	}
	$password = isset($_POST['password']) ? $_POST['password'] : '';

	if (empty($loginId) || empty($password)) {
		$_SESSION['login_error'] = "Username or email and password are required";
		header("Location: ../view/login.php");
		exit();
	}

	$user = new User();
	$row = $user->loginCheck($loginId, $password);

	if ($row == null) {
		$_SESSION['login_error'] = "Username/email or Password is invalid";
		header("Location: ../view/login.php");
		exit();
	}

	if ($row['status'] != 'active') {
		$_SESSION['login_error'] = "Your account is deactivated. Please contact the admin.";
		header("Location: ../view/login.php");
		exit();
	}

	// login successful - save the user in the session
	unset($_SESSION['login_error']);
	$_SESSION['user_id'] = $row['id'];
	$_SESSION['username'] = $row['username'];
	$_SESSION['name'] = $row['name'];
	$_SESSION['role'] = $row['role'];

	// simple cookie: remembers only the last typed username/email for the next login page
	setcookie('last_login_id', $loginId, time() + (86400 * 7), '/');

	// every role lands on its own dashboard
	if ($row['role'] == 'admin') {
		header("Location: ../view/admin/dashboard.php");
	} else if ($row['role'] == 'manager') {
		header("Location: ../view/manager/dashboard.php");
	} else if ($row['role'] == 'photographer') {
		header("Location: ../view/photographer/dashboard.php");
	} else {
		header("Location: ../view/customer/dashboard.php");
	}
	exit();
} else {
	header("Location: ../view/login.php");
}
