<?php
// AJAX + JSON endpoint 3 - photographers who are free on the given date
require_once __DIR__ . '/../model/User.php';

header('Content-Type: application/json');

$date = isset($_GET['date']) ? $_GET['date'] : '';

$user = new User();
$photographerList = $user->getAvailablePhotographers($date);

echo json_encode(array('photographers' => $photographerList));
