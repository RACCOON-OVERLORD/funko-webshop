<?php
require_once 'config.php';
require_once 'session.php';

if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naam = trim($_POST['naam'] ?? '');
    $achternaam = trim($_POST['achternaam'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $wachtwoord = $_POST['wachtwoord'] ?? '';
    $bevestiging = $_POST['wachtwoord_bevestiging'] ?? '';
    
    // Validation
    if (empty($naam)) $errors[] = 'Naam is verplicht';
    if (empty($achternaam)) $errors[] = 'Achternaam is verplicht';
    if (empty($email)) $errors[] = 'Email is verplicht';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Ongeldig emailadres';
    if (empty($wachtwoord)) $errors[] = 'Wachtwoord is verplicht';
    if (strlen($wachtwoord) < 6) $errors[] = 'Wachtwoord moet minimaal 6 tekens lang zijn';
    if ($wachtwoord !== $bevestiging) $errors[] = 'Wachtwoorden komen niet overeen';
    
    // Check email exists
    $check_stmt = $mysqli->prepare("SELECT id FROM gebruikers WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    if ($result->fetch_assoc()) {
        $errors[] = 'Email is al in gebruik';
    }
    $check_stmt->close();
    
    if (empty($errors)) {
        $hashed = password_hash($wachtwoord, PASSWORD_DEFAULT);
        
        $stmt = $mysqli->prepare("INSERT INTO gebruikers (naam, achternaam, email, wachtwoord) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $naam, $achternaam, $email, $hashed);
        $stmt->execute();
        
        $user = [
            'id' => $mysqli->insert_id,
            'naam' => $naam,
            'achternaam' => $achternaam,
            'email' => $email
        ];
        loginUser($user);
        
        $stmt->close();
        header('Location: index.php');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Registreren - Funko Shop</title>
    <link rel="stylesheet" href="css.css">
</head>
<body>
    <header class="header">
        <div class="header-container">
            <a href="index.php" class="logo">Funko Shop</a>
            <div class="user-info">
                <a href="inlogen.php">Inloggen</a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="card" style="max-width: 500px; margin: 50px auto;">
            <div class="card-header">
                <h1 style="text-align: center; margin: 0;">Account aanmaken</h1>
            </div>
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <?php foreach ($errors as $error): ?>
                            <p><?= htmlspecialchars($error) ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label class="form-label" for="naam">Voornaam</label>
                        <input type="text" class="form-control" id="naam" name="naam" value="<?= htmlspecialchars($_POST['naam'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="achternaam">Achternaam</label>
                        <input type="text" class="form-control" id="achternaam" name="achternaam" value="<?= htmlspecialchars($_POST['achternaam'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="wachtwoord">Wachtwoord</label>
                        <input type="password" class="form-control" id="wachtwoord" name="wachtwoord" required>
                        <small>Minimaal 6 tekens</small>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="wachtwoord_bevestiging">Wachtwoord bevestigen</label>
                        <input type="password" class="form-control" id="wachtwoord_bevestiging" name="wachtwoord_bevestiging" required>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-block">Account aanmaken</button>
                </form>
                
                <div style="text-align: center; margin-top: 20px;">
                    <p>Al een account? <a href="inlogen.php">Inloggen</a></p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>