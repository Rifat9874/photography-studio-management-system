<?php
require_once __DIR__ . '/../db/db.php';

class Booking {
	public function establishConnection() {
		$db = new DBConnection();
		$conn = $db->connect();
		return $conn;
	}

	// CREATE - customer sends a booking request
	public function insertBooking($customerId, $packageId, $bookingDate, $bookingTime, $location, $note) {
		$sql = "INSERT INTO bookings (customer_id, package_id, booking_date, booking_time, location, note, status)
				VALUES (?, ?, ?, ?, ?, ?, 'pending')";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('iissss', $customerId, $packageId, $bookingDate, $bookingTime, $location, $note);
		$success = $stmt->execute();
		if ($success) {
			return $conn->insert_id;
		} else {
			return $conn->error;
		}
	}

	// READ - bookings of one customer (JOIN package + photographer)
	public function getBookingsByCustomer($customerId) {
		$sql = "SELECT b.*, p.name AS package_name, p.price, c.name AS category_name,
					   u.name AS photographer_name
				FROM bookings b
				JOIN packages p ON b.package_id = p.id
				JOIN categories c ON p.category_id = c.id
				LEFT JOIN users u ON b.photographer_id = u.id
				WHERE b.customer_id = ?
				ORDER BY b.id DESC";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('i', $customerId);
		$stmt->execute();
		$result = $stmt->get_result();

		$bookingList = [];
		while ($row = $result->fetch_assoc()) {
			array_push($bookingList, $row);
		}
		return $bookingList;
	}

	// READ - all bookings for the booking manager
	public function getAllBookings() {
		$sql = "SELECT b.*, p.name AS package_name, p.price, c.name AS category_name,
					   cu.name AS customer_name, cu.phone AS customer_phone,
					   u.name AS photographer_name
				FROM bookings b
				JOIN packages p ON b.package_id = p.id
				JOIN categories c ON p.category_id = c.id
				JOIN users cu ON b.customer_id = cu.id
				LEFT JOIN users u ON b.photographer_id = u.id
				ORDER BY b.id DESC";
		$conn = $this->establishConnection();
		$result = $conn->query($sql);

		$bookingList = [];
		while ($row = $result->fetch_assoc()) {
			array_push($bookingList, $row);
		}
		return $bookingList;
	}

	// READ - shoots assigned to one photographer
	public function getBookingsByPhotographer($photographerId) {
		$sql = "SELECT b.*, p.name AS package_name, c.name AS category_name,
					   cu.name AS customer_name, cu.phone AS customer_phone
				FROM bookings b
				JOIN packages p ON b.package_id = p.id
				JOIN categories c ON p.category_id = c.id
				JOIN users cu ON b.customer_id = cu.id
				WHERE b.photographer_id = ?
				ORDER BY b.booking_date DESC";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('i', $photographerId);
		$stmt->execute();
		$result = $stmt->get_result();

		$bookingList = [];
		while ($row = $result->fetch_assoc()) {
			array_push($bookingList, $row);
		}
		return $bookingList;
	}

	public function getBookingById($id) {
		$sql = "SELECT b.*, p.name AS package_name, p.price, p.category_id,
					   c.name AS category_name, cu.name AS customer_name,
					   u.name AS photographer_name
				FROM bookings b
				JOIN packages p ON b.package_id = p.id
				JOIN categories c ON p.category_id = c.id
				JOIN users cu ON b.customer_id = cu.id
				LEFT JOIN users u ON b.photographer_id = u.id
				WHERE b.id = ?";
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

	// small helper used on the dashboards
	public function countByStatus($status) {
		$sql = "SELECT COUNT(*) AS total FROM bookings WHERE status = ?";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('s', $status);
		$stmt->execute();
		$result = $stmt->get_result();
		$row = $result->fetch_assoc();
		return $row['total'];
	}

	// UPDATE - customer edits a pending booking
	public function updateBooking($id, $packageId, $bookingDate, $bookingTime, $location, $note) {
		$sql = "UPDATE bookings SET package_id = ?, booking_date = ?, booking_time = ?,
				location = ?, note = ? WHERE id = ?";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('issssi', $packageId, $bookingDate, $bookingTime, $location, $note, $id);
		return $stmt->execute();
	}

	// UPDATE - approve / reject / complete / cancel
	public function updateStatus($id, $status) {
		$sql = "UPDATE bookings SET status = ? WHERE id = ?";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('si', $status, $id);
		return $stmt->execute();
	}

	// UPDATE - manager assigns a photographer
	public function assignPhotographer($id, $photographerId) {
		$sql = "UPDATE bookings SET photographer_id = ?, status = 'assigned' WHERE id = ?";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('ii', $photographerId, $id);
		return $stmt->execute();
	}

	// DELETE
	public function deleteBooking($id) {
		$sql = "DELETE FROM bookings WHERE id = ?";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('i', $id);
		return $stmt->execute();
	}
}
