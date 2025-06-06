<?php
require_once 'config.php';
$expected = hash('sha256', $site_password);
if (!isset($_COOKIE['travel_auth']) || !hash_equals($expected, $_COOKIE['travel_auth'])) {
    header('Location: login.php');
    exit;
}
?>
