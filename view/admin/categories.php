<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
	header("Location: ../login.php");
	exit();
}

require_once __DIR__ . '/../../model/Category.php';
require_once __DIR__ . '/../../model/Package.php';

$category = new Category();
$categoryList = $category->getAllCategories();

$package = new Package();
$packageList = $package->getAllPackages();

// load one category into the form when Edit is clicked
$editCategory = null;
if (isset($_GET['edit_id'])) {
	$editCategory = $category->getCategoryById($_GET['edit_id']);
}

$base = "../../";
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Category Management</title>
	<link rel="stylesheet" href="../../css/style.css">
	<link rel="stylesheet" href="../../css/admin.css">
	<script src="../../js/script.js"></script>
	<script src="../../js/admin.js"></script>
</head>
<body>
	<?php require_once __DIR__ . '/../nav.php'; ?>

	<div class="container">
		<h1>Photography Category Management</h1>
		<?php require_once __DIR__ . '/../message.php'; ?>

		<div class="box">
			<?php if ($editCategory == null) { ?>
				<h2>Create New Category</h2>
			<?php } else { ?>
				<h2>Edit Category #<?php echo $editCategory['id'] ?></h2>
			<?php } ?>

			<form action="../../controller/category-handler.php" method="post">
				<?php if ($editCategory == null) { ?>
					<input type="hidden" name="action" value="create">
				<?php } else { ?>
					<input type="hidden" name="action" value="update">
					<input type="hidden" name="id" value="<?php echo $editCategory['id'] ?>">
				<?php } ?>

				<label for="name">Category Name</label>
				<input type="text" id="name" name="name"
					   value="<?php if ($editCategory != null) echo $editCategory['name'] ?>" required>

				<label for="description">Description</label>
				<input type="text" id="description" name="description"
					   value="<?php if ($editCategory != null) echo $editCategory['description'] ?>">

				<?php if ($editCategory == null) { ?>
					<input type="submit" value="Create Category">
				<?php } else { ?>
					<input type="submit" value="Update Category">
					<a href="categories.php">Cancel</a>
				<?php } ?>
			</form>
		</div>

		<div class="box">
			<h2>All Categories</h2>
			<table>
				<tr><th>ID</th><th>Name</th><th>Description</th><th>Packages in it</th><th>Actions</th></tr>
				<?php
				for ($i = 0; $i < count($categoryList); $i++) {
					$row = $categoryList[$i];

					// count how many packages use this category
					$packageCount = 0;
					for ($j = 0; $j < count($packageList); $j++) {
						if ($packageList[$j]['category_id'] == $row['id']) {
							$packageCount = $packageCount + 1;
						}
					}

					echo "<tr>";
					echo "<td>" . $row['id'] . "</td>";
					echo "<td>" . $row['name'] . "</td>";
					echo "<td>" . $row['description'] . "</td>";
					echo "<td>" . $packageCount . "</td>";
					echo "<td>
						<a href='categories.php?edit_id=" . $row['id'] . "'>Edit</a>
						<form action='../../controller/category-handler.php' method='post' style='display:inline' onsubmit='return confirmDelete()'>
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
