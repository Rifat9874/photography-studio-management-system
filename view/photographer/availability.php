<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'photographer') {
	header("Location: ../login.php");
	exit();
}

require_once __DIR__ . '/../../model/Availability.php';

$photographerId = $_SESSION['user_id'];

$availability = new Availability();
$slotList = $availability->getSlotsByPhotographer($photographerId);

// load one slot into the form when Edit is clicked
$editSlot = null;
if (isset($_GET['edit_id'])) {
	$editSlot = $availability->getSlotById($_GET['edit_id']);
	if ($editSlot != null && $editSlot['photographer_id'] != $photographerId) {
		$editSlot = null;
	}
}

$base = "../../";
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>My Availability</title>
	<link rel="stylesheet" href="../../css/style.css">
	<link rel="stylesheet" href="../../css/photographer.css">
	<script src="../../js/script.js"></script>
	<script src="../../js/photographer.js"></script>
</head>
<body>
	<?php require_once __DIR__ . '/../nav.php'; ?>

	<div class="container">
		<h1>Availability Management</h1>
		<p>The booking manager can only assign you on the dates you add here.</p>
		<?php require_once __DIR__ . '/../message.php'; ?>

		<div class="box">
			<?php if ($editSlot == null) { ?>
				<h2>Add a Free Date</h2>
			<?php } else { ?>
				<h2>Edit Slot #<?php echo $editSlot['id'] ?></h2>
			<?php } ?>

			<form action="../../controller/availability-handler.php" method="post">
				<?php if ($editSlot == null) { ?>
					<input type="hidden" name="action" value="create">
				<?php } else { ?>
					<input type="hidden" name="action" value="update">
					<input type="hidden" name="id" value="<?php echo $editSlot['id'] ?>">
				<?php } ?>

				<label for="available_date">Date</label>
				<input type="date" id="available_date" name="available_date"
					   value="<?php if ($editSlot != null) echo $editSlot['available_date'] ?>" required>

				<label for="time_slot">Time Slot</label>
				<select id="time_slot" name="time_slot">
					<option value="Morning" <?php if ($editSlot != null && $editSlot['time_slot'] == 'Morning') echo 'selected' ?>>Morning</option>
					<option value="Afternoon" <?php if ($editSlot != null && $editSlot['time_slot'] == 'Afternoon') echo 'selected' ?>>Afternoon</option>
					<option value="Evening" <?php if ($editSlot != null && $editSlot['time_slot'] == 'Evening') echo 'selected' ?>>Evening</option>
				</select>

				<?php if ($editSlot == null) { ?>
					<input type="submit" value="Add Availability">
				<?php } else { ?>
					<input type="submit" value="Update Availability">
					<a href="availability.php">Cancel</a>
				<?php } ?>
			</form>
		</div>

		<div class="box">
			<h2>My Free Dates</h2>
			<table>
				<tr><th>ID</th><th>Date</th><th>Time Slot</th><th>Actions</th></tr>
				<?php
				for ($i = 0; $i < count($slotList); $i++) {
					$row = $slotList[$i];
					echo "<tr>";
					echo "<td>" . $row['id'] . "</td>";
					echo "<td>" . $row['available_date'] . "</td>";
					echo "<td>" . $row['time_slot'] . "</td>";
					echo "<td>
						<a href='availability.php?edit_id=" . $row['id'] . "'>Edit</a>
						<form action='../../controller/availability-handler.php' method='post' style='display:inline' onsubmit='return confirmDelete()'>
							<input type='hidden' name='action' value='delete'>
							<input type='hidden' name='id' value='" . $row['id'] . "'>
							<input type='submit' class='small-btn danger' value='Delete'>
						</form>
					</td>";
					echo "</tr>";
				}
				if (count($slotList) == 0) {
					echo "<tr><td colspan='4'>You have not added any free date.</td></tr>";
				}
				?>
			</table>
		</div>
	</div>
</body>
</html>
