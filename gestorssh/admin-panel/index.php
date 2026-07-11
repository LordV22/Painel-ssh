<?php
session_start();
require_once("../pages/system/seguranca.php");

if ($conn === null) { die("Erro de conexão com banco de dados."); }

// Check if admin already exists
$stmt = $conn->query("SELECT setup_done FROM painel_admin LIMIT 1");
$admin = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;

if ($admin && $admin['setup_done'] == 1) {
    // Admin already registered → show login
    header("Location: login.php");
    exit;
} else {
    // No admin yet → show registration form
    header("Location: setup.php");
    exit;
}
?>
