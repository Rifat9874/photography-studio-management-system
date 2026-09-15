<?php
require_once __DIR__ . '/../db/db.php';

class Package {
	public function establishConnection() {
		$db = new DBConnection();
		$conn = $db->connect();
		return $conn;
	}

	// CREATE
	public function insertPackage($categoryId, $name, $price, $duration, $inclusions) {
		$sql = "INSERT INTO packages (category_id, name, price, duration, inclusions)
				VALUES (?, ?, ?, ?, ?)";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('isdss', $categoryId, $name, $price, $duration, $inclusions);
		return $stmt->execute();
	}

	// READ - all packages with their category name (JOIN)
	public function getAllPackages() {
		$sql = "SELECT p.*, c.name AS category_name
				FROM packages p
				JOIN categories c ON p.category_id = c.id
				ORDER BY c.name, p.price";
		$conn = $this->establishConnection();
		$result = $conn->query($sql);

		$packageList = [];
		while ($row = $result->fetch_assoc()) {
			array_push($packageList, $row);
		}
		return $packageList;
	}

	// used by AJAX when the customer picks a category
	public function getPackagesByCategory($categoryId) {
		$sql = "SELECT * FROM packages WHERE category_id = ? ORDER BY price";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('i', $categoryId);
		$stmt->execute();
		$result = $stmt->get_result();

		$packageList = [];
		while ($row = $result->fetch_assoc()) {
			array_push($packageList, $row);
		}
		return $packageList;
	}

	public function getPackageById($id) {
		$sql = "SELECT p.*, c.name AS category_name
				FROM packages p JOIN categories c ON p.category_id = c.id
				WHERE p.id = ?";
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
	public function updatePackage($id, $categoryId, $name, $price, $duration, $inclusions) {
		$sql = "UPDATE packages SET category_id = ?, name = ?, price = ?, duration = ?, inclusions = ?
				WHERE id = ?";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('isdssi', $categoryId, $name, $price, $duration, $inclusions, $id);
		return $stmt->execute();
	}

	// DELETE
	public function deletePackage($id) {
		$sql = "DELETE FROM packages WHERE id = ?";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('i', $id);
		return $stmt->execute();
	}
}
