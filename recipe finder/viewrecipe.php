<?php
require_once('DBconnect.php'); 

function fetchAllRecipes($conn) {
    $sql = "SELECT RecipeName, description FROM recipe ORDER BY created_at DESC";
    $result = $conn->query($sql);
    $recipes = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $recipes[] = $row;
        }
    }
    return $recipes;
}

function renderRecipeCard($recipe) {
    ?>
    <div class="recipe-card">
        <img src="<?php echo htmlspecialchars($recipe['Image']); ?>" alt="Recipe Image" style="width:200px;height:150px;">
        <h3><?php echo htmlspecialchars($recipe['Title']); ?></h3>
        <p><?php echo htmlspecialchars($recipe['Description']); ?></p>
        <a href="viewrecipe.php?id=<?php echo $recipe['RecipeId']; ?>">View Details</a>
    </div>
    <?php
}

// Usage example:
require_once 'DBconnect.php'; 
$recipes = fetchAllRecipes($conn);
?>
<!DOCTYPE html>
<html>
<head>
    <title>All Recipes</title>
    <style>
        .recipe-card { border:1px solid #ccc; padding:16px; margin:16px; display:inline-block; vertical-align:top; }
    </style>
</head>
<body>
    <h1>All Recipes</h1>
    <div class="recipes-container">
        <?php foreach ($recipes as $recipe) {
            renderRecipeCard($recipe);
        } ?>
    </div>
    <footer> 
    <div class="container">
            <p style="text-align:center;">© 2025 RR. Navana Sheikh. ReginaVincenza. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>