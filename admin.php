<?php
require 'config.php';
require 'auth.php';

// Handle document upload
if (isset($_POST['upload']) && !empty($_FILES['document']['name'])) {
    $filename = basename($_FILES['document']['name']);
    $target = 'uploads/' . $filename;
    if (move_uploaded_file($_FILES['document']['tmp_name'], $target)) {
        $stmt = $pdo->prepare('INSERT INTO documents (filename, filepath) VALUES (?, ?)');
        $stmt->execute([$filename, $target]);
    }
}

// Handle document deletion
if (isset($_GET['delete_doc'])) {
    $id = (int)$_GET['delete_doc'];
    $stmt = $pdo->prepare('SELECT filepath FROM documents WHERE id = ?');
    $stmt->execute([$id]);
    if ($row = $stmt->fetch()) {
        if (file_exists($row['filepath'])) {
            unlink($row['filepath']);
        }
        $pdo->prepare('DELETE FROM documents WHERE id = ?')->execute([$id]);
    }
    header('Location: admin.php');
    exit;
}

// Add travel plan
if (isset($_POST['add_plan']) && !empty($_POST['plan_content'])) {
    $stmt = $pdo->prepare('INSERT INTO travel_plans (content) VALUES (?)');
    $stmt->execute([$_POST['plan_content']]);
}

// Delete plan
if (isset($_GET['delete_plan'])) {
    $pdo->prepare('DELETE FROM travel_plans WHERE id = ?')->execute([(int)$_GET['delete_plan']]);
    header('Location: admin.php');
    exit;
}

// Add event
if (isset($_POST['add_event']) && !empty($_POST['title']) && !empty($_POST['event_date'])) {
    $stmt = $pdo->prepare('INSERT INTO events (title, event_date, description) VALUES (?, ?, ?)');
    $stmt->execute([$_POST['title'], $_POST['event_date'], $_POST['description']]);
}

// Delete event
if (isset($_GET['delete_event'])) {
    $pdo->prepare('DELETE FROM events WHERE id = ?')->execute([(int)$_GET['delete_event']]);
    header('Location: admin.php');
    exit;
}

$documents = $pdo->query('SELECT * FROM documents ORDER BY uploaded_at DESC')->fetchAll();
$plans     = $pdo->query('SELECT * FROM travel_plans ORDER BY created_at DESC')->fetchAll();
$events    = $pdo->query('SELECT * FROM events ORDER BY event_date ASC')->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Travel Information Portal</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        section { margin-bottom: 40px; }
        h2 { border-bottom: 1px solid #ccc; padding-bottom: 5px; }
        ul { list-style: none; padding: 0; }
        li { margin-bottom: 5px; }
    </style>
</head>
<body>
    <h1>Administration</h1>
    <p><a href="index.php">Back to main page</a> | <a href='logout.php'>Logout</a></p>
<section>
        <h2>Documents</h2>
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="document" required>
            <button type="submit" name="upload">Upload</button>
        </form>
        <ul>
            <?php foreach ($documents as $doc): ?>
                <li>
                    <a href="<?php echo htmlspecialchars($doc['filepath']); ?>"><?php echo htmlspecialchars($doc['filename']); ?></a>
                    <a href="?delete_doc=<?php echo $doc['id']; ?>" onclick="return confirm('Delete this document?');">Delete</a>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <section>
        <h2>Travel Plans / Itinerary</h2>
        <form method="post">
            <textarea name="plan_content" rows="4" cols="50" required></textarea><br>
            <button type="submit" name="add_plan">Add Plan</button>
        </form>
        <ul>
            <?php foreach ($plans as $plan): ?>
                <li>
                    <?php echo nl2br(htmlspecialchars($plan['content'])); ?>
                    <a href="?delete_plan=<?php echo $plan['id']; ?>" onclick="return confirm('Delete this plan?');">Delete</a>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <section>
        <h2>Upcoming Events</h2>
        <form method="post">
            Title: <input type="text" name="title" required><br>
            Date: <input type="date" name="event_date" required><br>
            Description:<br>
            <textarea name="description" rows="4" cols="50"></textarea><br>
            <button type="submit" name="add_event">Add Event</button>
        </form>
        <ul>
            <?php foreach ($events as $event): ?>
                <li>
                    <strong><?php echo htmlspecialchars($event['title']); ?></strong>
                    (<?php echo htmlspecialchars($event['event_date']); ?>)
                    <a href="?delete_event=<?php echo $event['id']; ?>" onclick="return confirm('Delete this event?');">Delete</a><br>
                    <?php echo nl2br(htmlspecialchars($event['description'])); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
</body>
</html>
