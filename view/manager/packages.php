<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'manager') {
	header("Location: ../login.php");
	exit();
}

require_once __DIR__ . '/../../model/Package.php';
require_once __DIR__ . '/../../model/Category.php';

$package = new Package();
$packageList = $package->getAllPackages();

$category = new Category();
$categoryList = $category->getAllCategories();

// load one package into the form when Edit is clicked
$editPackage = null;
if (isset($_GET['edit_id'])) {
	$editPackage = $package->getPackageById($_GET['edit_id']);
}

$base = "../../";
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Package Management</title>
	<link rel="stylesheet" href="../../css/style.css">
	<link rel="stylesheet" href="../../css/manager.css">
	<script src="../../js/script.js"></script>
	<script src="../../js/manager.js"></script>
</head>
<body>
	<?php require_once __DIR__ . '/../nav.php'; ?>

	<div class="container">
		<h1>Photography Package Management</h1>
		<?php require_once __DIR__ . '/../message.php'; ?>

		<div class="box">
			<?php if ($editPackage == null) { ?>
				<h2>Create New Package</h2>
			<?php } else { ?>
				<h2>Edit Package #<?php echo $editPackage['id'] ?></h2>
			<?php } ?>

			<form action="../../controller/package-handler.php" method="post">
				<?php if ($editPackage == null) { ?>
					<input type="hidden" name="action" value="create">
				<?php } else { ?>
					<input type="hidden" name="action" value="update">
					<input type="hidden" name="id" value="<?php echo $editPackage['id'] ?>">
				<?php } ?>

				<label for="category_id">Category</label>
				<select id="category_id" name="category_id">
					<?php
					for ($i = 0; $i < count($categoryList); $i++) {
						$selected = "";
						if ($editPackage != null && $editPackage['category_id'] == $categoryList[$i]['id']) {
							$selected = "selected";
						}
						echo "<option value='" . $categoryList[$i]['id'] . "' " . $selected . ">" .
							 $categoryList[$i]['name'] . "</option>";
					}
					?>
				</select>

				<label for="name">Package Name</label>
				<input type="text" id="name" name="name"
					   value="<?php if ($editPackage != null) echo $editPackage['name'] ?>" required>

				<label for="price">Price (Tk)</label>
				<input type="number" id="price" name="price" step="0.01"
					   value="<?php if ($editPackage != null) echo $editPackage['price'] ?>" required>

				<label for="duration">Duration</label>
				<input type="text" id="duration" name="duration"
					   value="<?php if ($editPackage != null) echo $editPackage['duration'] ?>">

				<label for="inclusions">Inclusions</label>
				<textarea id="inclusions" name="inclusions"><?php if ($editPackage != null) echo $editPackage['inclusions'] ?></textarea>

				<?php if ($editPackage == null) { ?>
					<input type="submit" value="Create Package">
				<?php } else { ?>
					<input type="submit" value="Update Package">
					<a href="packages.php">Cancel</a>
				<?php } ?>
			</form>
		</div>

		<div class="box">
			<h2>All Packages</h2>
			<table>
				<tr><th>ID</th><th>Category</th><th>Name</th><th>Price</th><th>Duration</th><th>Inclusions</th><th>Actions</th></tr>
				<?php
				for ($i = 0; $i < count($packageList); $i++) {
					$row = $packageList[$i];
					echo "<tr>";
					echo "<td>" . $row['id'] . "</td>";
					echo "<td>" . $row['category_name'] . "</td>";
					echo "<td>" . $row['name'] . "</td>";
					echo "<td>" . $row['price'] . "</td>";
					echo "<td>" . $row['duration'] . "</td>";
					echo "<td>" . $row['inclusions'] . "</td>";
					echo "<td>
						<a href='packages.php?edit_id=" . $row['id'] . "'>Edit</a>
						<form action='../../controller/package-handler.php' method='post' style='display:inline' onsubmit='return confirmDelete()'>
							<input type='hidden' name='action' value='delete'>
							<input type='hidden' name='id' value='" . $row['id'] . "'>
							<input type='submit' class='small-btn danger' value='Delete'>
						</form>
					</td>";
					echo "</tr>";
				}
				?>
			</table>
		</div>
	</div>
</body>
</html>
