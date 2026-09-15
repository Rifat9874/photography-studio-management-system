<?php
require_once __DIR__ . '/../db/db.php';

class Availability {
	public function establishConnection() {
		$db = new DBConnection();
		$conn = $db->connect();
		return $conn;
	}

	// CREATE - photographer adds a free date + slot
	public function insertSlot($photographerId, $availableDate, $timeSlot) {
		$sql = "INSERT INTO availability (photographer_id, available_date, time_slot) VALUES (?, ?, ?)";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('iss', $photographerId, $availableDate, $timeSlot);
		return $stmt->execute();
	}

	// READ
	public function getSlotsByPhotographer($photographerId) {
		$sql = "SELECT * FROM availability WHERE photographer_id = ? ORDER BY available_date";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('i', $photographerId);
		$stmt->execute();
		$result = $stmt->get_result();

		$slotList = [];
		while ($row = $result->fetch_assoc()) {
			array_push($slotList, $row);
		}
		return $slotList;
	}

	public function getSlotById($id) {
		$sql = "SELECT * FROM availability WHERE id = ?";
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

	// stops the same date + slot being added twice
	public function slotExists($photographerId, $availableDate, $timeSlot) {
		$sql = "SELECT id FROM availability WHERE photographer_id = ? AND available_date = ? AND time_slot = ?";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('iss', $photographerId, $availableDate, $timeSlot);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result->num_rows > 0) {
			return true;
		} else {
			return false;
		}
	}

	// UPDATE
	public function updateSlot($id, $availableDate, $timeSlot) {
		$sql = "UPDATE availability SET available_date = ?, time_slot = ? WHERE id = ?";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('ssi', $availableDate, $timeSlot, $id);
		return $stmt->execute();
	}

	// DELETE
	public function deleteSlot($id) {
		$sql = "DELETE FROM availability WHERE id = ?";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('i', $id);
		return $stmt->execute();
	}
}
