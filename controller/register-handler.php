<?php
require_once __DIR__ . '/../model/User.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$name = trim($_POST['name']);
	$username = trim($_POST['username']);
	$email = trim($_POST['email']);
	$password = $_POST['password'];
	$confirmPassword = $_POST['confirm_password'];
	$phone = trim($_POST['phone']);
	$role = $_POST['role'];
	$specialization = isset($_POST['specialization']) ? $_POST['specialization'] : '';

	// server side validation
	$errors = [];
	if (empty($name) || strlen($name) < 3) {
		$errors[] = "Name must be at least 3 characters";
	}
	if (empty($username) || strlen($username) < 3) {
		$errors[] = "Username must be at least 3 characters";
	}
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$errors[] = "Email pattern is invalid";
	}
	if (strlen($password) < 6) {
		$errors[] = "Password must be at least 6 characters";
	}
	if ($password != $confirmPassword) {
		$errors[] = "Password and Confirm Password do not match";
	}
	if (!is_numeric($phone) || strlen($phone) < 11) {
		$errors[] = "Phone number must be 11 digits";
	}
	if ($role != 'customer' && $role != 'photographer') {
		$errors[] = "Invalid role selected";
	}

	$user = new User();
	if ($user->isUsernameTaken($username)) {
		$errors[] = "This username is already taken";
	}
	if ($user->isEmailTaken($email)) {
		$errors[] = "This email is already registered";
	}

	if (count($errors) > 0) {
		$_SESSION['register_error'] = $errors;
		header("Location: ../view/register.php");
		exit();
	}

	$result = $user->insertUser($name, $username, $email, $password, $phone, $role, $specialization);
	if (is_int($result)) {
		unset($_SESSION['register_error']);
		$_SESSION['message'] = "Account created successfully. Please login.";
		header("Location: ../view/login.php");
	} else {
		$_SESSION['register_error'] = ["Unable to create account: " . $result];
		header("Location: ../view/register.php");
	}
	exit();
} else {
	header("Location: ../view/register.php");
}
