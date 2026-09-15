<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
	header("Location: ../login.php");
	exit();
}

require_once __DIR__ . '/../../model/Portfolio.php';
require_once __DIR__ . '/../../model/User.php';

$portfolio = new Portfolio();
$itemList = $portfolio->getAllItems();

$user = new User();
$photographerList = $user->getUsersByRole('photographer');

$base = "../../";
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Our Photographers</title>
	<link rel="stylesheet" href="../../css/style.css">
	<link rel="stylesheet" href="../../css/customer.css">
</head>
<body>
	<?php require_once __DIR__ . '/../nav.php'; ?>

	<div class="container">
		<h1>Our Photographers</h1>

		<div class="box">
			<h2>Photographer List</h2>
			<table>
				<tr><th>Name</th><th>Specialization</th><th>Phone</th></tr>
				<?php
				for ($i = 0; $i < count($photographerList); $i++) {
					echo "<tr>";
					echo "<td>" . $photographerList[$i]['name'] . "</td>";
					echo "<td>" . $photographerList[$i]['specialization'] . "</td>";
					echo "<td>" . $photographerList[$i]['phone'] . "</td>";
					echo "</tr>";
				}
				?>
			</table>
		</div>

		<div class="box">
			<h2>Sample Work (Portfolio)</h2>
			<table>
				<tr><th>Picture</th><th>Title</th><th>Category</th><th>Photographer</th><th>Description</th></tr>
				<?php
				for ($i = 0; $i < count($itemList); $i++) {
					$row = $itemList[$i];
					if ($row['image'] != '') {
						$picture = "<img class='portfolio-pic' src='../../images/" . $row['image'] . "'>";
					} else {
						$picture = "No picture";
					}
					echo "<tr>";
					echo "<td>" . $picture . "</td>";
					echo "<td>" . $row['title'] . "</td>";
					echo "<td>" . $row['category_name'] . "</td>";
					echo "<td>" . $row['photographer_name'] . "</td>";
					echo "<td>" . $row['description'] . "</td>";
					echo "</tr>";
				}
				if (count($itemList) == 0) {
					echo "<tr><td colspan='5'>No portfolio item yet.</td></tr>";
				}
				?>
			</table>
		</div>
	</div>
</body>
</html>
