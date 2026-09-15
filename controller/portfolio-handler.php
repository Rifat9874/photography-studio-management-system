<?php
require_once __DIR__ . '/../model/Portfolio.php';
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'photographer') {
	header("Location: ../view/login.php");
	exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
	$action = $_POST['action'];
	$portfolio = new Portfolio();
	$photographerId = $_SESSION['user_id'];

	// CREATE - upload a new sample work
	if ($action == 'create') {
		$categoryId = $_POST['category_id'];
		$title = trim($_POST['title']);
		$description = trim($_POST['description']);
		$imageName = '';

		if (empty($title)) {
			$_SESSION['error'] = "Title is required";
			header("Location: ../view/photographer/portfolio.php");
			exit();
		}

		// picture upload is optional
		if (isset($_FILES['image']) && $_FILES['image']['name'] != '') {
			$tmpLocation = $_FILES['image']['tmp_name'];
			$imageName = $_SESSION['username'] . '_' . time() . '_' . $_FILES['image']['name'];
			$targetFilePath = __DIR__ . '/../images/' . $imageName;
			if (!move_uploaded_file($tmpLocation, $targetFilePath)) {
				$imageName = '';
				$_SESSION['error'] = "Image upload failed, item saved without picture";
			}
		}

		$portfolio->insertItem($photographerId, $categoryId, $title, $description, $imageName);
		$_SESSION['message'] = "Portfolio item added";
	}

	// UPDATE
	if ($action == 'update') {
		$id = $_POST['id'];
		$categoryId = $_POST['category_id'];
		$title = trim($_POST['title']);
		$description = trim($_POST['description']);

		$row = $portfolio->getItemById($id);
		if ($row['photographer_id'] != $photographerId) {
			$_SESSION['error'] = "This is not your portfolio item";
		} else {
			$portfolio->updateItem($id, $categoryId, $title, $description);

			// if a new picture was chosen, replace the old one
			if (isset($_FILES['image']) && $_FILES['image']['name'] != '') {
				$tmpLocation = $_FILES['image']['tmp_name'];
				$imageName = $_SESSION['username'] . '_' . time() . '_' . $_FILES['image']['name'];
				$targetFilePath = __DIR__ . '/../images/' . $imageName;
				if (move_uploaded_file($tmpLocation, $targetFilePath)) {
					$portfolio->updateImage($id, $imageName);
				}
			}
			$_SESSION['message'] = "Portfolio item updated";
		}
	}

	// DELETE
	if ($action == 'delete') {
		$id = $_POST['id'];
		$row = $portfolio->getItemById($id);
		if ($row['photographer_id'] == $photographerId) {
			$portfolio->deleteItem($id);
			$_SESSION['message'] = "Portfolio item deleted";
		} else {
			$_SESSION['error'] = "This is not your portfolio item";
		}
	}
}
header("Location: ../view/photographer/portfolio.php");
