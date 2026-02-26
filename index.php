<?php
require_once 'config.php';
require_once 'session.php';

$stmt = $mysqli->prepare("SELECT * FROM producten ORDER BY aangemaakt_op DESC");
$stmt->execute();
$result = $stmt->get_result();
$producten = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Funko Pop Shop</title>
    <link rel="stylesheet" href="css.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <header class="header">
        <div class="header-container">
            <a href="index.php" class="logo">Funko Pop Shop</a>  
            <div class="user-info">
                <?php if (isLoggedIn()): ?>
                    <a href="account.php">Account</a>
                    <a href="winkelwagen.php">Winkelwagen (<?= getCartItemCount() ?>)</a>
                    <a href="logout.php">Uitloggen</a>
                <?php else: ?>
                    <a href="inlogen.php">Inloggen</a>
                    <a href="regristreren.php">Registreren</a>
                    <a href="winkelwagen.php">Winkelwagen (<?= getCartItemCount() ?>)</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
                    
    <div class="container">
        <div class="collection-showcase">
            <h2>Nieuwste Funko Pop Collectibles!</h2>
            <p>Ontdek de allerlaatste toevoegingen aan onze collectie!</p>
        </div>
        
        <div class="products-grid">
            <?php foreach ($producten as $product): ?>
                <div class="product-card <?= $product['limited_edition'] ? 'limited-edition' : '' ?>">
                    <?php if ($product['in_aanbieding']): ?>
                        <div class="sale-badge">Sale!</div>
                    <?php endif; ?>
                    
                    <?php if ($product['exclusief']): ?>
                        <div class="exclusive-badge">Exclusive</div>
                    <?php endif; ?>
                    
                    <?php if ($product['vaulted']): ?>
                        <div class="vaulted-badge">Vaulted</div>
                    <?php endif; ?>
                    
                    <div class="product-image">
                        <?php if ($product['afbeelding']): ?>
                            <img src="images/<?= htmlspecialchars($product['afbeelding']) ?>" alt="<?= htmlspecialchars($product['naam']) ?>">
                        <?php else: ?>
                            <div class="no-image"></div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="product-content">
                        <?php if ($product['funko_nummer']): ?>
                            <div class="funko-number"><?= htmlspecialchars($product['funko_nummer']) ?></div>
                        <?php endif; ?>
                        
                        <h3><?= htmlspecialchars($product['naam']) ?></h3>
                        
                        <?php if ($product['serie']): ?>
                            <div class="product-serie"><?= htmlspecialchars($product['serie']) ?></div>
                        <?php endif; ?>
                        
                        <div class="product-price">
                            <?php if ($product['in_aanbieding']): ?>
                                <span class="original-price">€<?= number_format($product['prijs'], 2, ',', '.') ?></span>
                                <span class="sale-price">€<?= number_format($product['aanbieding_prijs'], 2, ',', '.') ?></span>
                            <?php else: ?>
                                <span class="current-price">€<?= number_format($product['prijs'], 2, ',', '.') ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <p class="product-categorie"><?= htmlspecialchars($product['categorie']) ?></p>
                        
                        <div class="product-stock">
                            <?php if ($product['voorraad'] > 0): ?>
                                <span style="color: var(--funko-green); font-weight: 600;">Op voorraad</span>
                            <?php else: ?>
                                <span style="color: var(--funko-red); font-weight: 600;">Uitverkocht</span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="product-actions">
                            <?php if ($product['voorraad'] > 0): ?>
                                <form method="POST" action="add_to_cart.php">
                                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                    <button type="submit" class="btn btn-primary">In Winkelwagen</button>
                                </form>
                            <?php endif; ?>
                            <a href="product.php?id=<?= $product['id'] ?>" class="btn btn-secondary">Details</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        
            <?php if (empty($producten)): ?>
            <div class="card">
                <div class="card-body" style="text-align: center; padding: 50px;">
                    <h2>Collectie wordt bijgewerkt!</h2>
                    <p>Kom binnenkort terug voor nieuwe releases!</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 