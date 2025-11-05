<?php
require_once __DIR__ . "/dbconnect.php"; // expects $conn (mysqli)
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn->set_charset("utf8mb4");

// ---------- Read filters ----------
$q       = isset($_GET['q']) ? trim($_GET['q']) : '';
$country = isset($_GET['country']) ? trim($_GET['country']) : '';
$type    = isset($_GET['type']) ? trim($_GET['type']) : '';
$sort    = isset($_GET['sort']) ? $_GET['sort'] : 'best';

// ---------- Build WHERE ----------
$conds   = [];
$params  = [];
$typestr = '';

if ($q !== '') {
    // Search in name/desc/ingredients/instructions
    $conds[] = "(r.RecipeName LIKE ? OR r.Description LIKE ? OR r.Ingredients LIKE ? OR r.Instructions LIKE ?)";
    $like = "%{$q}%";
    $params[] = &$like; $params[] = &$like; $params[] = &$like; $params[] = &$like;
    $typestr .= 'ssss';
}
if ($country !== '') {
    $conds[] = "r.ByCountry = ?";
    $params[] = &$country;
    $typestr .= 's';
}
if ($type !== '') {
    $conds[] = "r.Type = ?";
    $params[] = &$type;
    $typestr .= 's';
}
$whereSql = $conds ? ('WHERE ' . implode(' AND ', $conds)) : '';

// ---------- Sorting ----------
/*
 * best   -> highest AVG rating first; unrated last; tie-breaker: Popularity then newest
 * newest -> newest RecipeId first
 * time   -> tries to sort by minutes if Preptime is a plain number; otherwise pushes unknowns last
 *
 * NOTE: Preptime in your data is mixed (e.g., '70', '3 hours 20 minutes').
 * The CASE below treats numeric Preptime as minutes and sends non-numeric to the bottom.
 */
$orderBy = "ORDER BY (AVG(rv.rating) IS NULL), AVG(rv.rating) DESC, r.Popularity DESC, r.RecipeId DESC";
if ($sort === 'newest') {
    $orderBy = "ORDER BY r.RecipeId DESC";
} elseif ($sort === 'time') {
    $orderBy = "
      ORDER BY
        CASE
          WHEN r.Preptime REGEXP '^[0-9]+$' THEN CAST(r.Preptime AS UNSIGNED)
          ELSE 999999
        END ASC,
        r.RecipeId DESC
    ";
}

// ---------- Dropdown data (countries/types) ----------
$countries = [];
if ($res = $conn->query("SELECT DISTINCT ByCountry FROM recipe WHERE ByCountry <> '' ORDER BY ByCountry")) {
    while ($row = $res->fetch_assoc()) $countries[] = $row['ByCountry'];
    $res->free();
}
$types = [];
if ($res = $conn->query("SELECT DISTINCT Type FROM recipe WHERE Type <> '' ORDER BY Type")) {
    while ($row = $res->fetch_assoc()) $types[] = $row['Type'];
    $res->free();
}

// ---------- Main query ----------
$sql = "
SELECT
  r.RecipeId,
  r.RecipeName,
  r.Type,
  r.ByCountry,
  r.Preptime,
  r.Popularity,
  SUBSTRING(r.Description, 1, 180)  AS DescPreview,
  SUBSTRING(r.Ingredients, 1, 220)  AS IngredientsPreview,
  ROUND(AVG(rv.rating), 2)          AS avg_rating,
  COUNT(rv.rating)                  AS votes
