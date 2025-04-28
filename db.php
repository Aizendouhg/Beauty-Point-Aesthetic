<?php
$pdo = new PDO('mysql:host=localhost;dbname=client_system', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>