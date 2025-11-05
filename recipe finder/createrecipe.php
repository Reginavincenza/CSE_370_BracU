<?php
// Show any success/error messages here
session_start();
$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Create Recipe</title></head>
<body>
<h1>Create New Recipe</h1>

<?php if ($message) echo "<p>$message</p>"; ?>

<form action="createrecipe.php" method="POST">

  <label>Recipe Name:</label><br>
  <input type="text" name="name" required><br><br>

  <label>Description:</label><br>
  <textarea name="description" required></textarea><br><br>

  <label>Instructions:</label><br>
  <textarea name="Instruction" ></textarea><br><br>

  <label>Prep Time (minutes):</label><br>
  <input type="number" name="prep_time"><br><br>

  <label>Type:</label><br>
  <input type="text" name="Type"><br><br>
  <label>Country:</label><br>
  <input type="text" name="Country"><br><br>

  <label>Ingredients:</label><br>
  <input type="text" name="Ingredients"><br><br>
  <label>image url:</label><br>
  <input type="URL" name="image"><br><br>

<!--
  <h3>Ingredients</h3>  <div class="ingredient">
    Quantity: <input type="text" name="quantity[]" required>
  <div id="ingredients">
    <div class="ingredient">
      Ingredient: <input type="text" name="ingredient[]" required>
    </div>
  </div>

  <button type="button" onclick="addIngredient()">Add Another Ingredient</button><br><br>
-->
  <button type="submit">Submit Recipe</button>
</form>
<!-- JavaScript to dynamically add ingredient fields 
<script>
function addIngredient() {
  const container = document.getElementById('ingredients');
  const div = document.createElement('div');
  div.classList.add('ingredient');
  div.innerHTML = 'Quantity: <input type="text" name="quantity[]" required> Ingredient: <input type="text" name="ingredient[]" required>';
  container.appendChild(div);
}
</script>
-->
<?php 
require 'dbconnect.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['description'], $_POST['prep_time'], $_POST['Type'], $_POST['Country'], $_POST['Ingredients'], $_POST['image'])){
    $name = $_POST['name'];
    $description = $_POST['description'];
    $instruction = $_POST['Instruction'];
    $prep_time = $_POST['prep_time'];
    $type = $_POST['Type'];
    $country = $_POST['Country'];
    $ingredients = $_POST['Ingredients'];
    $image = $_POST['image'];

    $sql = "INSERT INTO recipe (RecipeName, Description, Instructions, Preptime, type, ByCountry, Ingredients) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssisss", $name, $description, $instruction, $prep_time, $type, $country, $ingredients);
    /*
    $selectstmt = $mysqli->prepare("SELECT RecipeId FROM recipe WHERE RecipeName = ?");
    $selectstmt->bind_param("s", $name);
    $selectstmt->execute();
    $result = $selectstmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $recipeId = $row['RecipeId'];

        // Step 2: Insert into by_country
        $insertStmt = $mysqli->prepare("INSERT INTO by_country (Country_recipeid, By_COUNTRY) VALUES (?, ?)");
        $insertStmt->bind_param("is", $recipeId, $country);
        $insertStmt->execute();
    }*/
    if ($stmt->execute()) {
        $_SESSION['message'] = "Inserted Successfully";
        header("Location: landing.php");
        exit();
    } else {
        $_SESSION['message'] = "Insertion Failed";
        header("Location: createrecipe.php");
        exit();
    }
    $stmt->close();}
?>
<footer> 
    <div class="container">
            <p style="text-align:center;">© 2025 RR. Navana Sheikh. ReginaVincenza. All Rights Reserved.</p>
        </div>
    </footer>
</body>
</html>