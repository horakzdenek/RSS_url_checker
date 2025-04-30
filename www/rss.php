<?php
//header('Content-Type: application/rss+xml; charset=utf-8');
header('Content-Type: text/xml; charset=UTF-8');

$user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($user_id <= 0) {
    die('Neplatné ID uživatele.');
}

$db = new PDO('sqlite:../db/db.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Nejprve zkontroluj změny (stejně jako v check_changes.php)
$stmt = $db->prepare("SELECT * FROM watchlist WHERE user_id = :user_id");
$stmt->execute([':user_id' => $user_id]);
$watchlist = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($watchlist as $item) {
    $html = @file_get_contents($item['url']);
    if ($html === false) continue;

    $current_hash = hash('sha256', $html);
    if ($current_hash !== $item['last_hash']) {
        // Loguj změnu
        $stmtInsert = $db->prepare("INSERT INTO changes (watch_id, timestamp) VALUES (:watch_id, :timestamp)");
        $stmtInsert->execute([
            ':watch_id' => $item['id'],
            ':timestamp' => date('Y-m-d H:i:s')
        ]);

        // Aktualizuj záznam
        $stmtUpdate = $db->prepare("UPDATE watchlist SET last_hash = :hash, last_checked = :checked WHERE id = :id");
        $stmtUpdate->execute([
            ':hash' => $current_hash,
            ':checked' => date('Y-m-d H:i:s'),
            ':id' => $item['id']
        ]);
    }
}

// Načti změny pro uživatele
$stmt = $db->prepare("
    SELECT changes.watch_id, changes.timestamp, watchlist.url 
    FROM changes 
    JOIN watchlist ON changes.watch_id = watchlist.id 
    WHERE watchlist.user_id = :user_id 
    ORDER BY changes.timestamp DESC
");
$stmt->execute([':user_id' => $user_id]);
$changes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Výstup XML
echo "<?xml version=\"1.0\" encoding=\"UTF-8\" ?>\n";
echo '<?xml-stylesheet type="text/xsl" href="rss-style.xsl"?>';
?>
<rss version="2.0">
<channel>
    <title>Změny sledovaných stránek uživatele <?= $user_id ?></title>
    <link>http://<?= $_SERVER['HTTP_HOST'] ?>/</link>
    <description>RSS kanál změn webů pro uživatele <?= $user_id ?></description>
    <language>cs</language>

<?php foreach ($changes as $change): ?>
    <item>
        <title>Změna na <?= htmlspecialchars($change['url']) ?></title>
        <link><?= htmlspecialchars($change['url']) ?></link>
        <guid isPermaLink="false">change-<?= $change['watch_id'] ?>-<?= strtotime($change['timestamp']) ?></guid>
        <pubDate><?= date(DATE_RSS, strtotime($change['timestamp'])) ?></pubDate>
        <description>Změna detekována na <?= htmlspecialchars($change['url']) ?> v <?= $change['timestamp'] ?></description>
    </item>
<?php endforeach; ?>

</channel>
</rss>
