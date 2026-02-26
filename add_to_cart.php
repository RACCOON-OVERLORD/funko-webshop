<?php
require_once 'session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    // validatie
    $productId = (int)$_POST['product_id'];
    if ($productId > 0) {
        addToCart($productId);
    }
}

header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php'));
exit();