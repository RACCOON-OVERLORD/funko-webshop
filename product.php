<?php
require_once 'config.php'; // DB
require_once 'session.php'; // Sessies

if (!isset($_GET['id'])) { // ID check
    header('Location: index.php');
    exit();
}

// Product ophalen met mysqli prepared statement
$stmt = $mysqli->prepare("SELECT * FROM producten WHERE id = ?");
$stmt->bind_param("i", $_GET['id']);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$stmt->close();

if (!$product) { // Bestaat niet
    header('Location: index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['naam']) ?> - Funko Pop Shop</title>
    <link rel="stylesheet" href="css.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .product-detail {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-top: 30px;
        }
        
        .product-images {
            position: sticky;
            top: 20px;
        }
        
        .main-image {
            background: linear-gradient(135deg, var(--funko-light-gray) 0%, #D5DBDB 100%);
            border-radius: 25px;
            padding: 30px;
            text-align: center;
            box-shadow: var(--pop-shadow);
            border: 4px solid var(--funko-yellow);
            position: relative;
            overflow: hidden;
        }
        
        .main-image::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.1) 50%, transparent 70%);
            animation: shine 4s infinite;
            pointer-events: none;
        }
        
        .main-image img {
            max-width: 100%;
            max-height: 400px;
            object-fit: contain;
            border-radius: 15px;
            box-shadow: 0 15px 30px rgba(0,0,0,0.2);
            transition: transform 0.5s ease;
            position: relative;
            z-index: 2;
        }
        
        .main-image:hover img {
            transform: scale(1.1) rotate(5deg);
        }
        
        .no-image {
            font-size: 8rem;
            color: var(--funko-gray);
            padding: 100px 0;
        }
        
        .product-info {
            background: var(--funko-white);
            border-radius: 25px;
            padding: 40px;
            box-shadow: var(--pop-shadow);
            border: 4px solid var(--funko-light-gray);
            height: fit-content;
        }
        
        .product-info h1 {
            font-family: 'Fredoka One', cursive;
            font-size: 32px;
            color: var(--funko-black);
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .product-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 25px;
        }
        
        .meta-badge {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-family: 'Fredoka One', cursive;
            text-transform: uppercase;
            font-weight: 400;
        }
        
        .meta-category {
            background: var(--funko-blue);
            color: var(--funko-white);
        }
        
        .meta-serie {
            background: var(--funko-purple);
            color: var(--funko-white);
        }
        
        .meta-number {
            background: var(--funko-yellow);
            color: var(--funko-black);
            font-weight: 700;
        }
        
        .product-pricing {
            background: linear-gradient(135deg, var(--funko-light-gray) 0%, #D5DBDB 100%);
            padding: 25px;
            border-radius: 20px;
            margin: 25px 0;
            text-align: center;
            border: 3px solid var(--funko-yellow);
        }
        
        .product-voorraad {
            margin: 20px 0;
            padding: 15px;
            border-radius: 15px;
            text-align: center;
            font-family: 'Fredoka One', cursive;
            text-transform: uppercase;
            font-size: 14px;
        }
        
        .in-stock {
            background: linear-gradient(135deg, #D5F4E6 0%, #82E0AA 100%);
            color: var(--funko-green);
            border: 2px solid var(--funko-green);
        }
        
        .out-of-stock {
            background: linear-gradient(135deg, #FADBD8 0%, #F1948A 100%);
            color: var(--funko-red);
            border: 2px solid var(--funko-red);
        }
        
        .special-badges {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin: 20px 0;
        }
        
        .special-badge {
            padding: 10px 20px;
            border-radius: 25px;
            font-family: 'Fredoka One', cursive;
            font-size: 12px;
            text-transform: uppercase;
            color: var(--funko-white);
            animation: float 3s ease-in-out infinite;
        }
        
        .exclusive-badge-large {
            background: linear-gradient(135deg, var(--funko-purple) 0%, #7D3C98 100%);
            box-shadow: 0 4px 8px rgba(155, 89, 182, 0.3);
        }
        
        .vaulted-badge-large {
            background: linear-gradient(135deg, var(--funko-black) 0%, #1C2833 100%);
            box-shadow: 0 4px 8px rgba(44, 62, 80, 0.3);
        }
        
        .limited-badge-large {
            background: linear-gradient(135deg, var(--funko-orange) 0%, #D68910 100%);
            box-shadow: 0 4px 8px rgba(243, 156, 18, 0.3);
            animation: glow 2s ease-in-out infinite alternate;
        }
        
        @keyframes glow {
            from { box-shadow: 0 4px 8px rgba(243, 156, 18, 0.3); }
            to { box-shadow: 0 4px 20px rgba(243, 156, 18, 0.7); }
        }
        
        .product-actions {
            margin: 30px 0;
        }
        
        .product-description {
            background: linear-gradient(135deg, var(--funko-light-gray) 0%, #D5DBDB 100%);
            padding: 30px;
            border-radius: 20px;
            margin-top: 30px;
            border: 3px solid var(--funko-blue);
        }
        
        .product-description h3 {
            font-family: 'Fredoka One', cursive;
            color: var(--funko-blue);
            margin-bottom: 15px;
            text-transform: uppercase;
            font-size: 20px;
        }
        
        @media (max-width: 768px) {
            .product-detail {
                grid-template-columns: 1fr;
                gap: 20px;
            }
            
            .product-info {
                padding: 25px;
            }
            
            .product-info h1 {
                font-size: 24px;
            }
            
            .main-image {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <div class="header-container">
            <a href="index.php" class="logo">Funko Pop Shop</a>
            <div class="user-info">
                <?php if (isLoggedIn()): ?>
                    <a href="winkelwagen.php">Winkelwagen (<?= getCartItemCount() ?>)</a>
                    <a href="logout.php">Uitloggen</a>
                <?php else: ?>
                    <a href="inlogen.php">Inloggen</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <div class="container">
        <div class="product-detail">
            <div class="product-images">
                <div class="main-image">
                    <?php if ($product['afbeelding']): ?>
                        <img src="images/<?= htmlspecialchars($product['afbeelding']) ?>" alt="<?= htmlspecialchars($product['naam']) ?>">
                    <?php else: ?>
                        <div class="no-image"></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="product-info">
                <h1><?= htmlspecialchars($product['naam']) ?></h1>
                
                <div class="product-meta">
                    <?php if ($product['funko_nummer']): ?>
                        <span class="meta-badge meta-number"><?= htmlspecialchars($product['funko_nummer']) ?></span>
                    <?php endif; ?>
                    <?php if ($product['categorie']): ?>
                        <span class="meta-badge meta-category"><?= htmlspecialchars($product['categorie']) ?></span>
                    <?php endif; ?>
                    <?php if ($product['serie']): ?>
                        <span class="meta-badge meta-serie"><?= htmlspecialchars($product['serie']) ?></span>
                    <?php endif; ?>
                </div>
                
                <?php if ($product['exclusief'] || $product['vaulted'] || $product['limited_edition']): ?>
                    <div class="special-badges">
                        <?php if ($product['exclusief']): ?>
                            <span class="special-badge exclusive-badge-large">Exclusive</span>
                        <?php endif; ?>
                        <?php if ($product['vaulted']): ?>
                            <span class="special-badge vaulted-badge-large">Vaulted</span>
                        <?php endif; ?>
                        <?php if ($product['limited_edition']): ?>
                            <span class="special-badge limited-badge-large">⭐ Limited Edition</span>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <div class="product-pricing">
                    <?php if ($product['in_aanbieding']): ?>
                        <div style="margin-bottom: 10px;">
                            <span class="original-price" style="font-size: 20px;">€<?= number_format($product['prijs'], 2, ',', '.') ?></span>
                        </div>
                        <div>
                            <span class="sale-price" style="font-size: 32px; font-family: 'Fredoka One', cursive;">€<?= number_format($product['aanbieding_prijs'], 2, ',', '.') ?></span>
                            <div style="margin-top: 10px; background: var(--funko-red); color: white; padding: 8px 16px; border-radius: 15px; display: inline-block; font-family: 'Fredoka One', cursive; font-size: 12px; text-transform: uppercase;">Special Offer!</div>
                        </div>
                    <?php else: ?>
                        <span class="current-price" style="font-size: 32px; font-family: 'Fredoka One', cursive;">€<?= number_format($product['prijs'], 2, ',', '.') ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="product-voorraad">
                    <?php if ($product['voorraad'] > 0): ?>
                        <div class="in-stock">
                            Op voorraad (<?= $product['voorraad'] ?> stuks beschikbaar)
                        </div>
                    <?php else: ?>
                        <div class="out-of-stock">
                            Tijdelijk uitverkocht
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="product-actions">
                    <?php if ($product['voorraad'] > 0): ?>
                        <form method="POST" action="add_to_cart.php">
                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                            <button type="submit" class="btn btn-primary btn-block" style="font-size: 18px; padding: 18px;">
                                Toevoegen aan winkelwagen
                            </button>
                        </form>
                    <?php else: ?>
                        <button class="btn btn-secondary btn-block" disabled style="font-size: 18px; padding: 18px;">
                            Uitverkocht
                        </button>
                    <?php endif; ?>
                </div>
                
                <?php if ($product['beschrijving']): ?>
                    <div class="product-description">
                        <h3>Productbeschrijving</h3>
                        <p><?= nl2br(htmlspecialchars($product['beschrijving'])) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div style="text-align: center; margin: 40px 0;">
            <a href="index.php" class="btn btn-secondary">← Terug naar collectie</a>
        </div>
    </div>

    <script>
        // Add some interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            // Parallax effect on main image
            const mainImage = document.querySelector('.main-image img');
            if (mainImage) {
                document.addEventListener('mousemove', function(e) {
                    const x = (e.clientX / window.innerWidth) * 10;
                    const y = (e.clientY / window.innerHeight) * 10;
                    mainImage.style.transform = `translateX(${x}px) translateY(${y}px) scale(1.02)`;
                });
            }

            // Pulse effect for limited edition
            const limitedBadge = document.querySelector('.limited-badge-large');
            if (limitedBadge) {
                setInterval(() => {
                    limitedBadge.style.transform = 'scale(1.1)';
                    setTimeout(() => {
                        limitedBadge.style.transform = 'scale(1)';
                    }, 200);
                }, 2000);
            }
        });
    </script>
</body>
</html>