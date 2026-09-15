<?php
require_once __DIR__ . '/../db/db.php';

class Complaint {
	public function establishConnection() {
		$db = new DBConnection();
		$conn = $db->connect();
		return $conn;
	}

	// CREATE
	public function insertComplaint($customerId, $bookingId, $subject, $message) {
		$sql = "INSERT INTO complaints (customer_id, booking_id, subject, message, status)
				VALUES (?, ?, ?, ?, 'open')";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('iiss', $customerId, $bookingId, $subject, $message);
		return $stmt->execute();
	}

	// READ
	public function getComplaintsByCustomer($customerId) {
		$sql = "SELECT * FROM complaints WHERE customer_id = ? ORDER BY id DESC";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('i', $customerId);
		$stmt->execute();
		$result = $stmt->get_result();

		$complaintList = [];
		while ($row = $result->fetch_assoc()) {
			array_push($complaintList, $row);
		}
		return $complaintList;
	}

	public function getAllComplaints() {
		$sql = "SELECT co.*, u.name AS customer_name, u.email AS customer_email
				FROM complaints co
				JOIN users u ON co.customer_id = u.id
				ORDER BY co.id DESC";
		$conn = $this->establishConnection();
		$result = $conn->query($sql);

		$complaintList = [];
		while ($row = $result->fetch_assoc()) {
			array_push($complaintList, $row);
		}
		return $complaintList;
	}

	public function getComplaintById($id) {
		$sql = "SELECT * FROM complaints WHERE id = ?";
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

	public function countByStatus($status) {
		$sql = "SELECT COUNT(*) AS total FROM complaints WHERE status = ?";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('s', $status);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		return $row['total'];
	}

	// UPDATE - admin answers and changes the status
	public function updateComplaint($id, $status, $adminReply) {
		$sql = "UPDATE complaints SET status = ?, admin_reply = ? WHERE id = ?";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('ssi', $status, $adminReply, $id);
		return $stmt->execute();
	}

	// DELETE
	public function deleteComplaint($id) {
		$sql = "DELETE FROM complaints WHERE id = ?";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('i', $id);
		return $stmt->execute();
	}
}
