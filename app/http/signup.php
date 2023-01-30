<?php  

# check kardane submit shodane user & pass
if(isset($_POST['username']) &&
   isset($_POST['password']) &&
   isset($_POST['name'])){

   include '../db.conn.php';
   
  	# gereftane data ba post request
   $name = $_POST['name'];
   $password = $_POST['password'];
   $username = $_POST['username'];

   # URL ba format data
   $data = 'name='.$name.'&username='.$username;

   //validate kardane user baraye sign up
   if (empty($name)) {
   	  $em = "Name is required";

   	  //redirect kardan be signup ba error message
   	  header("Location: ../../signup.php?error=$em");
   	  exit;
   }else if(empty($username)){
   	  $em = "Username is required";

   	  //redirect kardan be signup ba error message
   	  header("Location: ../../signup.php?error=$em&$data");
   	  exit;
   }else if(empty($password)){

   	  $em = "Password is required";

   	  //redirect kardan be signup ba error message
   	  header("Location: ../../signup.php?error=$em&$data");
   	  exit;
   }else {
   	  //check kardan vojood username
   	  $sql = "SELECT username 
   	          FROM users
   	          WHERE username=?";
      $stmt = $conn->prepare($sql);
      $stmt->execute([$username]);

      if($stmt->rowCount() > 0){
      	$em = "The username ($username) is taken";
      	header("Location: ../../signup.php?error=$em&$data");
   	    exit;
      }else {
      	//upload profile picture
      	if (isset($_FILES['pp'])) {
      		# get data and store them in var
      		$img_name  = $_FILES['pp']['name'];
      		$tmp_name  = $_FILES['pp']['tmp_name'];
      		$error  = $_FILES['pp']['error'];

      		if($error === 0){
               
               # get image extension store it in var
      		   $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);

               #zakhire ax ba format lower case
				$img_ex_lc = strtolower($img_ex);

				#format mojaz zakhire ax
				$allowed_exs = array("jpg", "jpeg", "png");

				#baresi mojaz budan format ax
				if (in_array($img_ex_lc, $allowed_exs)) {
					#taghire name ax ba username
					$new_img_name = $username. '.'.$img_ex_lc;

					# sakhtane upload path dar root
					$img_upload_path = '../../uploads/'.$new_img_name;

					# enteghal ax be masire taeen shode
                    move_uploaded_file($tmp_name, $img_upload_path);
				}else {
					$em = "You can't upload files of this type";
			      	header("Location: ../../signup.php?error=$em&$data");
			   	    exit;
				}

      		}
      	}

      	// password hashing
      	$password = password_hash($password, PASSWORD_DEFAULT);

      	//insert user ba P_P
      	if (isset($new_img_name)) {

            $sql = "INSERT INTO users
                    (name, username, password, p_p)
                    VALUES (?,?,?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$name, $username, $password, $new_img_name]);
      	//insert user bedoone P_P
		}else {
            # inserting data into database
            $sql = "INSERT INTO users
                    (name, username, password)
                    VALUES (?,?,?)";
            $stmt = $conn->prepare($sql);
            $stmt->execute([$name, $username, $password]);
      	}

      	// success message
      	$sm = "Account created successfully";

      	// redirect be login
      	header("Location: ../../index.php?success=$sm");
     	exit;
      }

   }
}else {
	header("Location: ../../signup.php");
   	exit;
}