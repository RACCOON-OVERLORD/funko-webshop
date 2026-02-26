<?php
require_once 'session.php';
logoutUser();
header('Location: index.php');
exit();
?>