<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    $stmt = $pdo->prepare("SELECT filename FROM clients WHERE id = ?");
    $stmt->execute([$id]);
    $client = $stmt->fetch();

    if ($client && $client['filename'] && file_exists('uploads/' . $client['filename'])) {
        unlink('uploads/' . $client['filename']);
    }

    $stmt = $pdo->prepare("DELETE FROM clients WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: dashboard.php');
exit;
?>