<?php  

session_start();

# barresi log in budane user
if (isset($_SESSION['username'])) {

	include '../db.conn.php';

	$id = $_SESSION['user_id'];

	//update lastseen user login
	$sql = "UPDATE users
	        SET last_seen = NOW() 
	        WHERE user_id = ?";
	$stmt = $conn->prepare($sql);
	$stmt->execute([$id]);


}else {
	header("Location: ../../index.php");
	exit;
}