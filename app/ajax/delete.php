<?php 

session_start();

# barresi log in budane user
if (isset($_SESSION['username'])) {

    if (isset($_POST['chat_id'])) {

        include '../db.conn.php';

        $chat_id = $_POST['chat_id'];

        $sql = "DELETE FROM chats 
            WHERE chat_id = $chat_id";
        $stmt = $conn->prepare($sql);
        $res = $stmt->execute();

        

    }
}