<?php 

session_start();

# barresi log in budane user
if (isset($_SESSION['username'])) {

	if (isset($_POST['message']) &&
        isset($_POST['to_id'])) {
	include '../db.conn.php';

	# daryafte etelaat payam va zakhire dar db
	$message = $_POST['message'];
	$to_id = $_POST['to_id'];
	$from_id = $_SESSION['user_id'];

	$sql = "INSERT INTO 
	       chats (from_id, to_id, message) 
	       VALUES (?, ?, ?)";
	$stmt = $conn->prepare($sql);
	$res  = $stmt->execute([$from_id, $to_id, $message]);
	
	$chat_id = $conn->lastInsertId();
    # baresi vared shodane payam
    if ($res) {

		
       $sql2 = "SELECT * FROM conversations
               WHERE (user_1=? AND user_2=?)
               OR    (user_2=? AND user_1=?)";
       $stmt2 = $conn->prepare($sql2);
	   $stmt2->execute([$from_id, $to_id, $from_id, $to_id]);

	    // tanzime time zone
		define('TIMEZONE', 'Asia/Tehran');
		date_default_timezone_set(TIMEZONE);

		$time = date("h:i:s a");

		//aya in avalin conversation beine an hast
		if ($stmt2->rowCount() == 0 ) {
			# vared kardan dar jadvale conversation
			$sql3 = "INSERT INTO 
			         conversations(user_1, user_2)
			         VALUES (?,?)";
			$stmt3 = $conn->prepare($sql3); 
			$stmt3->execute([$from_id, $to_id]);
		}

		?>
		<p class="rtext align-self-end
		          border rounded p-2 mb-1">
		    <?=$message?>  
		    <small class="d-block"><?=$time?></small>   

		

    <?php 
     }
  }
}else {
	header("Location: ../../index.php");
	exit;
}