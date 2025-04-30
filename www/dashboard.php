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

echo '
<!doctype html>
<html lang="cs">
<head>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>';
echo "<h2>Sledované odkazy</h2>";

// add new link
echo "<p><a href='add_watch.php' class='add-button'>Přidat nový odkaz</a></p>";




echo '<div class="watchlist-container">'; // vystředí obsah vizuálně
// eigen RSS link
echo "<p><strong>📡 Tvůj RSS kanál:</strong> <a href='rss.php?id=" . urlencode($user_id) . "' target='_blank'>rss.php?id=" . htmlspecialchars($user_id) . "</a></p>";
if ($watchlist) {
    echo "<ul>";
    foreach ($watchlist as $item) {
        echo "<li>";
        echo "<a href='" . htmlspecialchars($item['url']) . "' target='_blank'>" . htmlspecialchars($item['url']) . "</a> ";
        echo "<form method='POST' action='delete_watch.php' style='display:inline;' onsubmit=\"return confirm('Opravdu chcete smazat tento záznam?');\">";
        echo "<input type='hidden' name='watch_id' value='" . htmlspecialchars($item['id']) . "'>";
        echo "<button type='submit'>🗑️ Smazat</button>";
        echo "</form>";
        echo "</li>";
    }
    echo "</ul>";
} else {
    echo "Nemáte žádné sledované odkazy.";
}

echo '</div>'; // konec vystředění

// logout button
echo '<div class="vpravo">
    <a href="logout.php" class="logout-button">Odhlásit se</a>
</div>
';





//echo "<p><a href='logout.php' class='logout-button'>Odhlásit se</a></p>";

echo '</body></html>';
?>
