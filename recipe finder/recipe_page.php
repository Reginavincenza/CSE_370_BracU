<?php
// Location: C:\xampp\htdocs\CSE370\recipe_page.php
require_once __DIR__ . "/dbconnect.php";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn->set_charset("utf8mb4");

$rid = isset($_GET['rid']) ? (int)$_GET['rid'] : 0;
$msg = isset($_GET['msg']) ? $_GET['msg'] : "";

function e($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Recipes</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<style>
 body{font-family:system-ui,Arial,sans-serif;max-width:900px;margin:24px auto;padding:0 16px}
 a{color:#0b63ce;text-decoration:none} a:hover{text-decoration:underline}
 .card{border:1px solid #ddd;border-radius:10px;padding:16px;margin:12px 0}
 .muted{color:#666}
 .btn{display:inline-block;padding:8px 14px;border:1px solid #0b63ce;border-radius:8px}
 input,textarea,select{width:100%;padding:10px;margin:6px 0 12px;border:1px solid #ccc;border-radius:8px}
 .ok{background:#e7f7ea;border:1px solid #8cd19d;padding:10px;border-radius:8px}
</style>
</head>
<body>
  <div>
    
    <a href="createrecipe.php" class="btn btn-primary" style="margin: 20px;">Create Recipe</a>
    
    </div>

<?php if($msg): ?>
  <div class="ok"><?= e($msg) ?></div>
<?php endif; ?>

<?php
if ($rid > 0) {
    // ----- Single recipe -----
    $sql = "SELECT RecipeId, RecipeName, Type, Ingredients, Preptime, RuserId
            FROM recipe WHERE RecipeId = ?";
    $st  = $conn->prepare($sql);
    $st->bind_param("i", $rid);
    $st->execute();
    $recipe = $st->get_result()->fetch_assoc();

    if (!$recipe) {
        http_response_code(404);
        echo "<h2>Recipe not found</h2>";
        echo '<p><a href="recipe_page.php">&larr; Back to all recipes</a></p>';
        exit;
    }
    ?>
    <p><a href="recipe_page.php">&larr; Back to all recipes</a></p>

    <div class="card">
      <h1><?= e($recipe['RecipeName']) ?></h1>
      <div class="muted">
        <strong>Type:</strong> <?= e($recipe['Type']) ?> &nbsp; |
        <strong>Prep time:</strong> <?= e($recipe['Preptime']) ?>
      </div>
            <h3>Ingredients</h3>
      <pre style="white-space:pre-wrap;"><?= e($recipe['Ingredients']) ?></pre>
    </div>

    <?php
    // ----- Reviews for this recipe -----
    $sql = "SELECT r.ReviewId, r.Rusername, r.rating, r.percentage, r.feedback
            FROM gives g
            JOIN reviews r ON r.ReviewId = g.RevId
            WHERE g.SrecipeId = ?
            ORDER BY r.ReviewId DESC";
    $st = $conn->prepare($sql);
    $st->bind_param("i", $rid);
    $st->execute();
    $reviews = $st->get_result();
    ?>

    <h2>Reviews</h2>
    <?php if ($reviews->num_rows === 0): ?>
      <p class="muted">No reviews yet—be the first!</p>
    <?php else: ?>
      <?php while($rv = $reviews->fetch_assoc()): ?>
        <div class="card">
          <div><strong><?= e($rv['Rusername']) ?></strong> · Rating: <?= e($rv['rating']) ?>/5 (<?= (int)$rv['percentage'] ?>%)</div>
          <p><?= nl2br(e($rv['feedback'])) ?></p>
        </div>
      <?php endwhile; ?>
    <?php endif; ?>

    <h2>Add your review</h2>
    <form method="post" action="user_review.php">
      <input type="hidden" name="rid" value="<?= (int)$recipe['RecipeId'] ?>">
      <label>Username (must exist in `user`.`Username`)</label>
      <input name="username" required maxlength="20" placeholder="e.g., bluefalcon92">
      <label>Rating (0–5, step 0.5)</label>
      <input name="rating" type="number" step="0.5" min="0" max="5" required>
      <label>Feedback</label>
      <textarea name="feedback" rows="4" required></textarea>
      <button class="btn" type="submit">Submit review</button>
    </form>

<?php
} else {
    // ----- List all recipes -----
    $res = $conn->query("SELECT RecipeId, RecipeName, SUBSTRING(Ingredients,1,160) AS snippet
                         FROM recipe ORDER BY RecipeId DESC LIMIT 100");
    echo "<h1>Latest Recipes</h1>";
    while ($row = $res->fetch_assoc()) {
        echo '<div class="card">';
        echo '<h3><a href="recipe-template-page.php?RecipeId='.(int)$row['RecipeId'].'">'.e($row['RecipeName']).'</a></h3>';
        echo '<p class="muted">'.e($row['snippet']).'...</p>';
        echo '<a class="btn" href="recipe-template-page.php?RecipeId='.(int)$row['RecipeId'].'">Open</a>';
        echo '</div>';
    }
}
?>
</body>
</html>
