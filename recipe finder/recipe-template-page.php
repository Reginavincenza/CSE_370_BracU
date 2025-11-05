
<?php
include('DBconnect.php');

$message = '';
$recipe = null;
$recipe_id = null;

if (isset($_GET['RecipeId'])) {
    $recipe_id = $_GET['RecipeId'];

    // Prepare and execute query to fetch recipe details
    $stmt = $conn->prepare("SELECT RecipeId, RecipeName, Instructions, description, Ingredients FROM recipe WHERE RecipeId = ?");
    $stmt->bind_param("i", $recipe_id); // assuming RecipeId is integer
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result && $result->num_rows > 0) {
        $recipe = $result->fetch_assoc();
    } else {
        $message = "Recipe not found for ID: " . htmlspecialchars($recipe_id);
    }
        $stmt->close();
} else {
    $message = "No RecipeId specified.";
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
        <title> Recipe of <?php echo htmlspecialchars($recipe['RecipeName']); ?> |RR </title>

    <!-- Bootstrap CSS and other stylesheets -->
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome.min.css" rel="stylesheet">
    <link href="css/animate.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">

</head>
<body> 

<h2>Recipe of <?php echo htmlspecialchars($recipe['RecipeName']);?>:</h2>
<div>
    <p><?php echo htmlspecialchars($recipe['description']); ?></p>
    <p>Instructions: </p>
        <p> <?php echo htmlspecialchars($recipe['Instructions']); ?></p>
    <!-- Add more recipe details as needed -->
</div>

<?php if ($message): ?>
    <p><strong><?php echo htmlspecialchars($message); ?></strong></p>
<?php endif; ?>

<!-- recipe er individual pages e save button thakbe-->
<form method="POST" action="saving.php">
    <input type="hidden" name="RecipeId" value="<?php echo htmlspecialchars($recipe_id); ?>">
    <button type="submit">Save Recipe</button>
</form>

<form method="POST" action="history-saver.php">
    <input type="hidden" name="RecipeId" value="<?php echo htmlspecialchars($recipe_id); ?>">
    <button type="submit">Created This</button>
</form>


<footer> <div class="container">
            <p style="text-align:center;">© 2025 RR. Navana Sheikh. ReginaVincenza. All Rights Reserved.</p>
        </div>
    </footer>
    <!-- ====== JS Files ====== -->
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>