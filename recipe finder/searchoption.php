<?php
// Database connection
$mysqli = new mysqli("localhost", "root", "", "recipe_recommendation");
if ($mysqli->connect_errno) {
    die("Failed to connect: " . $mysqli->connect_error);
}

// Search by recipe name
function searchByName($name) {
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT RecipeId FROM recipe WHERE RecipeName LIKE ?");
    $search = "%$name%";
    $stmt->bind_param("s", $search);
    $stmt->execute();
    return $stmt->get_result();
}

// Search by ingredient
function searchByIngredient($ingredient) {
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT RecipeId FROM recipe WHERE Ingredients LIKE ?");
    $search = "%$ingredient%";
    $stmt->bind_param("s", $search);
    $stmt->execute();
    return $stmt->get_result();
}

// Search by cuisine type
function searchByCuisine($cuisine) {
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT RecipeId FROM recipe WHERE by_country = ?");
    $stmt->bind_param("s", $cuisine);
    $stmt->execute();
    return $stmt->get_result();
}

// Search by cooking time (less than or equal to)
function searchByCookingTime($maxTime) {
    global $mysqli;
    $stmt = $mysqli->prepare("SELECT RecipeId FROM recipe WHERE Preptime <= ?");
    $stmt->bind_param("i", $maxTime);
    $stmt->execute();
    return $stmt->get_result();
}

// Example usage
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $results = [];
    if (!empty($_POST['RecipeName'])) {
        $results = searchByName($_POST['RecipeName']);
    } elseif (!empty($_POST['Ingredients'])) {
        $results = searchByIngredient($_POST['ingredient']);
    } elseif (!empty($_POST['cuisine'])) {
        $results = searchByCuisine($_POST['cuisine']);
    } elseif (!empty($_POST['max_time'])) {
        $results = searchByCookingTime((int)$_POST['max_time']);
    }

    // Display results
    if ($results && $results->num_rows > 0) {
        $row = $results->fetch_assoc();
        $sqlselect = $mysqli->prepare("SELECT RecipeId FROM recipe WHERE RecipeId = ?");
        header("Location: recipe-template-page.php?RecipeId=" . $row['RecipeId']); 
        exit;
    }
    } else {
        echo "No recipes found.";
    }

?>

<!-- Simple search form -->
<form method="post">
    <input type="text" name="name" placeholder="Search by recipe name">
    <input type="text" name="ingredient" placeholder="Search by ingredient">
    <input type="text" name="cuisine" placeholder="Search by cuisine">
    <input type="number" name="max_time" placeholder="Max cooking time (minutes)">
    <button type="submit">Search</button>
</form>

<?php
// Display search results
if ($results instanceof mysqli_result) {
    if ($results->num_rows > 0) {
        while ($row = $results->fetch_assoc()) {
            echo "<div><strong>" . htmlspecialchars($row['RecipeName']) . "</strong> - " .
                 htmlspecialchars($row['by_country']) . " - " .
                 htmlspecialchars($row['Preptime']) . " mins</div>";
        }
    } else {
        echo "No recipes found.";
    }
}
?>
