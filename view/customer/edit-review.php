<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'customer') {
	header("Location: ../login.php");
	exit();
}

require_once __DIR__ . '/../../model/Review.php';

$id = $_GET['id'];

$review = new Review();
$reviewList = $review->getReviewsByCustomer($_SESSION['user_id']);

// find the review that belongs to this customer
$row = null;
for ($i = 0; $i < count($reviewList); $i++) {
	if ($reviewList[$i]['id'] == $id) {
		$row = $reviewList[$i];
	}
}

if ($row == null) {
	$_SESSION['error'] = "Review not found";
	header("Location: reviews.php");
	exit();
}

$base = "../../";
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<title>Edit Review</title>
	<link rel="stylesheet" href="../../css/style.css">
	<link rel="stylesheet" href="../../css/customer.css">
</head>
<body>
	<?php require_once __DIR__ . '/../nav.php'; ?>

	<div class="container">
		<h1>Edit Review #<?php echo $row['id'] ?></h1>

		<div class="box">
			<p>Package: <b><?php echo $row['package_name'] ?></b></p>
			<form action="../../controller/review-handler.php" method="post">
				<input type="hidden" name="action" value="update">
				<input type="hidden" name="id" value="<?php echo $row['id'] ?>">

				<label for="rating">Rating</label>
				<select id="rating" name="rating">
					<?php
					for ($star = 5; $star >= 1; $star--) {
						$selected = "";
						if ($star == $row['rating']) {
							$selected = "selected";
						}
						echo "<option value='" . $star . "' " . $selected . ">" . $star . " / 5</option>";
					}
					?>
				</select>

				<label for="comment">Comment</label>
				<textarea id="comment" name="comment"><?php echo $row['comment'] ?></textarea>

				<input type="submit" value="Update Review">
			</form>
			<p><a href="reviews.php">Back to my reviews</a></p>
		</div>
	</div>
</body>
</html>
