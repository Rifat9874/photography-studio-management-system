<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
	header("Location: ../login.php");
	exit();
}

require_once __DIR__ . '/../../model/Category.php';
require_once __DIR__ . '/../../model/Package.php';
require_once __DIR__ . '/../../model/User.php';

$category = new Category();
$categoryList = $category->getAllCategories();

$package = new Package();
$packageList = $package->getAllPackages();

$user = new User();
$photographerList = $user->getUsersByRole('photographer');

$base = "../../";
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Photography Packages</title>
	<link rel="stylesheet" href="../../css/style.css">
	<link rel="stylesheet" href="../../css/customer.css">
</head>
<body>
	<?php require_once __DIR__ . '/../nav.php'; ?>

	<div class="container">
		<h1>Photography Categories &amp; Packages</h1>
		<?php require_once __DIR__ . '/../message.php'; ?>

		<div class="box">
			<h2>Categories</h2>
			<table>
				<tr><th>Category</th><th>Description</th><th>Photographers specialized in it</th></tr>
				<?php
				for ($i = 0; $i < count($categoryList); $i++) {
					$categoryName = $categoryList[$i]['name'];

					// find the photographers of this category
					$names = "";
					for ($j = 0; $j < count($photographerList); $j++) {
						if ($photographerList[$j]['specialization'] == $categoryName) {
							$names = $names . $photographerList[$j]['name'] . ", ";
						}
					}
					if ($names == "") {
						$names = "-";
					}

					echo "<tr>";
					echo "<td>" . $categoryName . "</td>";
					echo "<td>" . $categoryList[$i]['description'] . "</td>";
					echo "<td>" . $names . "</td>";
					echo "</tr>";
				}
				?>
			</table>
		</div>

		<div class="box">
			<h2>Compare Packages</h2>
			<table>
				<tr>
					<th>Category</th>
					<th>Package</th>
					<th>Price (Tk)</th>
					<th>Duration</th>
					<th>Inclusions</th>
					<th>Book</th>
				</tr>
				<?php
				for ($i = 0; $i < count($packageList); $i++) {
					$row = $packageList[$i];
					echo "<tr>";
					echo "<td>" . $row['category_name'] . "</td>";
					echo "<td>" . $row['name'] . "</td>";
					echo "<td>" . $row['price'] . "</td>";
					echo "<td>" . $row['duration'] . "</td>";
					echo "<td>" . $row['inclusions'] . "</td>";
					echo "<td><a href='new-booking.php?package_id=" . $row['id'] . "'>Book this</a></td>";
					echo "</tr>";
				}
				?>
			</table>
		</div>
	</div>
</body>
</html>
