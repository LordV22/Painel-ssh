<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }
require_once("../pages/system/seguranca.php");
$admin_nome = $_SESSION['admin_nome'] ?? 'Admin';
$admin_email = $_SESSION['admin_email'] ?? '';
$page = $_GET['page'] ?? 'dashboard';
$total_users = 0; $total_ssh = 0; $total_servidores = 0; $total_online = 0;
if ($conn) {
    $r = $conn->query("SELECT COUNT(*) as t FROM usuario"); if ($r) $total_users = $r->fetch()['t'];
    $r = $conn->query("SELECT COUNT(*) as t FROM usuario_ssh"); if ($r) $total_ssh = $r->fetch()['t'];
    $r = $conn->query("SELECT COUNT(*) as t FROM servidor"); if ($r) $total_servidores = $r->fetch()['t'];
    $r = $conn->query("SELECT SUM(online) as t FROM usuario_ssh"); if ($r) $total_online = $r->fetch()['t'] ?? 0;
}

// Handle deletar actions
if ($page === 'deletar-usuario') { $id = intval($_GET['id'] ?? 0); if ($id > 0) $conn->prepare("DELETE FROM usuario WHERE id_usuario = ?")->execute([$id]); header("Location: ?page=usuarios"); exit; }
if ($page === 'deletar-servidor') { $id = intval($_GET['id'] ?? 0); if ($id > 0) $conn->prepare("DELETE FROM servidor WHERE id_servidor = ?")->execute([$id]); header("Location: ?page=servidores"); exit; }

