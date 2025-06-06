<?php
require 'config.php';
require 'auth.php';

// Handle sorting update via AJAX
if (isset($_POST['update_order']) && isset($_POST['order'])) {
    $table = '';
    switch ($_POST['update_order']) {
        case 'documents':
            $table = 'documents';
            break;
        case 'plans':
            $table = 'travel_plans';
            break;
        case 'events':
            $table = 'events';
            break;
    }
    if ($table) {
        $ids = array_map('intval', $_POST['order']);
        $stmt = $pdo->prepare("UPDATE {$table} SET position = ? WHERE id = ?");
        foreach ($ids as $pos => $id) {
            $stmt->execute([$pos, $id]);
        }
    }
    exit;
}

// Handle document upload
if (isset($_POST['upload']) && !empty($_FILES['document']['name'])) {
    $filename = basename($_FILES['document']['name']);
    $target = 'uploads/' . $filename;
    if (move_uploaded_file($_FILES['document']['tmp_name'], $target)) {
        $pos = (int)$pdo->query('SELECT MAX(position) FROM documents')->fetchColumn() + 1;
        $stmt = $pdo->prepare('INSERT INTO documents (filename, filepath, position) VALUES (?, ?, ?)');
        $stmt->execute([$filename, $target, $pos]);
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

// Update document filename
if (isset($_POST['update_doc'])) {
    $id = (int)$_POST['doc_id'];
    $name = trim($_POST['filename']);
    $pdo->prepare('UPDATE documents SET filename = ? WHERE id = ?')->execute([$name, $id]);
    header('Location: admin.php');
    exit;
}

// Add travel plan
if (isset($_POST['add_plan']) && !empty($_POST['plan_content'])) {
    $pos = (int)$pdo->query('SELECT MAX(position) FROM travel_plans')->fetchColumn() + 1;
    $stmt = $pdo->prepare('INSERT INTO travel_plans (content, position) VALUES (?, ?)');
    $stmt->execute([$_POST['plan_content'], $pos]);
}

// Delete plan
if (isset($_GET['delete_plan'])) {
    $pdo->prepare('DELETE FROM travel_plans WHERE id = ?')->execute([(int)$_GET['delete_plan']]);
    header('Location: admin.php');
    exit;
}

// Update travel plan
if (isset($_POST['update_plan'])) {
    $id = (int)$_POST['plan_id'];
    $content = trim($_POST['plan_content_edit']);
    $pdo->prepare('UPDATE travel_plans SET content = ? WHERE id = ?')->execute([$content, $id]);
    header('Location: admin.php');
    exit;
}

// Add event
if (isset($_POST['add_event']) && !empty($_POST['title']) && !empty($_POST['event_date'])) {
    $pos = (int)$pdo->query('SELECT MAX(position) FROM events')->fetchColumn() + 1;
    $stmt = $pdo->prepare('INSERT INTO events (title, event_date, description, position) VALUES (?, ?, ?, ?)');
    $stmt->execute([$_POST['title'], $_POST['event_date'], $_POST['description'], $pos]);
}

// Delete event
if (isset($_GET['delete_event'])) {
    $pdo->prepare('DELETE FROM events WHERE id = ?')->execute([(int)$_GET['delete_event']]);
    header('Location: admin.php');
    exit;
}

// Update event
if (isset($_POST['update_event'])) {
    $id = (int)$_POST['event_id'];
    $title = trim($_POST['title_edit']);
    $date = $_POST['event_date_edit'];
    $description = trim($_POST['description_edit']);
    $pdo->prepare('UPDATE events SET title = ?, event_date = ?, description = ? WHERE id = ?')
        ->execute([$title, $date, $description, $id]);
    header('Location: admin.php');
    exit;
}

$documents = $pdo->query('SELECT * FROM documents ORDER BY position ASC, uploaded_at DESC')->fetchAll();
$plans     = $pdo->query('SELECT * FROM travel_plans ORDER BY position ASC, created_at DESC')->fetchAll();
$events    = $pdo->query('SELECT * FROM events ORDER BY position ASC, event_date ASC')->fetchAll();
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
        <ul id="docList" class="list-group">
            <?php foreach ($documents as $doc): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center" data-id="<?php echo $doc['id']; ?>">
                    <?php if (isset($_GET['edit_doc']) && $_GET['edit_doc'] == $doc['id']): ?>
                        <form method="post" class="d-flex w-100">
                            <input type="hidden" name="doc_id" value="<?php echo $doc['id']; ?>">
                            <input type="text" name="filename" class="form-control me-2" value="<?php echo htmlspecialchars($doc['filename']); ?>" required>
                            <button class="btn btn-success me-2" type="submit" name="update_doc">Save</button>
                            <a href="admin.php" class="btn btn-secondary">Cancel</a>
                        </form>
                    <?php else: ?>
                        <a href="<?php echo htmlspecialchars($doc['filepath']); ?>"><?php echo htmlspecialchars($doc['filename']); ?></a>
                        <div>
                            <a href="?edit_doc=<?php echo $doc['id']; ?>" class="text-primary me-3">Edit</a>
                            <a href="?delete_doc=<?php echo $doc['id']; ?>" class="text-danger" onclick="return confirm('Delete this document?');">Delete</a>
                        </div>
                    <?php endif; ?>
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
        <ul id="planList" class="list-group">
            <?php foreach ($plans as $plan): ?>
                <li class="list-group-item d-flex justify-content-between align-items-start" data-id="<?php echo $plan['id']; ?>">
                    <?php if (isset($_GET['edit_plan']) && $_GET['edit_plan'] == $plan['id']): ?>
                        <form method="post" class="w-100">
                            <input type="hidden" name="plan_id" value="<?php echo $plan['id']; ?>">
                            <textarea name="plan_content_edit" class="form-control mb-2" rows="4" required><?php echo htmlspecialchars($plan['content']); ?></textarea>
                            <button class="btn btn-success btn-sm" type="submit" name="update_plan">Save</button>
                            <a href="admin.php" class="btn btn-secondary btn-sm ms-2">Cancel</a>
                        </form>
                    <?php else: ?>
                        <span><?php echo nl2br(htmlspecialchars($plan['content'])); ?></span>
                        <div>
                            <a href="?edit_plan=<?php echo $plan['id']; ?>" class="text-primary ms-3">Edit</a>
                            <a href="?delete_plan=<?php echo $plan['id']; ?>" class="text-danger ms-3" onclick="return confirm('Delete this plan?');">Delete</a>
                        </div>
                    <?php endif; ?>
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
        <ul id="eventList" class="list-group">
            <?php foreach ($events as $event): ?>
                <li class="list-group-item d-flex justify-content-between align-items-start" data-id="<?php echo $event['id']; ?>">
                    <?php if (isset($_GET['edit_event']) && $_GET['edit_event'] == $event['id']): ?>
                        <form method="post" class="w-100">
                            <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                            <div class="mb-2">
                                <input type="text" class="form-control" name="title_edit" value="<?php echo htmlspecialchars($event['title']); ?>" required>
                            </div>
                            <div class="mb-2">
                                <input type="date" class="form-control" name="event_date_edit" value="<?php echo htmlspecialchars($event['event_date']); ?>" required>
                            </div>
                            <div class="mb-2">
                                <textarea class="form-control" name="description_edit" rows="4"><?php echo htmlspecialchars($event['description']); ?></textarea>
                            </div>
                            <button type="submit" name="update_event" class="btn btn-success btn-sm">Save</button>
                            <a href="admin.php" class="btn btn-secondary btn-sm ms-2">Cancel</a>
                        </form>
                    <?php else: ?>
                        <div>
                            <strong><?php echo htmlspecialchars($event['title']); ?></strong>
                            (<?php echo htmlspecialchars($event['event_date']); ?>)<br>
                            <?php echo nl2br(htmlspecialchars($event['description'])); ?>
                        </div>
                        <div>
                            <a href="?edit_event=<?php echo $event['id']; ?>" class="text-primary ms-3">Edit</a>
                            <a href="?delete_event=<?php echo $event['id']; ?>" class="text-danger ms-3" onclick="return confirm('Delete this event?');">Delete</a>
                        </div>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.0/Sortable.min.js"></script>
<script>
function makeSortable(el, type) {
    if (!el) return;
    new Sortable(el, {
        animation: 150,
        onEnd: function () {
            const order = Array.from(el.children).map(li => li.dataset.id);
            const params = new URLSearchParams();
            params.append('update_order', type);
            order.forEach(id => params.append('order[]', id));
            fetch('admin.php', {method: 'POST', body: params});
        }
    });
}
makeSortable(document.getElementById('docList'), 'documents');
makeSortable(document.getElementById('planList'), 'plans');
makeSortable(document.getElementById('eventList'), 'events');
</script>
</body>
</html>
