<?php
session_start(); // Start the session to access session variables

// Check if user is logged in
if (!isset($_SESSION['UserId'])) {
    // User not logged in, show login form and exit
    include('login.php');
    exit;
}

include('dbconnect.php'); // Include DB connection once here

$user_id = $_SESSION['UserId'];
$message = '';



// Fetch saved recipes for the user
$sql = "SELECT r.RecipeId, r.RecipeName, r.Description 
        FROM recipe r
        JOIN saves s ON r.RecipeId = s.Srecipeid
        WHERE s.Suserid = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Saved Recipes | RR</title>

<!-- Bootstrap CSS and other stylesheets -->
<link href="css/bootstrap.min.css" rel="stylesheet">
<link href="font-awesome.min.css" rel="stylesheet">
<link href="css/animate.min.css" rel="stylesheet">
<link href="styles.css" rel="stylesheet">

</head>
<body>

<div class="container">
    <h2>Your Saved Recipes</h2>

    <?php if ($message): ?>
        <p><strong><?php echo htmlspecialchars($message); ?></strong></p>
    <?php endif; ?>

    <?php if ($result->num_rows === 0): ?>
        <p>You have no saved recipes.</p>
    <?php else: ?>
        <ul>
            <?php while ($recipe = $result->fetch_assoc()): ?>
                <li>
                    <h3><?php echo htmlspecialchars($recipe['RecipeName']); ?></h3>
                    <p><?php echo htmlspecialchars($recipe['Description']); ?></p>
                    <!-- You can add a link to the full recipe page -->
                    <form method="POST" action="Saved.php" style="display:inline;">
                        <input type="hidden" name="RecipeId" value="<?php echo htmlspecialchars($recipe['RecipeId']); ?>">
                        <button type="submit">Save Again</button>
                    </form>
                </li>
            <?php endwhile; ?>
        </ul>
    <?php endif; ?>
</div>

<footer> 
    <div class="container">
        <p style="text-align:center;">© 2025 RR. Navana Sheikh. ReginaVincenza. All Rights Reserved.</p>
    </div>
</footer>

<!-- JS Files -->
<script src="js/jquery.js"></script>
<script src="js/bootstrap.min.js"></script>
<script src="js/main.js"></script>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
