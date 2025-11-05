<?php
session_start(); // Start the session to access session variables

// Check if user is logged in
if (!isset($_SESSION['UserId'])) {
    // User not logged in, show login form and exit
    include('login.php');
    exit;
}

include('DBconnect.php'); // Include DB connection once here

$user_id = $_SESSION['UserId'];
$message = '';
// Handle recipe form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['RecipeId'])) {
    $recipe_id = intval($_POST['RecipeId']);

    if ($recipe_id <= 0) {
        $message = "Invalid recipe ID.";
    } else {
        // Check if recipe is already made before
        $sql_check = "SELECT h.HSnum FROM has_history h JOIN cooking_history c ON h.HSnum = c.SerialNumber WHERE CHuserid = ? AND HrecipeID = ?";
        $stmt = $conn->prepare($sql_check);
        $stmt->bind_param("ii", $user_id, $recipe_id);
        $stmt->execute();
        $stmt->store_result();

$sql_select_recipename = "SELECT RecipeName FROM recipe WHERE RecipeId = $recipe_id"; 

        // Insert into the bookmark table
$sql_history = "INSERT INTO has_history (HrecipeID) VALUES (?)";
$stmt_history = $conn->prepare($sql_history);
$stmt_history->bind_param("s", $recipe_id);
$stmt_history->execute();

// Get the auto-incremented BookmarkId from the bookmark table
$SerialNumber = $conn->insert_id; // This retrieves the last inserted ID 
        if ($stmt->num_rows === 0) { // Not already saved, proceed to save
        $sql_saves = "INSERT INTO Cooking_history (CHuserid, date) VALUES (?, NOW())";
        $stmt_saves = $conn->prepare($sql_saves);
        $stmt_saves->bind_param("i", $user_id);
        $stmt_saves->execute();



            if ($stmt_history->execute()) {

                $message = "You have made this Recipe successfully!";
            } else {
                $message = "Error!";
            }
            $stmt_history->close();
        } else {
            $message = "You have already made this recipe.";
        }
        $stmt->close();
    }
}