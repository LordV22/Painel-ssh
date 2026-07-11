<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }

require_once("../pages/system/seguranca.php");

$admin_nome = $_SESSION['admin_nome'] ?? 'Admin';
$admin_email = $_SESSION['admin_email'] ?? '';

// Stats
$total_users = 0;
$total_ssh = 0;
if ($conn) {
    $r = $conn->query("SELECT COUNT(*) as t FROM usuario");
    if ($r) $total_users = $r->fetch()['t'];
    $r = $conn->query("SELECT COUNT(*) as t FROM usuario_ssh");
    if ($r) $total_ssh = $r->fetch()['t'];
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root{--primary:#7367f0;--grad-1:#7367f0;--grad-2:#9e95f5;--bg:#0f1021;--card:#1a1b3a;--sidebar:#151631;}
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Montserrat',sans-serif;background:var(--bg);color:#e0e0f0;min-height:100vh;display:flex;}
        .sidebar{width:260px;background:var(--sidebar);padding:24px 16px;border-right:1px solid rgba(115,103,240,.15);min-height:100vh;position:fixed;}
        .sidebar h2{font-size:18px;color:#fff;margin-bottom:4px;}
        .sidebar .role{font-size:11px;color:var(--primary);margin-bottom:32px;display:block;}
        .sidebar nav a{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;color:#a0a0c0;text-decoration:none;font-size:14px;margin-bottom:4px;transition:all .2s;}
        .sidebar nav a:hover,.sidebar nav a.active{background:rgba(115,103,240,.15);color:#fff;}
        .main{margin-left:260px;padding:32px;flex:1;}
        .topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:32px;}
        .topbar h1{font-size:24px;color:#fff;}
        .topbar a{color:#ff6b6b;text-decoration:none;font-size:14px;}
        .cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;margin-bottom:32px;}
        .stat-card{background:var(--card);border-radius:14px;padding:24px;border:1px solid rgba(115,103,240,.15);}
        .stat-card .num{font-size:32px;font-weight:700;color:#fff;}
        .stat-card .label{font-size:13px;color:#a0a0c0;margin-top:4px;}
        .section{background:var(--card);border-radius:14px;padding:24px;border:1px solid rgba(115,103,240,.15);}
        .section h3{color:#fff;margin-bottom:16px;font-size:16px;}
        table{width:100%;border-collapse:collapse;}
        th{text-align:left;padding:10px;color:#a0a0c0;font-size:12px;border-bottom:1px solid rgba(115,103,240,.15);}
        td{padding:10px;font-size:13px;border-bottom:1px solid rgba(115,103,240,.08);}
        .empty{text-align:center;color:#7070a0;padding:40px;font-size:14px;}
    </style>
</head>
<body>
    <aside class="sidebar">
        <h2>🛡️ Admin</h2>
        <span class="role"><?= htmlspecialchars($admin_nome) ?></span>
        <nav>
            <a href="home.php" class="active">📊 Dashboard</a>
            <a href="?page=usuarios">👥 Usuários</a>
            <a href="?page=servidores">🖥️ Servidores</a>
            <a href="?page=config">⚙️ Configurações</a>
        </nav>
    </aside>
    <main class="main">
        <div class="topbar">
            <h1>Dashboard</h1>
            <a href="logout.php">Sair ↗</a>
        </div>
        <div class="cards">
            <div class="stat-card">
                <div class="num"><?= $total_users ?></div>
                <div class="label">Usuários Cadastrados</div>
            </div>
            <div class="stat-card">
                <div class="num"><?= $total_ssh ?></div>
                <div class="label">Contas SSH</div>
            </div>
        </div>
        <div class="section">
            <h3>Bem-vindo ao Painel Administrativo</h3>
            <p style="color:#a0a0c0;font-size:14px;">E-mail: <?= htmlspecialchars($admin_email) ?></p>
            <p style="color:#a0a0c0;font-size:14px;margin-top:8px;">Use o menu lateral para gerenciar o sistema.</p>
        </div>
    </main>
</body>
</html>
