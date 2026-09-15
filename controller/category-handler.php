<?php
require_once __DIR__ . '/../model/Category.php';
session_start();

// category management belongs to the admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
	header("Location: ../view/login.php");
	exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$action = $_POST['action'];
	$category = new Category();

	// CREATE
	if ($action == 'create') {
		$name = trim($_POST['name']);
		$description = trim($_POST['description']);
		if (empty($name)) {
			$_SESSION['error'] = "Category name is required";
		} else {
			$category->insertCategory($name, $description);
			$_SESSION['message'] = "Category created";
		}
	}

	// UPDATE
	if ($action == 'update') {
		$id = $_POST['id'];
		$name = trim($_POST['name']);
		$description = trim($_POST['description']);
		if (empty($name)) {
			$_SESSION['error'] = "Category name is required";
		} else {
			$category->updateCategory($id, $name, $description);
			$_SESSION['message'] = "Category updated";
		}
	}

	// DELETE
	if ($action == 'delete') {
		$id = $_POST['id'];
		$category->deleteCategory($id);
		$_SESSION['message'] = "Category deleted (its packages were removed too)";
	}
}
header("Location: ../view/admin/categories.php");
