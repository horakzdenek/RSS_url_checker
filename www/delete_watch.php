<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo "Nejste přihlášen.";
    exit;
}

if (!isset($_POST['watch_id'])) {
    http_response_code(400);
    echo "Chybí watch_id.";
    exit;
}

$db = new PDO('sqlite:../db/db.sqlite');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $db->prepare("SELECT * FROM watchlist WHERE id = :id AND user_id = :user_id");
$stmt->execute([
    ':id' => $_POST['watch_id'],
    ':user_id' => $_SESSION['user_id']
]);
$watch = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$watch) {
    http_response_code(404);
    echo "Záznam nenalezen nebo nemáte oprávnění.";
    exit;
}

$stmt = $db->prepare("DELETE FROM watchlist WHERE id = :id");
$stmt->execute([':id' => $_POST['watch_id']]);

header("Location: dashboard.php");
exit;
?>
