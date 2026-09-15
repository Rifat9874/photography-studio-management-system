<?php
require_once __DIR__ . '/../model/Package.php';
session_start();

// package management belongs to the booking manager
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'manager') {
	header("Location: ../view/login.php");
	exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$action = $_POST['action'];
	$package = new Package();

	// CREATE
	if ($action == 'create') {
		$categoryId = $_POST['category_id'];
		$name = trim($_POST['name']);
		$price = $_POST['price'];
		$duration = trim($_POST['duration']);
		$inclusions = trim($_POST['inclusions']);

		if (empty($name)) {
			$_SESSION['error'] = "Package name is required";
		} else if (!is_numeric($price) || $price <= 0) {
			$_SESSION['error'] = "Price must be a positive number";
		} else {
			$package->insertPackage($categoryId, $name, $price, $duration, $inclusions);
			$_SESSION['message'] = "Package created";
		}
	}

	// UPDATE
	if ($action == 'update') {
		$id = $_POST['id'];
		$categoryId = $_POST['category_id'];
		$name = trim($_POST['name']);
		$price = $_POST['price'];
		$duration = trim($_POST['duration']);
		$inclusions = trim($_POST['inclusions']);

		if (empty($name) || !is_numeric($price)) {
			$_SESSION['error'] = "Name and a numeric price are required";
		} else {
			$package->updatePackage($id, $categoryId, $name, $price, $duration, $inclusions);
			$_SESSION['message'] = "Package updated";
		}
	}

	// DELETE
	if ($action == 'delete') {
		$id = $_POST['id'];
		$package->deletePackage($id);
		$_SESSION['message'] = "Package deleted";
	}
}
header("Location: ../view/manager/packages.php");
