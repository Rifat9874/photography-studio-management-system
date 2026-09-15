<?php
require_once __DIR__ . '/../db/db.php';

class User {
	public function establishConnection() {
		$db = new DBConnection();
		$conn = $db->connect();
		return $conn;
	}

	// CREATE - new account
	public function insertUser($name, $username, $email, $password, $phone, $role, $specialization) {
		$sql = "INSERT INTO users (name, username, email, password, phone, role, specialization)
				VALUES (?, ?, ?, ?, ?, ?, ?)";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('sssssss', $name, $username, $email, $password, $phone, $role, $specialization);
		$success = $stmt->execute();
		if ($success) {
			return $conn->insert_id;
		} else {
			return $conn->error;
		}
	}

	// login check - user can type either username or email
	public function loginCheck($loginId, $password) {
		$sql = "SELECT * FROM users WHERE username = ? OR email = ?";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('ss', $loginId, $loginId);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result->num_rows > 0) {
			$row = $result->fetch_assoc();
			if ($row['password'] == $password) {
				return $row;
			}
		}
		return null;
	}

	public function isUsernameTaken($username) {
		$sql = "SELECT id FROM users WHERE username = ?";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('s', $username);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result->num_rows > 0) {
			return true;
		} else {
			return false;
		}
	}

	public function isEmailTaken($email) {
		$sql = "SELECT id FROM users WHERE email = ?";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('s', $email);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result->num_rows > 0) {
			return true;
		} else {
			return false;
		}
	}

	// READ - one user
	public function getUserById($id) {
		$sql = "SELECT * FROM users WHERE id = ?";
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

	// used by the "forgot password" page
	public function getUserByUsernameAndEmail($username, $email) {
		$sql = "SELECT * FROM users WHERE username = ? AND email = ?";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('ss', $username, $email);
		$stmt->execute();
		$result = $stmt->get_result();
		if ($result->num_rows > 0) {
			return $result->fetch_assoc();
		} else {
			return null;
		}
	}

	// READ - all users except admin himself
	public function getAllUsers() {
		$sql = "SELECT id, name, username, email, phone, role, specialization, status, created_at
				FROM users ORDER BY id";
		$conn = $this->establishConnection();
		$result = $conn->query($sql);

		$userList = [];
		while ($row = $result->fetch_assoc()) {
			array_push($userList, $row);
		}
		return $userList;
	}

	public function getUsersByRole($role) {
		$sql = "SELECT * FROM users WHERE role = ? AND status = 'active' ORDER BY name";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('s', $role);
		$stmt->execute();
		$result = $stmt->get_result();

		$userList = [];
		while ($row = $result->fetch_assoc()) {
			array_push($userList, $row);
		}
		return $userList;
	}

	// photographers who marked themselves free on this date (used by AJAX)
	public function getAvailablePhotographers($date) {
		$sql = "SELECT DISTINCT u.id, u.name, u.specialization, a.time_slot
				FROM users u
				JOIN availability a ON u.id = a.photographer_id
				WHERE u.role = 'photographer' AND u.status = 'active' AND a.available_date = ?
				ORDER BY u.name";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('s', $date);
		$stmt->execute();
		$result = $stmt->get_result();

		$photographerList = [];
		while ($row = $result->fetch_assoc()) {
			array_push($photographerList, $row);
		}
		return $photographerList;
	}

	// UPDATE - profile
	public function updateProfile($id, $name, $email, $phone, $specialization) {
		$sql = "UPDATE users SET name = ?, email = ?, phone = ?, specialization = ? WHERE id = ?";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('ssssi', $name, $email, $phone, $specialization, $id);
		return $stmt->execute();
	}

	public function updateProfilePic($id, $fileName) {
		$sql = "UPDATE users SET profile_pic = ? WHERE id = ?";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('si', $fileName, $id);
		return $stmt->execute();
	}

	public function updatePassword($id, $newPassword) {
		$sql = "UPDATE users SET password = ? WHERE id = ?";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('si', $newPassword, $id);
		return $stmt->execute();
	}

	// UPDATE - admin activates / deactivates an account
	public function updateStatus($id, $status) {
		$sql = "UPDATE users SET status = ? WHERE id = ?";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('si', $status, $id);
		return $stmt->execute();
	}

	// DELETE
	public function deleteUser($id) {
		$sql = "DELETE FROM users WHERE id = ?";
		$conn = $this->establishConnection();
		$stmt = $conn->prepare($sql);
		$stmt->bind_param('i', $id);
		return $stmt->execute();
	}
}
