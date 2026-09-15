<?php
require_once __DIR__ . '/../db/db.php';

class Review {
	public function establishConnection() {
		$db = new DBConnection();
		$conn = $db->connect();
		return $conn;
	}

	// CREATE
	public function insertReview($bookingId, $customerId, $rating, $comment) {
		$sql = "INSERT INTO reviews (booking_id, customer_id, rating, comment) VALUES (?, ?, ?, ?)";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('iiis', $bookingId, $customerId, $rating, $comment);
		return $stmt->execute();
	}

	// READ - one review of one booking
	public function getReviewByBooking($bookingId) {
		$sql = "SELECT * FROM reviews WHERE booking_id = ?";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('i', $bookingId);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result->num_rows > 0) {
			return $result->fetch_assoc();
		} else {
			return null;
		}
	}

	public function getReviewsByCustomer($customerId) {
		$sql = "SELECT r.*, p.name AS package_name, b.booking_date
				FROM reviews r
				JOIN bookings b ON r.booking_id = b.id
				JOIN packages p ON b.package_id = p.id
				WHERE r.customer_id = ?
				ORDER BY r.id DESC";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('i', $customerId);
		$stmt->execute();
		$result = $stmt->get_result();

		$reviewList = [];
		while ($row = $result->fetch_assoc()) {
			array_push($reviewList, $row);
		}
		return $reviewList;
	}

	// reviews of the shoots done by one photographer
	public function getReviewsByPhotographer($photographerId) {
		$sql = "SELECT r.*, p.name AS package_name, cu.name AS customer_name, b.booking_date
				FROM reviews r
				JOIN bookings b ON r.booking_id = b.id
				JOIN packages p ON b.package_id = p.id
				JOIN users cu ON r.customer_id = cu.id
				WHERE b.photographer_id = ?
				ORDER BY r.id DESC";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('i', $photographerId);
		$stmt->execute();
		$result = $stmt->get_result();

		$reviewList = [];
		while ($row = $result->fetch_assoc()) {
			array_push($reviewList, $row);
		}
		return $reviewList;
	}

	public function getAllReviews() {
		$sql = "SELECT r.*, p.name AS package_name, cu.name AS customer_name
				FROM reviews r
				JOIN bookings b ON r.booking_id = b.id
				JOIN packages p ON b.package_id = p.id
				JOIN users cu ON r.customer_id = cu.id
				ORDER BY r.id DESC";
		$conn = $this->establishConnection();
		$result = $conn->query($sql);

		$reviewList = [];
		while ($row = $result->fetch_assoc()) {
			array_push($reviewList, $row);
		}
		return $reviewList;
	}

	// UPDATE
	public function updateReview($id, $rating, $comment) {
		$sql = "UPDATE reviews SET rating = ?, comment = ? WHERE id = ?";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('isi', $rating, $comment, $id);
		return $stmt->execute();
	}

	// DELETE
	public function deleteReview($id) {
		$sql = "DELETE FROM reviews WHERE id = ?";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('i', $id);
		return $stmt->execute();
	}
}
