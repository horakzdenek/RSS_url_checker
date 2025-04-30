<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$db = new PDO('sqlite:../db/db.sqlite');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $url = trim($_POST['url']);
    $user_id = $_SESSION['user_id']; // zůstává pro přihlášené uživatele

    if (filter_var($url, FILTER_VALIDATE_URL)) {
        // Ověření, jestli už odkaz není přidán
        $stmt = $db->prepare("SELECT * FROM watchlist WHERE url = :url AND user_id = :user_id");
        $stmt->execute([':url' => $url, ':user_id' => $user_id]);
        $existingLink = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$existingLink) {
            // Získáme HTML obsah stránky
            $content = file_get_contents($url);
            // Vytvoření hashe obsahu
            $hash = hash('sha256', $content);

            // Nastavení aktuálního času pro last_checked
            $current_time = date('Y-m-d H:i:s');

            $stmt = $db->prepare("INSERT INTO watchlist (user_id, url, last_hash, last_checked) VALUES (:user_id, :url, :hash, :last_checked)");
            $stmt->execute([':user_id' => $user_id, ':url' => $url, ':hash' => $hash, ':last_checked' => $current_time]);

            // Přesměrování na dashboard po úspěšném přidání
            header('Location: dashboard.php');
            exit; // Nezapomeň na exit po přesměrování
            //echo "Odkaz byl přidán. <a href='dashboard.php'>Přejít na dashboard</a>";
        } else {
            echo "Tento odkaz již sledujete.";
        }
    } else {
        echo "Neplatná URL adresa.";
    }
}
?>
<!doctype html>
<html lang="cs">
<head>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<form method="post">
  Odkaz k sledování: <input name="url" type="text"><br>
  <button>Přidat odkaz</button>
</form>

<p><a href="dashboard.php">Zpět na dashboard</a></p>

</body>
</html>
