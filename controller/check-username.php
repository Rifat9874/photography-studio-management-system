<?php
// AJAX + JSON endpoint 1 - called from the registration page while the user types
require_once __DIR__ . '/../model/User.php';

header('Content-Type: application/json');

$username = isset($_GET['username']) ? trim($_GET['username']) : '';

if (strlen($username) < 3) {
	echo json_encode(array('available' => false, 'message' => 'Username must be at least 3 characters'));
	exit();
}

$user = new User();
if ($user->isUsernameTaken($username)) {
	echo json_encode(array('available' => false, 'message' => 'This username is already taken'));
} else {
	echo json_encode(array('available' => true, 'message' => 'Username is available'));
}
