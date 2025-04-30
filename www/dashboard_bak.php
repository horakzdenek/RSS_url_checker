<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$db = new PDO('sqlite:../db/db.sqlite');
$user_id = $_SESSION['user_id'];

$stmt = $db->prepare("SELECT * FROM watchlist WHERE user_id = :user_id");
$stmt->execute([':user_id' => $user_id]);
$watchlist = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "<h2>Sledované odkazy</h2>";

if ($watchlist) {
    echo "<ul>";
    foreach ($watchlist as $item) {
        echo "<li><a href='" . htmlspecialchars($item['url']) . "' target='_blank'>" . htmlspecialchars($item['url']) . "</a></li>";
    }
    echo "</ul>";
} else {
    echo "Nemáte žádné sledované odkazy.";
}

echo "<p><a href='add_watch.php'>Přidat nový odkaz</a></p>";
?>
