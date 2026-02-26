<?php
require_once 'config.php';
require_once 'session.php';

if (!isLoggedIn()) {
    header('Location: inlogen.php');
    exit();
}

$cart = getCart();
if (empty($cart)) {
    header('Location: winkelwagen.php');
    exit();
}

// Voorraad bijwerken met prepared statement
$update_stmt = $mysqli->prepare("UPDATE producten SET voorraad = voorraad - ? WHERE id = ?");

foreach ($cart as $productId => $aantal) {
    $update_stmt->bind_param("ii", $aantal, $productId);
    $update_stmt->execute();
}

$update_stmt->close();

// Leeg wagen
clearCart();

?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Bestelling voltooid - LEGO Shop</title>
    <link rel="stylesheet" href="css.css">
</head>
<body>
    <header class="header">
        <div class="header-container">
            <a href="index.php" class="logo">LEGO Shop</a>
            <div class="user-info">
                <a href="winkelwagen.php">Winkelwagen (0)</a>
                <a href="logout.php">Uitloggen</a>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="card">
            <h1>Bedankt voor uw bestelling!</h1>
            <p>Uw bestelling is succesvol verwerkt.</p>
            <a href="index.php" class="btn btn-primary">Terug naar de homepage</a>
        </div>
    </div>
</body>
</html>