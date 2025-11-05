<?php // we need to connect the database to validate 
require_once('DBconnect.php'); 

// we need to check if the input fields are not empty
if(isset($_POST['uname']) && isset($_POST['pass'])){
    //this is to check if the user already exists in the database
    $username = $_POST['uname'];
    $password = $_POST['pass'];

    // we need to prevent SQL injection
    $username = stripcslashes($username);
    $password = stripcslashes($password);
    $username = mysqli_real_escape_string($conn, $username);
    $password = mysqli_real_escape_string($conn, $password);

    // now we need to check if the user exists in the database
    $sql = "SELECT * FROM user WHERE Username = '$username' AND Password = '$password'";
    $result = mysqli_query($conn, $sql);
    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
    $count = mysqli_num_rows($result);

    // if the user exists, then we need to start a session and redirect to profile page
    if($count == 1){
        session_start();
        $_SESSION['UserId'] = $row['UserId'];
        header('Location: landing.php');
    }else{
        echo "No account with this username exists. Please try again or sign up.";
        header('Location: landing.php');
        // sign up page e niye jabe
        //sql insert? then :))
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>Login Form Example</title>
<style>
  #sign-in {
    margin: 20px;
  }
  #login-form {
    display: none; /* Hidden by default */
    margin-top: 15px;
    border: 1px solid #ccc;
    padding: 15px;
    width: 300px;
    background-color: #f9f9f9;
  }
  #login-form label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
  }
  #login-form input[type="text"],
  #login-form input[type="password"] {
    width: 100%;
    padding: 8px;
    margin-bottom: 10px;
  }
  #login-form input[type="submit"] {
    width: 100%;
    padding: 10px;
    background-color: #4CAF50;
    color: white;
    border: none;
    cursor: pointer;
  }
  #login-form input[type="submit"]:hover {
    background-color: #45a049;
  }
</style>
</head>
<body>

<section id="sign-in">
  <button id="toggle-login">Login/Signup</button>
  
  <form id="login-form" action="login.php" method="post">
    <label for="uname">Username:</label>
    <input type="text" id="uname" name="uname" required />
    
    <label for="pass">Password:</label>
    <input type="password" id="pass" name="pass" required />
    
    <input type="submit" value="Sign In" />
  </form>
</section>

<script>
  const toggleButton = document.getElementById('toggle-login');
  const loginForm = document.getElementById('login-form');
  
  toggleButton.addEventListener('click', () => {
    if (loginForm.style.display === 'none' || loginForm.style.display === '') {
      loginForm.style.display = 'block';
    } else {
      loginForm.style.display = 'none';
    }
  });
</script>

</body>
</html>
