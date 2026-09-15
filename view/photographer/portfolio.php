<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'photographer') {
	header("Location: ../login.php");
	exit();
}

require_once __DIR__ . '/../../model/Portfolio.php';
require_once __DIR__ . '/../../model/Category.php';

$photographerId = $_SESSION['user_id'];

$portfolio = new Portfolio();
$itemList = $portfolio->getItemsByPhotographer($photographerId);

$category = new Category();
$categoryList = $category->getAllCategories();

// if the Edit link was clicked, load that item into the form
$editItem = null;
if (isset($_GET['edit_id'])) {
	$editItem = $portfolio->getItemById($_GET['edit_id']);
	if ($editItem != null && $editItem['photographer_id'] != $photographerId) {
		$editItem = null;
	}
}

$base = "../../";
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>My Portfolio</title>
	<link rel="stylesheet" href="../../css/style.css">
	<link rel="stylesheet" href="../../css/photographer.css">
	<script src="../../js/script.js"></script>
	<script src="../../js/photographer.js"></script>
</head>
<body>
	<?php require_once __DIR__ . '/../nav.php'; ?>

	<div class="container">
		<h1>Portfolio Management</h1>
		<?php require_once __DIR__ . '/../message.php'; ?>

		<div class="box">
			<?php if ($editItem == null) { ?>
				<h2>Add New Sample Work</h2>
			<?php } else { ?>
				<h2>Edit Item #<?php echo $editItem['id'] ?></h2>
			<?php } ?>

			<form action="../../controller/portfolio-handler.php" method="post" enctype="multipart/form-data">
				<?php if ($editItem == null) { ?>
					<input type="hidden" name="action" value="create">
				<?php } else { ?>
					<input type="hidden" name="action" value="update">
					<input type="hidden" name="id" value="<?php echo $editItem['id'] ?>">
				<?php } ?>

				<label for="title">Title</label>
				<input type="text" id="title" name="title"
					   value="<?php if ($editItem != null) echo $editItem['title'] ?>" required>

				<label for="category_id">Category</label>
				<select id="category_id" name="category_id">
					<?php
					for ($i = 0; $i < count($categoryList); $i++) {
						$selected = "";
						if ($editItem != null && $editItem['category_id'] == $categoryList[$i]['id']) {
							$selected = "selected";
						}
						echo "<option value='" . $categoryList[$i]['id'] . "' " . $selected . ">" .
							 $categoryList[$i]['name'] . "</option>";
					}
					?>
				</select>

				<label for="description">Description</label>
				<textarea id="description" name="description"><?php if ($editItem != null) echo $editItem['description'] ?></textarea>

				<label for="image">Picture (jpg / png)</label>
				<input type="file" id="image" name="image">

				<?php if ($editItem == null) { ?>
					<input type="submit" value="Add Item">
				<?php } else { ?>
					<input type="submit" value="Update Item">
					<a href="portfolio.php">Cancel</a>
				<?php } ?>
			</form>
		</div>

		<div class="box">
			<h2>My Sample Works</h2>
			<table>
				<tr><th>Picture</th><th>Title</th><th>Category</th><th>Description</th><th>Actions</th></tr>
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
					echo "<td>" . $row['description'] . "</td>";
					echo "<td>
						<a href='portfolio.php?edit_id=" . $row['id'] . "'>Edit</a>
						<form action='../../controller/portfolio-handler.php' method='post' style='display:inline' onsubmit='return confirmDelete()'>
							<input type='hidden' name='action' value='delete'>
							<input type='hidden' name='id' value='" . $row['id'] . "'>
							<input type='submit' class='small-btn danger' value='Delete'>
						</form>
					</td>";
					echo "</tr>";
				}
				if (count($itemList) == 0) {
					echo "<tr><td colspan='5'>Your portfolio is empty.</td></tr>";
				}
				?>
			</table>
		</div>
	</div>
</body>
</html>
