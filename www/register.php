<?php
session_start();
$db = new PDO('sqlite:../db/db.sqlite');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if ($username && $password) {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $db->prepare("INSERT INTO users (username, password_hash) VALUES (:u, :p)");
        try {
        $stmt->execute([':u' => $username, ':p' => $hash]);
        echo "Účet vytvořen. <a href='index.php'>Přihlásit se</a>";
        exit;
        } catch (PDOException $e) {
        echo "Chyba při registraci: " . $e->getMessage();
        }

    } else {
        echo "Vyplňte uživatelské jméno i heslo.";
    }
}
?>
<html lang="cs">
<head>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="auth-wrapper">
<form method="post" class="auth-form">
  Uživatelské jméno:<br> <input type="text" id="username" name="username" required><br>
  Heslo: <br><input type="password" name="password"><br>
  <button>Registrovat</button>
</form>
</div>
</body>
</html>
 
