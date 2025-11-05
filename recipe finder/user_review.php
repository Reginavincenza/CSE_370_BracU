<?php
// Location: C:\xampp\htdocs\CSE370\user_review.php
require_once __DIR__ . "/dbconnect.php";
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn->set_charset("utf8mb4");

function back($rid,$m){ header("Location: recipe_page.php?rid=".$rid."&msg=".rawurlencode($m)); exit; }

$rid       = isset($_POST['rid']) ? (int)$_POST['rid'] : 0;
$username  = isset($_POST['username']) ? trim($_POST['username']) : "";
$rating    = isset($_POST['rating']) ? (float)$_POST['rating'] : 0.0;
$feedback  = isset($_POST['feedback']) ? trim($_POST['feedback']) : "";

if ($rid <= 0 || $username === "" || $feedback === "") {
    back($rid, "Missing required fields.");
}
if ($rating < 0) $rating = 0;
if ($rating > 5) $rating = 5;

// percentage = rating out of 5, mapped to 0-100
$percentage = (int)round(($rating / 5) * 100);
if ($percentage > 99) $percentage = 99; // your column is int(2)

try {
    $conn->begin_transaction();

    // 1) Ensure username exists (foreign key in reviews → user.Username)
    $check = $conn->prepare("SELECT 1 FROM user WHERE Username = ?");
    $check->bind_param("s", $username);
    $check->execute();
    if ($check->get_result()->num_rows === 0) {
        $conn->rollback();
        back($rid, "Username not found in users table.");
    }

    // 2) Insert into reviews
    $ins1 = $conn->prepare("INSERT INTO reviews (rating, feedback, percentage, Rusername)
                            VALUES (?, ?, ?, ?)");
    $ins1->bind_param("dsis", $rating, $feedback, $percentage, $username);
    $ins1->execute();
    $revId = $conn->insert_id;

    // 3) Link review to recipe via gives (RevId ↔ SrecipeId)
    $ins2 = $conn->prepare("INSERT INTO gives (RevId, SrecipeId) VALUES (?, ?)");
    $ins2->bind_param("ii", $revId, $rid);
    $ins2->execute();

    $conn->commit();
    back($rid, "Review added successfully!");
} catch (mysqli_sql_exception $e) {
    if ($conn->errno) { $conn->rollback(); }
    back($rid, "Error: ".$e->getMessage());
}
