<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$search = '';
$perPage = 5;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$start = ($page - 1) * $perPage;

if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
    $stmt = $pdo->prepare("SELECT * FROM clients WHERE name LIKE ? ORDER BY created_at DESC LIMIT $start, $perPage");
    $stmt->execute(["%$search%"]);

    $total = $pdo->prepare("SELECT COUNT(*) FROM clients WHERE name LIKE ?");
    $total->execute(["%$search%"]);
} else {
    $stmt = $pdo->prepare("SELECT * FROM clients ORDER BY created_at DESC LIMIT $start, $perPage");
    $stmt->execute();

    $total = $pdo->prepare("SELECT COUNT(*) FROM clients");
    $total->execute();
}

$clients = $stmt->fetchAll();
$totalClients = $total->fetchColumn();
$totalPages = ceil($totalClients / $perPage);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <style>
        body {
            background-image: url('bg.jpg');
            background-size: stretch;
            background-repeat: no-repeat;
            background-position: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            background: rgba(255, 255, 255, 0.2); /* transparent white */
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0,0,0,0.25);
            width: 100%;
            max-width: 1200px;
            margin-top: 20px;
        }
        h2 {
            text-align: center;
            color: #fff;
        }
        .top-links {
            text-align: center;
            margin-bottom: 20px;
        }
        .top-links a {
            font-weight: bold;
            color: #fff;
            margin: 0 5px;
            text-decoration: none;
            background: rgba(52, 152, 219, 0.7);
            padding: 8px 12px;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .top-links a:hover {
            background: rgba(41, 128, 185, 0.7);
        }
        form {
            text-align: center;
            margin-bottom: 20px;
        }
        input[type="text"] {
            padding: 8px;
            width: 250px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.7);
        }
        input[type="submit"] {
            padding: 8px 12px;
            background: #3498db;
            border: none;
            color: #fff;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background: #2980b9;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: rgba(255, 255, 255, 0.7);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        table th, table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
            text-align: center;
        }
        table th {
            background: #3498db;
            color: #fff;
        }
        table tr:nth-child(even) {
            background: rgba(249, 249, 249, 0.6);
        }
        .pagination {
            text-align: center;
            margin-top: 20px;
        }
        .pagination a {
            display: inline-block;
            padding: 8px 12px;
            margin: 0 2px;
            background: rgba(255,255,255,0.7);
            color: #333;
            border-radius: 5px;
            text-decoration: none;
        }
        .pagination a:hover {
            background: #3498db;
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Client Records</h2>

        <div class="top-links">
            <a href="add_client.php">Add New Client</a> | 
            <a href="logout.php">Logout</a>
        </div>

        <form method="GET" action="dashboard.php">
            <input type="text" name="search" placeholder="Search client name..." value="<?= htmlspecialchars($search) ?>">
            <input type="submit" value="Search">
            <?php if ($search != ''): ?>
                <a href="dashboard.php" style="color:#e74c3c; text-decoration:none; margin-left:10px;">Clear Search</a>
            <?php endif; ?>
        </form>

        <table>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Service</th>
                <th>File</th>
                <th>Date Added</th>
                <th>Actions</th>
            </tr>
            <?php if (count($clients) > 0): ?>
                <?php foreach ($clients as $client): ?>
                <tr>
                    <td><?= htmlspecialchars($client['name']) ?></td>
                    <td><?= htmlspecialchars($client['email']) ?></td>
                    <td><?= htmlspecialchars($client['service']) ?></td>
                    <td>
                        <?php if ($client['filename']): ?>
                            <a href="uploads/<?= htmlspecialchars($client['filename']) ?>" download>Download</a>
                        <?php else: ?>
                            No File
                        <?php endif; ?>
                    </td>
                    <td><?= $client['created_at'] ?></td>
                    <td>
                        <a href="edit_client.php?id=<?= $client['id'] ?>">Edit</a> |
                        <a href="delete_client.php?id=<?= $client['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6">No clients found.</td>
                </tr>
            <?php endif; ?>
        </table>

        <div class="pagination">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <a href="?<?php if ($search) echo 'search=' . urlencode($search) . '&'; ?>page=<?= $i ?>">
                    <?= $i ?>
                </a>
            <?php endfor; ?>
        </div>
    </div>
</body>
</html>