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
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin - Travel Information Portal</title>
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
                <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>
<div class="container py-4">
    <h1 class="mb-4">Administration</h1>
<section class="mb-4">
        <h2 class="h4 border-bottom pb-2">Documents</h2>
        <form method="post" enctype="multipart/form-data" class="mb-3">
            <div class="input-group">
                <input type="file" class="form-control" name="document" required>
                <button type="submit" class="btn btn-primary" name="upload">Upload</button>
            </div>
        </form>
        <ul class="list-group">
            <?php foreach ($documents as $doc): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <a href="<?php echo htmlspecialchars($doc['filepath']); ?>"><?php echo htmlspecialchars($doc['filename']); ?></a>
                    <a href="?delete_doc=<?php echo $doc['id']; ?>" class="text-danger" onclick="return confirm('Delete this document?');">Delete</a>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <section class="mb-4">
        <h2 class="h4 border-bottom pb-2">Travel Plans / Itinerary</h2>
        <form method="post" class="mb-3">
            <div class="mb-2">
                <textarea name="plan_content" class="form-control" rows="4" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary" name="add_plan">Add Plan</button>
        </form>
        <ul class="list-group">
            <?php foreach ($plans as $plan): ?>
                <li class="list-group-item d-flex justify-content-between align-items-start">
                    <span><?php echo nl2br(htmlspecialchars($plan['content'])); ?></span>
                    <a href="?delete_plan=<?php echo $plan['id']; ?>" class="text-danger ms-3" onclick="return confirm('Delete this plan?');">Delete</a>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>

    <section class="mb-4">
        <h2 class="h4 border-bottom pb-2">Upcoming Events</h2>
        <form method="post" class="mb-3">
            <div class="mb-2">
                <input type="text" class="form-control" name="title" placeholder="Title" required>
            </div>
            <div class="mb-2">
                <input type="date" class="form-control" name="event_date" required>
            </div>
            <div class="mb-2">
                <textarea name="description" class="form-control" rows="4" placeholder="Description"></textarea>
            </div>
            <button type="submit" class="btn btn-primary" name="add_event">Add Event</button>
        </form>
        <ul class="list-group">
            <?php foreach ($events as $event): ?>
                <li class="list-group-item d-flex justify-content-between align-items-start">
                    <div>
                        <strong><?php echo htmlspecialchars($event['title']); ?></strong>
                        (<?php echo htmlspecialchars($event['event_date']); ?>)<br>
                        <?php echo nl2br(htmlspecialchars($event['description'])); ?>
                    </div>
                    <a href="?delete_event=<?php echo $event['id']; ?>" class="text-danger ms-3" onclick="return confirm('Delete this event?');">Delete</a>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
