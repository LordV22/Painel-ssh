<?php
session_start();
if (!isset($_SESSION['admin_id'])) { header("Location: login.php"); exit; }

require_once("../pages/system/seguranca.php");

$admin_nome = $_SESSION['admin_nome'] ?? 'Admin';
$admin_email = $_SESSION['admin_email'] ?? '';
$page = $_GET['page'] ?? 'dashboard';

// Stats
$total_users = 0;
$total_ssh = 0;
$total_servidores = 0;
$total_online = 0;
if ($conn) {
    $r = $conn->query("SELECT COUNT(*) as t FROM usuario");
    if ($r) $total_users = $r->fetch()['t'];
    $r = $conn->query("SELECT COUNT(*) as t FROM usuario_ssh");
    if ($r) $total_ssh = $r->fetch()['t'];
    $r = $conn->query("SELECT COUNT(*) as t FROM servidor");
    if ($r) $total_servidores = $r->fetch()['t'];
    $r = $conn->query("SELECT SUM(online) as t FROM usuario_ssh");
    if ($r) $total_online = $r->fetch()['t'] ?? 0;
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
        .section{background:var(--card);border-radius:14px;padding:24px;border:1px solid rgba(115,103,240,.15);margin-bottom:20px;}
        .section h3{color:#fff;margin-bottom:16px;font-size:16px;}
        table{width:100%;border-collapse:collapse;}
        th{text-align:left;padding:10px;color:#a0a0c0;font-size:12px;border-bottom:1px solid rgba(115,103,240,.15);}
        td{padding:10px;font-size:13px;border-bottom:1px solid rgba(115,103,240,.08);}
        .empty{text-align:center;color:#7070a0;padding:40px;font-size:14px;}
        label{display:block;font-size:13px;font-weight:500;margin-bottom:6px;color:#c0c0e0;}
        input[type=text],input[type=email],input[type=password],input[type=number],select,textarea{width:100%;padding:10px 12px;border-radius:8px;border:1px solid rgba(115,103,240,.3);background:rgba(255,255,255,.04);color:#fff;font-size:14px;margin-bottom:12px;font-family:inherit;}
        input:focus,select:focus,textarea:focus{outline:none;border-color:var(--primary);}
        .btn{padding:10px 20px;border:none;border-radius:8px;background:linear-gradient(90deg,var(--grad-1),var(--grad-2));color:#fff;font-size:14px;font-weight:600;cursor:pointer;}
        .btn:hover{box-shadow:0 4px 16px rgba(115,103,240,.4);}
        .btn-danger{background:linear-gradient(90deg,#e74c3c,#c0392b);}
        .btn-success{background:linear-gradient(90deg,#27ae60,#2ecc71);}
        .msg-ok{background:rgba(39,174,96,.15);border:1px solid rgba(39,174,96,.4);color:#2ecc71;padding:10px;border-radius:8px;margin-bottom:16px;font-size:13px;}
        .msg-erro{background:rgba(234,84,85,.1);border:1px solid rgba(234,84,85,.4);color:#ff9a9b;padding:10px;border-radius:8px;margin-bottom:16px;font-size:13px;}
        .row{display:flex;gap:16px;}
        .row>div{flex:1;}
        .badge-green{background:rgba(39,174,96,.2);color:#2ecc71;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600;}
        .badge-red{background:rgba(231,76,60,.2);color:#e74c3c;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600;}
        .badge-yellow{background:rgba(241,196,15,.2);color:#f1c40f;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600;}
    </style>
</head>
<body>
    <aside class="sidebar">
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
            <h1><?php
                $titles = ['dashboard'=>'Dashboard','usuarios'=>'Usuários','criar-usuario'=>'Criar Usuário','servidores'=>'Servidores','criar-servidor'=>'Criar Servidor','config'=>'Configurações'];
                echo $titles[$page] ?? 'Dashboard';
            ?></h1>
        </div>

<?php
// ========== DASHBOARD ==========
if ($page === 'dashboard'): ?>
        <div class="cards">
            <div class="stat-card"><div class="num"><?= $total_users ?></div><div class="label">Usuários</div></div>
            <div class="stat-card"><div class="num"><?= $total_ssh ?></div><div class="label">Contas SSH</div></div>
            <div class="stat-card"><div class="num"><?= $total_servidores ?></div><div class="label">Servidores</div></div>
            <div class="stat-card"><div class="num"><?= $total_online ?></div><div class="label">Online Agora</div></div>
        </div>
        <div class="section">
            <h3>Bem-vindo ao Painel Administrativo</h3>
            <p style="color:#a0a0c0;font-size:14px;">E-mail: <?= htmlspecialchars($admin_email) ?></p>
            <p style="color:#a0a0c0;font-size:14px;margin-top:8px;">Use o menu lateral para gerenciar o sistema.</p>
        </div>

<?php
// ========== LISTAR USUÁRIOS ==========
elseif ($page === 'usuarios'):
    $usuarios = $conn->query("SELECT * FROM usuario ORDER BY id_usuario DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
        <div class="section">
            <h3>Todos os Usuários (<?= count($usuarios) ?>)</h3>
            <?php if (empty($usuarios)): ?>
                <div class="empty">Nenhum usuário cadastrado.</div>
            <?php else: ?>
            <table>
                <tr><th>ID</th><th>Nome</th><th>Login</th><th>E-mail</th><th>Tipo</th><th>Status</th><th>Ações</th></tr>
                <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><?= $u['id_usuario'] ?></td>
                    <td><?= htmlspecialchars($u['nome']) ?></td>
                    <td><?= htmlspecialchars($u['login']) ?></td>
                    <td><?= htmlspecialchars($u['email']) ?></td>
                    <td><?= ucfirst($u['tipo']) ?></td>
                    <td><?= $u['ativo']==1 ? '<span class="badge-green">Ativo</span>' : '<span class="badge-red">Suspenso</span>' ?></td>
                    <td>
                        <a href="?page=editar-usuario&id=<?= $u['id_usuario'] ?>" style="color:var(--primary);font-size:12px;">Editar</a>
                        <a href="?page=deletar-usuario&id=<?= $u['id_usuario'] ?>" style="color:#e74c3c;font-size:12px;margin-left:8px;" onclick="return confirm('Tem certeza?')">Deletar</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php endif; ?>
        </div>

<?php
// ========== CRIAR USUÁRIOS ==========
elseif ($page === 'criar-usuario'):
    $msg = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = trim($_POST['nome'] ?? '');
        $login = trim($_POST['login'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $tipo = $_POST['tipo'] ?? 'vpn';
        if ($nome && $login && $senha) {
            $stmt = $conn->prepare("INSERT INTO usuario (nome, login, email, senha, tipo, ativo) VALUES (?, ?, ?, ?, ?, 1)");
            $stmt->execute([$nome, $login, $email, $senha, $tipo]);
            $msg = '<div class="msg-ok">Usuário criado com sucesso!</div>';
        } else {
            $msg = '<div class="msg-erro">Preencha nome, login e senha.</div>';
        }
    }
    echo $msg;
?>
        <div class="section">
            <h3>Novo Usuário</h3>
            <form method="POST">
                <div class="row">
                    <div><label>Nome</label><input type="text" name="nome" placeholder="Nome completo" required></div>
                    <div><label>Login</label><input type="text" name="login" placeholder="Login de acesso" required></div>
                </div>
                <div class="row">
                    <div><label>E-mail</label><input type="email" name="email" placeholder="email@exemplo.com"></div>
                    <div><label>Senha</label><input type="password" name="senha" placeholder="Senha" required></div>
                </div>
                <label>Tipo</label>
                <select name="tipo"><option value="vpn">VPN</option><option value="revenda">Revenda</option></select>
                <br><button type="submit" class="btn">Criar Usuário</button>
            </form>
        </div>

<?php
// ========== EDITAR USUÁRIO ==========
elseif ($page === 'editar-usuario'):
    $id = intval($_GET['id'] ?? 0);
    $msg = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = trim($_POST['nome'] ?? '');
        $login = trim($_POST['login'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $tipo = $_POST['tipo'] ?? 'vpn';
        $ativo = intval($_POST['ativo'] ?? 1);
        $nova_senha = trim($_POST['nova_senha'] ?? '');
        if ($nova_senha) {
            $stmt = $conn->prepare("UPDATE usuario SET nome=?, login=?, email=?, tipo=?, ativo=?, senha=? WHERE id_usuario=?");
            $stmt->execute([$nome, $login, $email, $tipo, $ativo, $nova_senha, $id]);
        } else {
            $stmt = $conn->prepare("UPDATE usuario SET nome=?, login=?, email=?, tipo=?, ativo=? WHERE id_usuario=?");
            $stmt->execute([$nome, $login, $email, $tipo, $ativo, $id]);
        }
        $msg = '<div class="msg-ok">Usuário atualizado!</div>';
    }
    $stmt = $conn->prepare("SELECT * FROM usuario WHERE id_usuario = ?");
    $stmt->execute([$id]);
    $u = $stmt->fetch(PDO::FETCH_ASSOC);
    echo $msg;
?>
        <div class="section">
            <h3>Editar Usuário #<?= $id ?></h3>
            <?php if ($u): ?>
            <form method="POST">
                <div class="row">
                    <div><label>Nome</label><input type="text" name="nome" value="<?= htmlspecialchars($u['nome']) ?>" required></div>
                    <div><label>Login</label><input type="text" name="login" value="<?= htmlspecialchars($u['login']) ?>" required></div>
                </div>
                <div class="row">
                    <div><label>E-mail</label><input type="email" name="email" value="<?= htmlspecialchars($u['email']) ?>"></div>
                    <div><label>Nova Senha (deixe vazio para manter)</label><input type="password" name="nova_senha" placeholder="Deixe vazio para manter"></div>
                </div>
                <div class="row">
                    <div><label>Tipo</label><select name="tipo"><option value="vpn" <?= $u['tipo']=='vpn'?'selected':'' ?>>VPN</option><option value="revenda" <?= $u['tipo']=='revenda'?'selected':'' ?>>Revenda</option></select></div>
                    <div><label>Status</label><select name="ativo"><option value="1" <?= $u['ativo']==1?'selected':'' ?>>Ativo</option><option value="2" <?= $u['ativo']==2?'selected':'' ?>>Suspenso</option></select></div>
                </div>
                <button type="submit" class="btn">Salvar</button>
                <a href="?page=usuarios" style="color:#a0a0c0;margin-left:16px;font-size:14px;">← Voltar</a>
            </form>
            <?php else: ?><div class="msg-erro">Usuário não encontrado.</div><?php endif; ?>
        </div>

<?php
// ========== DELETAR USUÁRIO ==========
elseif ($page === 'deletar-usuario'):
    $id = intval($_GET['id'] ?? 0);
    if ($id > 0) {
        $conn->prepare("DELETE FROM usuario WHERE id_usuario = ?")->execute([$id]);
    }
    header("Location: ?page=usuarios");
    exit;

<?php
// ========== LISTAR SERVIDORES ==========
elseif ($page === 'servidores'):
    $servs = $conn->query("SELECT * FROM servidor ORDER BY id_servidor DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
        <div class="section">
            <h3>Servidores (<?= count($servs) ?>)</h3>
            <?php if (empty($servs)): ?>
                <div class="empty">Nenhum servidor cadastrado.</div>
            <?php else: ?>
            <table>
                <tr><th>ID</th><th>Nome</th><th>IP</th><th>Porta</th><th>Ações</th></tr>
                <?php foreach ($servs as $s): ?>
                <tr>
                    <td><?= $s['id_servidor'] ?></td>
                    <td><?= htmlspecialchars($s['nome'] ?? $s['servidor'] ?? '') ?></td>
                    <td><?= htmlspecialchars($s['ip'] ?? $s['endereco'] ?? '') ?></td>
                    <td><?= $s['porta'] ?? '22' ?></td>
                    <td><a href="?page=deletar-servidor&id=<?= $s['id_servidor'] ?>" style="color:#e74c3c;font-size:12px;" onclick="return confirm('Tem certeza?')">Deletar</a></td>
                </tr>
                <?php endforeach; ?>
            </table>
            <?php endif; ?>
        </div>

<?php
// ========== CRIAR SERVIDOR ==========
elseif ($page === 'criar-servidor'):
    $msg = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nome = trim($_POST['nome'] ?? '');
        $ip = trim($_POST['ip'] ?? '');
        $porta = intval($_POST['porta'] ?? 22);
        if ($nome && $ip) {
            $stmt = $conn->prepare("INSERT INTO servidor (servidor, ip, porta) VALUES (?, ?, ?)");
            $stmt->execute([$nome, $ip, $porta]);
            $msg = '<div class="msg-ok">Servidor criado com sucesso!</div>';
        } else {
            $msg = '<div class="msg-erro">Preencha nome e IP.</div>';
        }
    }
    echo $msg;
?>
        <div class="section">
            <h3>Novo Servidor</h3>
            <form method="POST">
                <div class="row">
                    <div><label>Nome</label><input type="text" name="nome" placeholder="Ex: Servidor Brasil" required></div>
                    <div><label>IP</label><input type="text" name="ip" placeholder="192.168.1.1" required></div>
                </div>
                <label>Porta SSH</label>
                <input type="number" name="porta" value="22">
                <button type="submit" class="btn">Criar Servidor</button>
            </form>
        </div>

<?php
// ========== DELETAR SERVIDOR ==========
elseif ($page === 'deletar-servidor'):
    $id = intval($_GET['id'] ?? 0);
    if ($id > 0) {
        $conn->prepare("DELETE FROM servidor WHERE id_servidor = ?")->execute([$id]);
    }
    header("Location: ?page=servidores");
    exit;

<?php
// ========== CONFIGURAÇÕES ==========
elseif ($page === 'config'):
    $msg = '';
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $acao = $_POST['acao'] ?? '';
        if ($acao === 'trocar-senha') {
            $senha_atual = $_POST['senha_atual'] ?? '';
            $nova = $_POST['nova_senha'] ?? '';
            $stmt = $conn->prepare("SELECT * FROM painel_admin WHERE id = ?");
            $stmt->execute([$_SESSION['admin_id']]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($admin && password_verify($senha_atual, $admin['senha'])) {
                $hash = password_hash($nova, PASSWORD_DEFAULT);
                $conn->prepare("UPDATE painel_admin SET senha = ? WHERE id = ?")->execute([$hash, $_SESSION['admin_id']]);
                $msg = '<div class="msg-ok">Senha alterada com sucesso!</div>';
            } else {
                $msg = '<div class="msg-erro">Senha atual incorreta.</div>';
            }
        } elseif ($acao === 'trocar-email') {
            $novo_email = trim($_POST['novo_email'] ?? '');
            if (filter_var($novo_email, FILTER_VALIDATE_EMAIL)) {
                $conn->prepare("UPDATE painel_admin SET email = ? WHERE id = ?")->execute([$novo_email, $_SESSION['admin_id']]);
                $_SESSION['admin_email'] = $novo_email;
                $msg = '<div class="msg-ok">E-mail atualizado!</div>';
            } else {
                $msg = '<div class="msg-erro">E-mail inválido.</div>';
            }
        }
    }
    echo $msg;
?>
        <div class="section">
            <h3>Trocar Senha</h3>
            <form method="POST">
                <input type="hidden" name="acao" value="trocar-senha">
                <label>Senha Atual</label>
                <input type="password" name="senha_atual" required>
                <label>Nova Senha</label>
                <input type="password" name="nova_senha" required>
                <button type="submit" class="btn">Alterar Senha</button>
            </form>
        </div>
        <div class="section">
            <h3>Trocar E-mail</h3>
            <form method="POST">
                <input type="hidden" name="acao" value="trocar-email">
                <label>Novo E-mail</label>
                <input type="email" name="novo_email" value="<?= htmlspecialchars($admin_email) ?>" required>
                <button type="submit" class="btn">Alterar E-mail</button>
            </form>
        </div>

<?php endif; ?>

    </main>
</body>
</html>
