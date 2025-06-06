<?php
require 'config.php';
$hash = hash('sha256', $site_password);
if (isset($_COOKIE['travel_auth']) && hash_equals($hash, $_COOKIE['travel_auth'])) {
    header('Location: index.php');
    exit;
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    if (hash_equals($site_password, $password)) {
        setcookie('travel_auth', $hash, time() + 30*24*60*60, '/');
        header('Location: index.php');
        exit;
    } else {
        $error = 'Invalid password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .error { color: red; }
    </style>
</head>
<body>
    <h1>Login</h1>
    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form method="post">
        Password: <input type="password" name="password" required>
        <button type="submit">Login</button>
    </form>
</body>
</html>
