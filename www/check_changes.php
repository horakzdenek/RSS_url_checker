<?php
// Připojení k databázi
$db = new PDO('sqlite:../db/db.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// Načti celý watchlist
$stmt = $db->prepare("SELECT * FROM watchlist");
$stmt->execute();
$watchlist = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($watchlist as $item) {
    echo "🔎 Kontroluji: {$item['url']}\n";

    // Pokus o stažení obsahu stránky
    $html = @file_get_contents($item['url']);
    if ($html === false) {
        echo "⚠️  Nelze stáhnout obsah URL: {$item['url']}\n\n";
        continue;
    }

    // Vypočti aktuální hash stránky
    $current_hash = hash('sha256', $html);
    echo "🔒 Starý hash: {$item['last_hash']}\n";
    echo "🧮 Nový hash:  {$current_hash}\n";

    // Porovnej hashe
    if ($current_hash !== $item['last_hash']) {
        echo "✅ Změna detekována. Ukládám nový hash a loguji změnu...\n";

        // Zaznamenej změnu do tabulky `changes` (jen ID sledovaného odkazu a timestamp)
        $stmt = $db->prepare("INSERT INTO changes (watch_id, timestamp) VALUES (:watch_id, :timestamp)");
        $stmt->execute([
            ':watch_id' => $item['id'],
            ':timestamp' => date('Y-m-d H:i:s')
        ]);

        // Aktualizuj hash a čas v `watchlist`
        $stmt = $db->prepare("UPDATE watchlist SET last_hash = :hash, last_checked = :checked WHERE id = :id");
        $stmt->execute([
            ':hash' => $current_hash,
            ':checked' => date('Y-m-d H:i:s'),
            ':id' => $item['id']
        ]);
    } else {
        echo "❌ Žádná změna.\n";
    }

    echo str_repeat("-", 40) . "\n\n";
}
?>
