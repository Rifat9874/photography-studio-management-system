<?php
// shows the success / error message that a controller saved in the session
if (isset($_SESSION['message'])) {
	echo "<div class='message'>" . $_SESSION['message'] . "</div>";
	unset($_SESSION['message']);
}
if (isset($_SESSION['error'])) {
	echo "<div class='error-message'>" . $_SESSION['error'] . "</div>";
	unset($_SESSION['error']);
}
?>
