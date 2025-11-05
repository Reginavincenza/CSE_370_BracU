<?php 
// connecting to database
include('DBconnect.php');
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
        <title> Recipes | RR </title>

    <!-- Bootstrap CSS and other stylesheets -->
    <link href="bootstrap.min.css" rel="stylesheet">
    <link href="font-awesome.min.css" rel="stylesheet">
    <link href="css/animate.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet">
    <!-- last entry kora recipe card er moto shobar upore dekhabe-->

</head>
<body>
    <div>
     <!-- create recipe r option kono ek jaygay thakbe-->
    <a href="createrecipe.php" class="btn btn-primary" style="margin: 20px;">Create Recipe</a>
    
    </div>
    <div class="container">
        <div class="row">
            <?php
            // Fetching recipes from the database
            $sql = "SELECT RecipeId, RecipeName, Description FROM recipe ORDER BY RecipeId DESC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                // Output data of each row
                while($row = $result->fetch_assoc()) {
                    echo '<div class="col-md-4" style="margin-bottom: 20px;">';
                    echo '<div class="card">';
                    echo '<div class="card-body">';
                    echo '<h5 class="card-title">' . htmlspecialchars($row['RecipeName']) . '</h5>';
                    echo '<p class="card-text">' . htmlspecialchars($row['Description']) . '</p>';
                    echo '<a href="recipe-template-page.php?RecipeId=' . $row['RecipeId'] . '" class="btn btn-primary">View Recipe</a>';
                    echo '</div>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "No recipes found.";
            }
            $conn->close();
            ?>
        </div>

    <footer> 
    <div class="container">
            <p style="text-align:center;">© 2025 RR. Navana Sheikh. ReginaVincenza. All Rights Reserved.</p>
        </div>
    </footer>
    <!-- ====== JS Files ====== -->
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>