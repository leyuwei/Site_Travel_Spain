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
    <title>Travel Information Portal</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        section { margin-bottom: 40px; }
        h2 { border-bottom: 1px solid #ccc; padding-bottom: 5px; }
        ul { list-style: none; padding: 0; }
        li { margin-bottom: 5px; }
    </style>
</head>
<body>
    <h1>Travel Information Portal</h1>
    <p><a href="admin.php">Administration</a> | <a href="logout.php">Logout</a></p>

    <section>
        <h2>Uploaded Documents</h2>
        <ul>
            <?php foreach ($documents as $doc): ?>
                <li><a href="<?php echo htmlspecialchars($doc['filepath']); ?>"><?php echo htmlspecialchars($doc['filename']); ?></a></li>
            <?php endforeach; ?>
        </ul>
    </section>

    <section>
        <h2>Travel Plans / Itinerary</h2>
        <ul>
            <?php foreach ($plans as $plan): ?>
                <li><?php echo nl2br(htmlspecialchars($plan['content'])); ?></li>
            <?php endforeach; ?>
        </ul>
    </section>

    <section>
        <h2>Upcoming Events</h2>
        <ul>
            <?php foreach ($events as $event): ?>
                <li>
                    <strong><?php echo htmlspecialchars($event['title']); ?></strong>
                    (<?php echo htmlspecialchars($event['event_date']); ?>)<br>
                    <?php echo nl2br(htmlspecialchars($event['description'])); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
</body>
</html>
