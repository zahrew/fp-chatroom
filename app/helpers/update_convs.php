<?php

function update_convs($user_id, $conn)
{

    $sql = "SELECT * FROM chats 
            WHERE to_id = ?
            And opened = ?";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_id, 0]);

    echo $stmt->rowCount();
}