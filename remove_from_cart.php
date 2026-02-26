<?php
require_once 'session.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Leeg alles
    if (isset($_POST['clear_cart'])) {
        clearCart();
    }
    // Anders
    elseif (isset($_POST['product_id'])) {
        $productId = (int)$_POST['product_id'];
        $action = $_POST['action'] ?? 'remove';
        
        if ($productId > 0) {
            $cart = getCart();
            
            if (isset($cart[$productId])) {
                switch ($action) {
                    case 'remove_one':
                        // 1 minder
                        if ($cart[$productId] > 1) {
                            $_SESSION['winkelwagen'][$productId]--;
                        } else {
                            unset($_SESSION['winkelwagen'][$productId]);
                        }
                        break;
                        
                    case 'remove_all':
                    case 'remove':
                    default:
                        // Alles weg
                        unset($_SESSION['winkelwagen'][$productId]);
                        break;
                }
            }
        }
    }
}

header('Location: winkelwagen.php');
exit();
?>