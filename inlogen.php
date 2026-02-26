<?php
require_once 'config.php';
require_once 'session.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naam = trim($_POST['naam'] ?? '');
    $wachtwoord = $_POST['wachtwoord'] ?? '';

    $stmt = $mysqli->prepare("SELECT id, naam, achternaam, email, wachtwoord FROM gebruikers WHERE naam = ? OR email = ?");
    $stmt->bind_param("ss", $naam, $naam);
    $stmt->execute();
    $result = $stmt->get_result();
    $gebruiker = $result->fetch_assoc();
    $stmt->close();

    if ($gebruiker && password_verify($wachtwoord, $gebruiker['wachtwoord'])) {
        loginUser($gebruiker);
        $redirect = $_SESSION['redirect_after_login'] ?? 'index.php';
        unset($_SESSION['redirect_after_login']);
        header('Location: ' . $redirect);
        exit();
    } else {
        $error = "Ongeldige inloggegevens.";
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Inloggen - Funko Shop</title>
    <link rel="stylesheet" href="css.css">
</head>
<body>
    <header class="header">
        <div class="header-container">
            <a href="index.php" class="logo">Funko Shop</a>
            <div class="user-info">
                <a href="regristreren.php">Registreren</a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="card" style="max-width: 500px; margin: 50px auto;">
            <div class="card-header">
                <h1 style="text-align: center; margin: 0;">Inloggen</h1>
            </div>
            <div class="card-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label class="form-label" for="naam">Gebruikersnaam of email</label>
                        <input type="text" class="form-control" id="naam" name="naam" value="<?= htmlspecialchars($_POST['naam'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="wachtwoord">Wachtwoord</label>
                        <input type="password" class="form-control" id="wachtwoord" name="wachtwoord" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">Inloggen</button>
                </form>
                
                <div style="text-align: center; margin-top: 20px;">
                    <p>Nog geen account? <a href="regristreren.php">Registreer hier</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>