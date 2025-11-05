

<?php
$_SESSION['UserId'] = $row['UserId'];
session_start();
// after validating user credentials

include('DBconnect.php');

// Redirect guest users to login
if (!isset($_SESSION['UserId'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['UserId'];

// Fetch user info for profile
$sql = "SELECT Username, Email FROM user WHERE UserId = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo "User not found.";
    exit();
}
?>
<!--frontend needs to be done better -->
<!DOCTYPE html>
<html>
<head> <title>Your Profile</title>
</head>
<body>
<h1>Welcome, <?php echo htmlspecialchars($user['Username']); ?>!</h1> 
<p>Email: <?php echo htmlspecialchars($user['Email']); ?></p>

<!-- Display profile picture if available -->
 <?php echo " ";/* if ($user['profile_pic']) { ?>
    <img src="<?php echo htmlspecialchars($user['profile_pic']); ?>" alt="Profile Picture" width="150" >
<?php } */  ?> 


<!-- Add more profile info, links to edit profile, logout etc. -->
 <footer> <div class="container">
            <p style="text-align:center;">© 2025 RR. Navana Sheikh. ReginaVincenza. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>
