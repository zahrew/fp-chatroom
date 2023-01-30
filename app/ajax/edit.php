<?php 

session_start();

# barresi log in budane user
if (isset($_SESSION['username'])) {
    if (isset($_POST['chat_id']) &&
        isset($_POST['message_edit']) ) {
       
        include '../db.conn.php';

        $chat_id = $_POST['chat_id'];
        $message_edit = $_POST['message_edit'];
        echo $message_edit;

        $sql = "UPDATE chats 
                SET message = ?
                WHERE chat_id = $chat_id";

        $stmt = $conn->prepare($sql);
        $res = $stmt->execute([$message_edit]);

        echo "reached";
    }
}