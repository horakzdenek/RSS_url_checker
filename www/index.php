<?php
session_start();
$db = new PDO('sqlite:../db/db.sqlite');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $db->prepare("SELECT * FROM users WHERE username = :u");
    $stmt->execute([':u' => $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        header('Location: dashboard.php');
        exit;
    } else {
        echo "Neplatné přihlášení.";
    }
}
?>
<!doctype html>
<html lang="cs">
<head>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="auth-wrapper">
<form method="post" class="auth-form">
  Uživatelské jméno:<br> <input type="text" id="username" name="username" required><br>
  Heslo: <br><input type="password" name="password"><br>
  <button>Přihlásit se</button>
  <p><a href="register.php">Nemáš účet? Zaregistruj se</a></p>
</form>
</div>


</body>
</html>
 
