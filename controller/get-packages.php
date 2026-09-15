<?php
// AJAX + JSON endpoint 2 - returns the packages of one category
require_once __DIR__ . '/../model/Package.php';

header('Content-Type: application/json');

$categoryId = isset($_GET['category_id']) ? $_GET['category_id'] : 0;

$package = new Package();
$packageList = $package->getPackagesByCategory($categoryId);

echo json_encode(array('packages' => $packageList));