FROM recipe r
LEFT JOIN gives   g  ON g.SrecipeId = r.RecipeId
LEFT JOIN reviews rv ON rv.ReviewId = g.RevId
{$whereSql}
GROUP BY r.RecipeId
{$orderBy}
LIMIT 50
";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    http_response_code(500);
    echo "Prepare failed: " . htmlspecialchars($conn->error);
    exit;
}
if ($typestr !== '') {
    $stmt->bind_param($typestr, ...$params); // refs used above
}
$stmt->execute();
$result  = $stmt->get_result();
$recipes = [];
while ($row = $result->fetch_assoc()) $recipes[] = $row;
$stmt->close();

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Recipe Search</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
  <style>
    body { background:#f8f9fa; }
    .header { background:#fff; border-bottom:2px solid #e5e7eb; padding:14px 0; }
    .brand { color:#7c3aed; font-weight:700; font-size:22px; }
    .brand:hover { color:#6d28d9; text-decoration:none; }
    .navlink { margin-left:18px; font-weight:600; color:#7c3aed; text-decoration:none; }
    .navlink:hover { color:#6d28d9; text-decoration:underline; }
    .page-wrap { max-width:1000px; margin:28px auto; }
    .card { border-radius:14px; box-shadow:0 8px 20px rgba(0,0,0,.05); }
    .pill { display:inline-block; background:#eef2ff; border-radius:999px; padding:2px 10px; margin:0 6px 6px 0; font-size:.82rem; }
    .recipe-title { font-size:1.25rem; font-weight:700; }
  </style>
</head>
<body>
  <div class="header">
    <div class="container d-flex align-items-center justify-content-between">
      <a class="brand" href="index.php">Recipe Recommendation</a>
      <div>
        <a class="navlink" href="index.php">Home</a>
        <a class="navlink" href="search.php">Search</a>
        <a class="navlink" href="review.php">Review</a>
        <a class="navlink" href="Saved.php">Saved</a>
      </div>
    </div>
  </div>

  <div class="page-wrap container">
    <h2 class="mb-4 text-center">🔎 Search Recipes</h2>

    <div class="card p-3 mb-4">
      <form class="row g-3" method="get" action="">
        <div class="col-md-4">
          <label class="form-label">Keyword</label>
          <input type="text" name="q" class="form-control" value="<?= h($q) ?>" placeholder="name, description, ingredient…">
        </div>
        <div class="col-md-3">
          <label class="form-label">Country</label>
          <select name="country" class="form-select">
            <option value="">Any</option>
            <?php foreach ($countries as $c): ?>
              <option value="<?= h($c) ?>" <?= ($c === $country ? 'selected' : '') ?>><?= h($c) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Type</label>
          <select name="type" class="form-select">
            <option value="">Any</option>
            <?php foreach ($types as $t): ?>
              <option value="<?= h($t) ?>" <?= ($t === $type ? 'selected' : '') ?>><?= h($t) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label">Sort</label>
          <select name="sort" class="form-select">
            <option value="best"   <?= $sort === 'best'   ? 'selected' : '' ?>>Best rated</option>
            <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest</option>
            <option value="time"   <?= $sort === 'time'   ? 'selected' : '' ?>>Prep time</option>
          </select>
        </div>
        <div class="col-12 text-end">
          <button class="btn btn-primary">Search</button>
          <a class="btn btn-secondary" href="search.php">Reset</a>
        </div>
      </form>
    </div>

    <?php if (!$recipes): ?>
      <div class="alert alert-warning">No recipes found.</div>
    <?php else: ?>
      <?php foreach ($recipes as $r): ?>
        <div class="card p-3 mb-3">
          <div class="d-flex justify-content-between align-items-start">
            <div class="pe-3">
              <div class="recipe-title"><?= h($r['RecipeName']) ?></div>
              <div class="mt-1">
                <?php if (!empty($r['ByCountry'])): ?><span class="pill"><?= h($r['ByCountry']) ?></span><?php endif; ?>
                <?php if (!empty($r['Type'])): ?><span class="pill"><?= h($r['Type']) ?></span><?php endif; ?>
                <?php if ($r['avg_rating'] !== null): ?>
                  <span class="pill">★ <?= h($r['avg_rating']) ?> (<?= h($r['votes']) ?>)</span>
                <?php else: ?>
                  <span class="pill">No ratings</span>
                <?php endif; ?>
                <span class="pill">⏱ <?= h($r['Preptime']) ?></span>
                <?php if (isset($r['Popularity'])): ?><span class="pill">🔥 <?= (int)$r['Popularity'] ?></span><?php endif; ?>
              </div>
            </div>
            <div class="text-nowrap">
              <!-- Adjust link to your single-recipe page -->
              <a href="recipe_template-page.php?RecipeId=<?= (int)$r['RecipeId'] ?>" class="btn btn-outline-primary">View</a>
            </div>
          </div>
          <?php if (!empty($r['DescPreview'])): ?>
            <div class="mt-2 text-muted"><?= nl2br(h($r['DescPreview'])) ?></div>
          <?php endif; ?>
          <div class="mt-2"><?= nl2br(h($r['IngredientsPreview'])) ?><?= (strlen((string)$r['IngredientsPreview']) >= 220 ? '…' : '') ?></div>
        </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</body>
</html>
