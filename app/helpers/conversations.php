<?php 

function getConversation($user_id, $conn){
    #daryafte hameye conv haye karbare log in 
    $sql = "SELECT * FROM conversations
            WHERE user_1=? OR user_2=?
            ORDER BY conversation_id DESC";

    $stmt = $conn->prepare($sql);
    $stmt->execute([$user_id, $user_id]);

    if($stmt->rowCount() > 0){
        $conversations = $stmt->fetchAll();

        #ijade araye braye zakhire convs
        $user_data = [];
        
        # loop dar convs 
        foreach($conversations as $conversation){
            # moghayese user-id session ba user id conv
            
            if ($conversation['user_1'] == $user_id) {
            	$sql2  = "SELECT *
            	          FROM users WHERE user_id=?";
            	$stmt2 = $conn->prepare($sql2);
            	$stmt2->execute([$conversation['user_2']]);
            }else {
            	$sql2  = "SELECT *
            	          FROM users WHERE user_id=?";
            	$stmt2 = $conn->prepare($sql2);
            	$stmt2->execute([$conversation['user_1']]);
            }

            $allConversations = $stmt2->fetchAll();

            # push kardane data dar araye 
            array_push($user_data, $allConversations[0]);
        }

        return $user_data;

    }else {
    	$conversations = [];
    	return $conversations;
    }  

}