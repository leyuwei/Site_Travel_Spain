<?php
require 'config.php';
require "auth.php";
//$documents
$stmt = $pdo->query('SELECT * FROM documents ORDER BY position ASC, uploaded_at DESC');
$documents = $stmt->fetchAll();

$stmt = $pdo->query('SELECT * FROM travel_plans ORDER BY position ASC, created_at DESC');
$plans = $stmt->fetchAll();

$stmt = $pdo->query('SELECT * FROM events ORDER BY position ASC, event_date ASC');
$events = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Travel Information Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .past-item { color: #6c757d; }
    </style>
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
    <div id="international-clock" class="mb-4">
        <strong>Beijing:</strong> <span id="time-beijing"></span>
        <strong class="ms-3">Spain:</strong> <span id="time-spain"></span>
    </div>

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
                <li class="list-group-item travel-plan-item"><?php echo nl2br(htmlspecialchars($plan['content'])); ?></li>
            <?php endforeach; ?>
        </ul>
    </section>

    <section class="mb-4">
        <h2 class="h4 border-bottom pb-2">Upcoming Events</h2>
        <ul class="list-group">
            <?php foreach ($events as $event): ?>
                <li class="list-group-item event-item" data-date="<?php echo htmlspecialchars($event['event_date']); ?>">
                    <strong><?php echo htmlspecialchars($event['title']); ?></strong>
                    (<?php echo htmlspecialchars($event['event_date']); ?>)<br>
                    <?php echo nl2br(htmlspecialchars($event['description'])); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function updateClocks() {
    const now = new Date();
    document.getElementById('time-beijing').textContent = now.toLocaleTimeString('en-GB', {timeZone: 'Asia/Shanghai'});
    document.getElementById('time-spain').textContent = now.toLocaleTimeString('en-GB', {timeZone: 'Europe/Madrid'});
}

function greyOutPastItems() {
    const now = new Date();
    document.querySelectorAll('.event-item').forEach(li => {
        const dateStr = li.dataset.date;
        if (dateStr) {
            const dt = new Date(dateStr + 'T23:59:59');
            if (dt < now) {
                li.classList.add('past-item');
            }
        }
    });
    document.querySelectorAll('.travel-plan-item').forEach(li => {
        const text = li.textContent;
        const m = text.match(/(\d{4}-\d{2}-\d{2})(?:\s+(\d{2}:\d{2}))/);
        if (m) {
            let dtStr = m[1];
            if (m[2]) dtStr += 'T' + m[2];
            const dt = new Date(dtStr);
            if (!isNaN(dt) && dt < now) {
                li.classList.add('past-item');
            }
        } else {
            const dateOnly = text.match(/\d{4}-\d{2}-\d{2}/);
            if (dateOnly) {
                const dt = new Date(dateOnly[0]);
                if (dt < now) li.classList.add('past-item');
            }
        }
    });
}

document.addEventListener('DOMContentLoaded', () => {
    updateClocks();
    greyOutPastItems();
    setInterval(updateClocks, 1000);
});
</script>
</body>
</html>
