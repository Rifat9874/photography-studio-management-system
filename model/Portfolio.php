<?php
require_once __DIR__ . '/../db/db.php';

class Portfolio {
	public function establishConnection() {
		$db = new DBConnection();
		$conn = $db->connect();
		return $conn;
	}

	// CREATE
	public function insertItem($photographerId, $categoryId, $title, $description, $image) {
		$sql = "INSERT INTO portfolio (photographer_id, category_id, title, description, image)
				VALUES (?, ?, ?, ?, ?)";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('iisss', $photographerId, $categoryId, $title, $description, $image);
		return $stmt->execute();
	}

	// READ - items of one photographer
	public function getItemsByPhotographer($photographerId) {
		$sql = "SELECT pf.*, c.name AS category_name
				FROM portfolio pf
				JOIN categories c ON pf.category_id = c.id
				WHERE pf.photographer_id = ?
				ORDER BY pf.id DESC";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('i', $photographerId);
		$stmt->execute();
		$result = $stmt->get_result();

		$itemList = [];
		while ($row = $result->fetch_assoc()) {
			array_push($itemList, $row);
		}
		return $itemList;
	}

	// READ - everything (customers browse this)
	public function getAllItems() {
		$sql = "SELECT pf.*, c.name AS category_name, u.name AS photographer_name, u.specialization
				FROM portfolio pf
				JOIN categories c ON pf.category_id = c.id
				JOIN users u ON pf.photographer_id = u.id
				ORDER BY pf.id DESC";
		$conn = $this->establishConnection();
		$result = $conn->query($sql);

		$itemList = [];
		while ($row = $result->fetch_assoc()) {
			array_push($itemList, $row);
		}
		return $itemList;
	}

	public function getItemById($id) {
		$sql = "SELECT * FROM portfolio WHERE id = ?";
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
	public function updateItem($id, $categoryId, $title, $description) {
		$sql = "UPDATE portfolio SET category_id = ?, title = ?, description = ? WHERE id = ?";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('issi', $categoryId, $title, $description, $id);
		return $stmt->execute();
	}

	public function updateImage($id, $image) {
		$sql = "UPDATE portfolio SET image = ? WHERE id = ?";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('si', $image, $id);
		return $stmt->execute();
	}

	// DELETE
	public function deleteItem($id) {
		$sql = "DELETE FROM portfolio WHERE id = ?";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('i', $id);
		return $stmt->execute();
	}
}
