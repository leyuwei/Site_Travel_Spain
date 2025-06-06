<?php
setcookie('travel_auth', '', time() - 3600, '/');
header('Location: login.php');
exit;
?>
