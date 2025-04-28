<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

$id = (int) $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ?");
$stmt->execute([$id]);
$client = $stmt->fetch();

if (!$client) {
    die('Client not found.');
}

if (isset($_POST['name'], $_POST['email'], $_POST['service'])) {
    $filename = $client['filename'];

    if (isset($_FILES['file']) && $_FILES['file']['error'] == 0) {
        if ($filename && file_exists('uploads/' . $filename)) {
            unlink('uploads/' . $filename);
        }

        $filename = uniqid() . '_' . basename($_FILES['file']['name']);
        move_uploaded_file($_FILES['file']['tmp_name'], 'uploads/' . $filename);
    }

    $stmt = $pdo->prepare("UPDATE clients SET name = ?, email = ?, service = ?, filename = ? WHERE id = ?");
    $stmt->execute([$_POST['name'], $_POST['email'], $_POST['service'], $filename, $id]);

    header('Location: dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Client</title>
</head>
<body>
    <h2>Edit Client</h2>
    <form method="POST" enctype="multipart/form-data">
        Name: <input type="text" name="name" value="<?= htmlspecialchars($client['name']) ?>" required><br><br>
        Email: <input type="email" name="email" value="<?= htmlspecialchars($client['email']) ?>" required><br><br>
        Service: <input type="text" name="service" value="<?= htmlspecialchars($client['service']) ?>" required><br><br>
        Upload New File: <input type="file" name="file"><br>
        (Current File: <?= htmlspecialchars($client['filename']) ?>)<br><br>
        <input type="submit" value="Update Client">
    </form>
    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>
