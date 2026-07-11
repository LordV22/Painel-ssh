<?php
session_start();
require_once("../pages/system/seguranca.php");

if ($conn === null) { die("Erro de conexão."); }

// Check if admin already exists
$stmt = $conn->query("SELECT setup_done FROM painel_admin LIMIT 1");
$admin = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : null;

if ($admin && $admin['setup_done'] == 1) {
    header("Location: login.php");
    exit;
}

$erro = "";
$sucesso = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $senha2 = $_POST['senha2'] ?? '';

    if (empty($nome) || empty($email) || empty($senha)) {
        $erro = "Preencha todos os campos.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "E-mail inválido.";
    } elseif (strlen($senha) < 6) {
        $erro = "A senha deve ter no mínimo 6 caracteres.";
    } elseif ($senha !== $senha2) {
        $erro = "As senhas não conferem.";
    } else {
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO painel_admin (nome, email, senha, setup_done) VALUES (?, ?, ?, 1)");
        $stmt->execute([$nome, $email, $hash]);
        header("Location: login.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setup Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root{--primary:#7367f0;--grad-1:#7367f0;--grad-2:#9e95f5;--bg:#0f1021;--card:#1a1b3a;}
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Montserrat',sans-serif;background:linear-gradient(135deg,var(--bg),var(--card));min-height:100vh;display:flex;align-items:center;justify-content:center;color:#e0e0f0;}
        .card{background:var(--card);border-radius:18px;padding:40px 32px;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(115,103,240,.25);border:1px solid rgba(115,103,240,.2);}
        h1{text-align:center;font-size:22px;color:#fff;margin-bottom:6px;}
        .sub{text-align:center;font-size:13px;color:#a0a0c0;margin-bottom:24px;}
        label{display:block;font-size:13px;font-weight:500;margin-bottom:6px;color:#c0c0e0;}
        input[type=text],input[type=email],input[type=password]{width:100%;padding:12px 14px;border-radius:10px;border:1px solid rgba(115,103,240,.3);background:rgba(255,255,255,.04);color:#fff;font-size:15px;margin-bottom:16px;font-family:inherit;}
        input:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(115,103,240,.15);}
        input::placeholder{color:#7070a0;}
        .btn{width:100%;padding:13px;border:none;border-radius:10px;background:linear-gradient(90deg,var(--grad-1),var(--grad-2));color:#fff;font-size:16px;font-weight:600;cursor:pointer;}
        .btn:hover{box-shadow:0 8px 24px rgba(115,103,240,.4);transform:translateY(-1px);}
        .erro{background:rgba(234,84,85,.1);border:1px solid rgba(234,84,85,.4);color:#ff9a9b;padding:10px;border-radius:10px;font-size:13px;margin-bottom:16px;text-align:center;}
    </style>
</head>
<body>
<div class="card">
    <h1>🛡️ Setup Inicial</h1>
    <p class="sub">Cadastre o único administrador do sistema</p>
    <?php if ($erro): ?><div class="erro"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
    <form method="POST">
        <label>Nome</label>
        <input type="text" name="nome" placeholder="Seu nome" required>
        <label>E-mail</label>
        <input type="email" name="email" placeholder="seu@email.com" required>
        <label>Senha</label>
        <input type="password" name="senha" placeholder="Mínimo 6 caracteres" required>
        <label>Confirmar Senha</label>
        <input type="password" name="senha2" placeholder="Repita a senha" required>
        <button type="submit" class="btn">Criar Administrador</button>
    </form>
</div>
</body>
</html>
