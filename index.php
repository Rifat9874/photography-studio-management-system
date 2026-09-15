<?php
// Entry point of the project.
// It only decides where the visitor should go.
session_start();

if (!isset($_SESSION['user_id'])) {
	header("Location: view/login.php");
	exit();
}

if ($_SESSION['role'] == 'admin') {
	header("Location: view/admin/dashboard.php");
} else if ($_SESSION['role'] == 'manager') {
	header("Location: view/manager/dashboard.php");
} else if ($_SESSION['role'] == 'photographer') {
	header("Location: view/photographer/dashboard.php");
} else {
	header("Location: view/customer/dashboard.php");
}
exit();