// Handle POST
$msg = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';
    if ($acao === 'criar-usuario') {
        $nome = trim($_POST['nome'] ?? ''); $login = trim($_POST['login'] ?? ''); $email = trim($_POST['email'] ?? ''); $senha = $_POST['senha'] ?? ''; $tipo = $_POST['tipo'] ?? 'vpn';
        if ($nome && $login && $senha) { $conn->prepare("INSERT INTO usuario (nome,login,email,senha,tipo,ativo) VALUES (?,?,?,?,?,1)")->execute([$nome,$login,$email,$senha,$tipo]); $msg = '<div class="msg-ok">Usuário criado!</div>'; }
        else { $msg = '<div class="msg-erro">Preencha nome, login e senha.</div>'; }
    }
    if ($acao === 'editar-usuario') {
        $id = intval($_POST['id'] ?? 0); $nome = trim($_POST['nome'] ?? ''); $login = trim($_POST['login'] ?? ''); $email = trim($_POST['email'] ?? ''); $tipo = $_POST['tipo'] ?? 'vpn'; $ativo = intval($_POST['ativo'] ?? 1); $ns = trim($_POST['nova_senha'] ?? '');
        if ($ns) { $conn->prepare("UPDATE usuario SET nome=?,login=?,email=?,tipo=?,ativo=?,senha=? WHERE id_usuario=?")->execute([$nome,$login,$email,$tipo,$ativo,$ns,$id]); }
        else { $conn->prepare("UPDATE usuario SET nome=?,login=?,email=?,tipo=?,ativo=? WHERE id_usuario=?")->execute([$nome,$login,$email,$tipo,$ativo,$id]); }
        $msg = '<div class="msg-ok">Usuário atualizado!</div>';
    }
    if ($acao === 'criar-servidor') {
        $nome = trim($_POST['nome'] ?? ''); $ip = trim($_POST['ip'] ?? ''); $porta = intval($_POST['porta'] ?? 22); $regiao = $_POST['regiao'] ?? 'america'; $login_s = trim($_POST['login_server'] ?? ''); $senha_s = $_POST['senha_server'] ?? ''; $local = trim($_POST['localizacao'] ?? '');
        if ($nome && $ip) { $conn->prepare("INSERT INTO servidor (nome,ip_servidor,porta,regiao,login_server,senha,localizacao,localizacao_img,ativo,limite_usuario,site_servidor) VALUES (?,?,?,?,?,?,?,?,1,10,'')")->execute([$nome,$ip,$porta,$regiao,$login_s,$senha_s,$local,$local]); $msg = '<div class="msg-ok">Servidor criado!</div>'; }
        else { $msg = '<div class="msg-erro">Preencha nome e IP.</div>'; }
    }
    if ($acao === 'trocar-senha') {
        $sa = $_POST['senha_atual'] ?? ''; $nova = $_POST['nova_senha'] ?? '';
        $stmt = $conn->prepare("SELECT * FROM painel_admin WHERE id = ?"); $stmt->execute([$_SESSION['admin_id']]); $adm = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($adm && password_verify($sa, $adm['senha'])) { $conn->prepare("UPDATE painel_admin SET senha=? WHERE id=?")->execute([password_hash($nova, PASSWORD_DEFAULT), $_SESSION['admin_id']]); $msg = '<div class="msg-ok">Senha alterada!</div>'; }
        else { $msg = '<div class="msg-erro">Senha atual incorreta.</div>'; }
    }
    if ($acao === 'trocar-email') {
        $ne = trim($_POST['novo_email'] ?? '');
        if (filter_var($ne, FILTER_VALIDATE_EMAIL)) { $conn->prepare("UPDATE painel_admin SET email=? WHERE id=?")->execute([$ne, $_SESSION['admin_id']]); $_SESSION['admin_email'] = $ne; $msg = '<div class="msg-ok">E-mail atualizado!</div>'; }
        else { $msg = '<div class="msg-erro">E-mail inválido.</div>'; }
    }
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
:root{--p:#7367f0;--g1:#7367f0;--g2:#9e95f5;--bg:#0f1021;--card:#1a1b3a;--sb:#151631;}
*{margin:0;padding:0;box-sizing:border-box;}
body{font-family:'Montserrat',sans-serif;background:var(--bg);color:#e0e0f0;min-height:100vh;}
.sidebar{position:fixed;top:0;left:0;width:260px;background:var(--sb);padding:24px 16px;border-right:1px solid rgba(115,103,240,.15);height:100vh;overflow-y:auto;z-index:100;}
.sidebar h2{font-size:18px;color:#fff;margin-bottom:4px;}
.sidebar .role{font-size:11px;color:var(--p);margin-bottom:24px;display:block;}
.sidebar nav a{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:10px;color:#a0a0c0;text-decoration:none;font-size:14px;margin-bottom:4px;transition:all .2s;}
.sidebar nav a:hover,.sidebar nav a.active{background:rgba(115,103,240,.15);color:#fff;}
.main{margin-left:260px;padding:24px;min-height:100vh;}
.topbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:24px;}
.topbar h1{font-size:22px;color:#fff;}
.topbar a{color:#ff6b6b;text-decoration:none;font-size:14px;}
.menu-toggle{display:none;background:none;border:none;color:#fff;font-size:24px;cursor:pointer;}
.cards{display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:12px;margin-bottom:24px;}
.stat-card{background:var(--card);border-radius:14px;padding:20px;border:1px solid rgba(115,103,240,.15);}
.stat-card .num{font-size:28px;font-weight:700;color:#fff;}
.stat-card .label{font-size:12px;color:#a0a0c0;margin-top:4px;}
.section{background:var(--card);border-radius:14px;padding:20px;border:1px solid rgba(115,103,240,.15);margin-bottom:16px;}
.section h3{color:#fff;margin-bottom:16px;font-size:15px;}
table{width:100%;border-collapse:collapse;overflow-x:auto;display:block;}
th{text-align:left;padding:8px;color:#a0a0c0;font-size:11px;border-bottom:1px solid rgba(115,103,240,.15);white-space:nowrap;}
td{padding:8px;font-size:12px;border-bottom:1px solid rgba(115,103,240,.08);white-space:nowrap;}
.empty{text-align:center;color:#7070a0;padding:30px;font-size:13px;}
label{display:block;font-size:12px;font-weight:500;margin-bottom:4px;color:#c0c0e0;}
input[type=text],input[type=email],input[type=password],input[type=number],select{width:100%;padding:10px 12px;border-radius:8px;border:1px solid rgba(115,103,240,.3);background:rgba(255,255,255,.04);color:#fff;font-size:14px;margin-bottom:12px;font-family:inherit;}
input:focus,select:focus{outline:none;border-color:var(--p);}
.btn{padding:10px 20px;border:none;border-radius:8px;background:linear-gradient(90deg,var(--g1),var(--g2));color:#fff;font-size:14px;font-weight:600;cursor:pointer;}
.btn:hover{box-shadow:0 4px 16px rgba(115,103,240,.4);}
.msg-ok{background:rgba(39,174,96,.15);border:1px solid rgba(39,174,96,.4);color:#2ecc71;padding:10px;border-radius:8px;margin-bottom:12px;font-size:13px;}
.msg-erro{background:rgba(234,84,85,.1);border:1px solid rgba(234,84,85,.4);color:#ff9a9b;padding:10px;border-radius:8px;margin-bottom:12px;font-size:13px;}
.row{display:flex;gap:12px;}.row>div{flex:1;}
.badge-green{background:rgba(39,174,96,.2);color:#2ecc71;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:600;}
.badge-red{background:rgba(231,76,60,.2);color:#e74c3c;padding:2px 8px;border-radius:10px;font-size:10px;font-weight:600;}
.overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:99;}
@media(max-width:768px){
.sidebar{transform:translateX(-100%);transition:transform .3s;width:240px;}
.sidebar.open{transform:translateX(0);}
.overlay.open{display:block;}
.main{margin-left:0;padding:16px;}
.menu-toggle{display:block;}
.topbar h1{font-size:18px;}
.row{flex-direction:column;}
.cards{grid-template-columns:repeat(2,1fr);}
table{font-size:11px;}
th,td{padding:6px;}
}
</style>
</head>
<body>
<div class="overlay" id="overlay" onclick="toggleMenu()"></div>
<aside class="sidebar" id="sidebar">
<h2>🛡️ Admin</h2>
<span class="role"><?= htmlspecialchars($admin_nome) ?></span>
<nav>
<a href="?page=dashboard" class="<?= $page=='dashboard'?'active':'' ?>">📊 Dashboard</a>
<a href="?page=usuarios" class="<?= $page=='usuarios'?'active':'' ?>">👥 Usuários</a>
<a href="?page=criar-usuario" class="<?= $page=='criar-usuario'?'active':'' ?>">➕ Criar Usuário</a>
<a href="?page=servidores" class="<?= $page=='servidores'?'active':'' ?>">🖥️ Servidores</a>
<a href="?page=criar-servidor" class="<?= $page=='criar-servidor'?'active':'' ?>">➕ Criar Servidor</a>
<a href="?page=config" class="<?= $page=='config'?'active':'' ?>">⚙️ Configurações</a>
<a href="logout.php" style="color:#ff6b6b;">🚪 Sair</a>
</nav>
</aside>
<main class="main">
<div class="topbar">
<button class="menu-toggle" onclick="toggleMenu()">☰</button>
<h1><?php $t=['dashboard'=>'Dashboard','usuarios'=>'Usuários','criar-usuario'=>'Criar Usuário','servidores'=>'Servidores','criar-servidor'=>'Criar Servidor','config'=>'Configurações']; echo $t[$page]??'Dashboard'; ?></h1>
<a href="logout.php">Sair ↗</a>
</div>
<?= $msg ?>
<?php if ($page === 'dashboard'): ?>
<div class="cards">
<div class="stat-card"><div class="num"><?= $total_users ?></div><div class="label">Usuários</div></div>
<div class="stat-card"><div class="num"><?= $total_ssh ?></div><div class="label">Contas SSH</div></div>
<div class="stat-card"><div class="num"><?= $total_servidores ?></div><div class="label">Servidores</div></div>
<div class="stat-card"><div class="num"><?= $total_online ?></div><div class="label">Online</div></div>
</div>
<div class="section"><h3>Bem-vindo, <?= htmlspecialchars($admin_nome) ?></h3><p style="color:#a0a0c0;font-size:13px;">E-mail: <?= htmlspecialchars($admin_email) ?></p></div>

<?php elseif ($page === 'usuarios'): ?>
<?php $usuarios = $conn->query("SELECT * FROM usuario ORDER BY id_usuario DESC")->fetchAll(PDO::FETCH_ASSOC); ?>
<div class="section"><h3>Usuários (<?= count($usuarios) ?>)</h3>
<?php if (empty($usuarios)): ?><div class="empty">Nenhum usuário.</div>
<?php else: ?>
<div style="overflow-x:auto;">
<table><tr><th>ID</th><th>Nome</th><th>Login</th><th>Tipo</th><th>Status</th><th>Ações</th></tr>
<?php foreach ($usuarios as $u): ?>
<tr><td><?= $u['id_usuario'] ?></td><td><?= htmlspecialchars($u['nome']) ?></td><td><?= htmlspecialchars($u['login']) ?></td><td><?= ucfirst($u['tipo']) ?></td>
<td><?= $u['ativo']==1?'<span class="badge-green">Ativo</span>':'<span class="badge-red">Suspenso</span>' ?></td>
<td><a href="?page=editar-usuario&id=<?=$u['id_usuario']?>" style="color:var(--p);font-size:11px;">Editar</a> <a href="?page=deletar-usuario&id=<?=$u['id_usuario']?>" style="color:#e74c3c;font-size:11px;" onclick="return confirm('Deletar?')">Del</a></td>
</tr><?php endforeach; ?>
</table></div><?php endif; ?>
</div>

<?php elseif ($page === 'criar-usuario'): ?>
<div class="section"><h3>Novo Usuário</h3>
<form method="POST"><input type="hidden" name="acao" value="criar-usuario">
<div class="row"><div><label>Nome</label><input name="nome" required></div><div><label>Login</label><input name="login" required></div></div>
<div class="row"><div><label>E-mail</label><input type="email" name="email"></div><div><label>Senha</label><input type="password" name="senha" required></div></div>
<label>Tipo</label><select name="tipo"><option value="vpn">VPN</option><option value="revenda">Revenda</option></select>
<button type="submit" class="btn">Criar</button></form></div>

<?php elseif ($page === 'editar-usuario'): ?>
<?php $id=intval($_GET['id']??0); $stmt=$conn->prepare("SELECT * FROM usuario WHERE id_usuario=?"); $stmt->execute([$id]); $u=$stmt->fetch(PDO::FETCH_ASSOC); ?>
<div class="section"><h3>Editar Usuário #<?= $id ?></h3>
<?php if ($u): ?>
<form method="POST"><input type="hidden" name="acao" value="editar-usuario"><input type="hidden" name="id" value="<?=$id?>">
<div class="row"><div><label>Nome</label><input name="nome" value="<?=htmlspecialchars($u['nome'])?>" required></div><div><label>Login</label><input name="login" value="<?=htmlspecialchars($u['login'])?>" required></div></div>
<div class="row"><div><label>E-mail</label><input type="email" name="email" value="<?=htmlspecialchars($u['email'])?>"></div><div><label>Nova Senha (vazio mantém)</label><input type="password" name="nova_senha"></div></div>
<div class="row"><div><label>Tipo</label><select name="tipo"><option value="vpn" <?=$u['tipo']=='vpn'?'selected':''?>>VPN</option><option value="revenda" <?=$u['tipo']=='revenda'?'selected':''?>>Revenda</option></select></div>
<div><label>Status</label><select name="ativo"><option value="1" <?=$u['ativo']==1?'selected':''?>>Ativo</option><option value="2" <?=$u['ativo']==2?'selected':''?>>Suspenso</option></select></div></div>
<button type="submit" class="btn">Salvar</button> <a href="?page=usuarios" style="color:#a0a0c0;margin-left:12px;font-size:13px;">← Voltar</a>
</form><?php else: ?><div class="msg-erro">Não encontrado.</div><?php endif; ?>
</div>

<?php elseif ($page === 'servidores'): ?>
<?php $servs=$conn->query("SELECT * FROM servidor ORDER BY id_servidor DESC")->fetchAll(PDO::FETCH_ASSOC); ?>
<div class="section"><h3>Servidores (<?= count($servs) ?>)</h3>
<?php if (empty($servs)): ?><div class="empty">Nenhum servidor.</div>
<?php else: ?>
<div style="overflow-x:auto;">
<table><tr><th>ID</th><th>Nome</th><th>IP</th><th>Porta</th><th>Ações</th></tr>
<?php foreach ($servs as $s): ?>
<tr><td><?=$s['id_servidor']?></td><td><?=htmlspecialchars(htmlspecialchars($s['nome']??''))?></td><td><?=htmlspecialchars($s['ip_servidor']??'')?></td><td><?=$s['porta']??'22'?></td>
<td><a href="?page=deletar-servidor&id=<?=$s['id_servidor']?>" style="color:#e74c3c;font-size:11px;" onclick="return confirm('Deletar?')">Del</a></td>
</tr><?php endforeach; ?>
</table></div><?php endif; ?>
</div>

<?php elseif ($page === 'criar-servidor'): ?>
<div class="section"><h3>Novo Servidor</h3>
<form method="POST"><input type="hidden" name="acao" value="criar-servidor">
<div class="row"><div><label>Nome</label><input name="nome" placeholder="Ex: Servidor BR" required></div><div><label>IP</label><input name="ip" placeholder="192.168.1.1" required></div></div>
<div class="row"><div><label>Região</label><select name="regiao"><option value="america">América</option><option value="europa">Europa</option><option value="asia">Ásia</option><option value="australia">Oceania</option></select></div><div><label>Porta SSH</label><input type="number" name="porta" value="22"></div></div>
<div class="row"><div><label>Login Servidor</label><input name="login_server" placeholder="root"></div><div><label>Senha Servidor</label><input type="password" name="senha_server"></div></div>
<label>Localização</label><input name="localizacao" placeholder="Ex: São Paulo, Brasil">
<button type="submit" class="btn">Criar Servidor</button></form></div>

<?php elseif ($page === 'config'): ?>
<div class="section"><h3>Trocar Senha</h3>
<form method="POST"><input type="hidden" name="acao" value="trocar-senha">
<label>Senha Atual</label><input type="password" name="senha_atual" required>
<label>Nova Senha</label><input type="password" name="nova_senha" required>
<button type="submit" class="btn">Alterar</button></form></div>
<div class="section"><h3>Trocar E-mail</h3>
<form method="POST"><input type="hidden" name="acao" value="trocar-email">
<label>Novo E-mail</label><input type="email" name="novo_email" value="<?=htmlspecialchars($admin_email)?>" required>
<button type="submit" class="btn">Alterar</button></form></div>

<?php endif; ?>
</main>
<script>
function toggleMenu(){document.getElementById('sidebar').classList.toggle('open');document.getElementById('overlay').classList.toggle('open');}
</script>
</body>
</html>
