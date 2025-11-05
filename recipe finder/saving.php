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
// Handle save recipe form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['RecipeId'])) {
    $recipe_id = intval($_POST['RecipeId']);

    if ($recipe_id <= 0) {
        $message = "Invalid recipe ID.";
    } else {
        // Check if recipe is already saved
        $sql_check = "SELECT Sbookmarkid FROM saves WHERE Suserid = ? AND Srecipeid = ?";
        $stmt = $conn->prepare($sql_check);
        $stmt->bind_param("ii", $user_id, $recipe_id);
        $stmt->execute();
        $stmt->store_result();

$sql_select_recipename = "SELECT RecipeName FROM recipe WHERE RecipeId = $recipe_id"; 

        // Insert into the bookmark table
$sql_bookmark = "INSERT INTO bookmark (Bookmarkname) VALUES (?)";
$stmt_bookmark = $conn->prepare($sql_bookmark);
$stmt_bookmark->bind_param("s", $recipe_id);
$stmt_bookmark->execute();

// Get the auto-incremented BookmarkId from the bookmark table
$bookmarkId = $conn->insert_id; // This retrieves the last inserted ID 
        if ($stmt->num_rows === 0) { // Not already saved, proceed to save
        // Now insert into saves using the new BookmarkId
        $sql_saves = "INSERT INTO saves (Suserid, Srecipeid, SbookmarkId, saved_date) VALUES (?, ?, ?, NOW())";
        $stmt_saves = $conn->prepare($sql_saves);
        $stmt_saves->bind_param("iii", $user_id, $recipe_id, $bookmarkId);
        $stmt_saves->execute();

            if ($stmt_insert->execute()) {
                // Update recipe popularity
                $sql_update = "UPDATE recipe SET Popularity = Popularity + 1 WHERE RecipeId = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("i", $recipe_id);
                $stmt_update->execute();
                $stmt_update->close();

                $message = "Recipe saved successfully!";
            } else {
                $message = "Error saving recipe.";
            }
            $stmt_insert->close();
        } else {
            $message = "You have already saved this recipe.";
        }
        $stmt->close();
    }
}