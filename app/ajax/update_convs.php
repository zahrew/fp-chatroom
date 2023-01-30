<?php  

session_start();

# barresi log in budane user
if (isset($_SESSION['username'])) {
    include '../db.conn.php';

    $id = $_SESSION['user_id'];

    $sql = "SELECT * FROM chats 
            WHERE to_id = ?
            And opened = ?";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$id, 0]);

    echo ($stmt->rowCount());
}