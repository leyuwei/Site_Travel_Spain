<?php
require 'config.php';
require "auth.php";
//$documents
$stmt = $pdo->query('SELECT * FROM documents ORDER BY uploaded_at DESC');
$documents = $stmt->fetchAll();

$stmt = $pdo->query('SELECT * FROM travel_plans ORDER BY created_at DESC');
$plans = $stmt->fetchAll();

$stmt = $pdo->query('SELECT * FROM events ORDER BY event_date ASC');
$events = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Travel Information Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">Travel Portal</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link" href="admin.php">Admin</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>
<div class="container py-4">
    <h1 class="mb-4">Travel Information Portal</h1>

    <section class="mb-4">
        <h2 class="h4 border-bottom pb-2">Uploaded Documents</h2>
        <ul class="list-group">
            <?php foreach ($documents as $doc): ?>
                <li class="list-group-item"><a href="<?php echo htmlspecialchars($doc['filepath']); ?>"><?php echo htmlspecialchars($doc['filename']); ?></a></li>
            <?php endforeach; ?>
        </ul>
    </section>

    <section class="mb-4">
        <h2 class="h4 border-bottom pb-2">Travel Plans / Itinerary</h2>
        <ul class="list-group">
            <?php foreach ($plans as $plan): ?>
                <li class="list-group-item"><?php echo nl2br(htmlspecialchars($plan['content'])); ?></li>
            <?php endforeach; ?>
        </ul>
    </section>

    <section class="mb-4">
        <h2 class="h4 border-bottom pb-2">Upcoming Events</h2>
        <ul class="list-group">
            <?php foreach ($events as $event): ?>
                <li class="list-group-item">
                    <strong><?php echo htmlspecialchars($event['title']); ?></strong>
                    (<?php echo htmlspecialchars($event['event_date']); ?>)<br>
                    <?php echo nl2br(htmlspecialchars($event['description'])); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
