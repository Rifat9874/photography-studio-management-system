<?php
require_once __DIR__ . '/../db/db.php';

class Category {
	public function establishConnection() {
		$db = new DBConnection();
		$conn = $db->connect();
		return $conn;
	}

	// CREATE
	public function insertCategory($name, $description) {
		$sql = "INSERT INTO categories (name, description) VALUES (?, ?)";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('ss', $name, $description);
		return $stmt->execute();
	}

	// READ
	public function getAllCategories() {
		$sql = "SELECT * FROM categories ORDER BY name";
		$conn = $this->establishConnection();
		$result = $conn->query($sql);

		$categoryList = [];
		while ($row = $result->fetch_assoc()) {
			array_push($categoryList, $row);
		}
		return $categoryList;
	}

	public function getCategoryById($id) {
		$sql = "SELECT * FROM categories WHERE id = ?";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('i', $id);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result->num_rows > 0) {
			return $result->fetch_assoc();
		} else {
			return null;
		}
	}

	// UPDATE
	public function updateCategory($id, $name, $description) {
		$sql = "UPDATE categories SET name = ?, description = ? WHERE id = ?";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('ssi', $name, $description, $id);
		return $stmt->execute();
	}

	// DELETE
	public function deleteCategory($id) {
		$sql = "DELETE FROM categories WHERE id = ?";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('i', $id);
		return $stmt->execute();
	}
}
