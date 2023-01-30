<?php
session_start();

# check kardane submit shodane user & pass
if (
  isset($_POST['username']) &&
  isset($_POST['password'])
) {

  include '../db.conn.php';

  # gereftane user pass ba post request
  $password = $_POST['password'];
  $username = $_POST['username'];

  //validate kardane user baraye sign in
  if (empty($username)) {

    $em = "Username is required";
    //redirect kardan be login ba error message
    header("Location: ../../index.php?error=$em");
  } else if (empty($password)) {
    $em = "Password is required";
    //redirect kardan be login ba error message
    header("Location: ../../index.php?error=$em");
  } else {
    $sql = "SELECT * FROM 
               users WHERE username=?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$username]);

    //check kardane user & pass baraye login
    if ($stmt->rowCount() === 1) {

      $user = $stmt->fetch();

      //check user
      if ($user['username'] === $username) {

        # check encrypted password
        if (password_verify($password, $user['password'])) {

          # sakhtane session darsoorate login movafagh
          $_SESSION['username'] = $user['username'];
          $_SESSION['name'] = $user['name'];
          $_SESSION['user_id'] = $user['user_id'];

          # redirect be 'home.php'
          header("Location: ../../home.php");

        } else {
          $em = "Incorect Username or password";

          //redirect kardan be login ba error message
          header("Location: ../../index.php?error=$em");
        }
      } else {
        $em = "Incorect Username or password";

        //redirect kardan be login ba error message
        header("Location: ../../index.php?error=$em");
      }
    }
  }
} else {
  header("Location: ../../index.php");
  exit;
}