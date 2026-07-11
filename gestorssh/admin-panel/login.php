<?php
session_start();
require_once("../pages/system/seguranca.php");

if ($conn === null) { die("Erro de conexão."); }

// If already logged in, go to dashboard
if (isset($_SESSION['admin_id'])) { header("Location: home.php"); exit; }

$erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($email) || empty($senha)) {
        $erro = "Preencha todos os campos.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM painel_admin WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($senha, $admin['senha'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_nome'] = $admin['nome'];
            $_SESSION['admin_email'] = $admin['email'];
            header("Location: home.php");
            exit;
        } else {
            $erro = "E-mail ou senha incorretos.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        :root{--primary:#7367f0;--grad-1:#7367f0;--grad-2:#9e95f5;--bg:#0f1021;--card:#1a1b3a;}
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Montserrat',sans-serif;background:linear-gradient(135deg,var(--bg),var(--card));min-height:100vh;display:flex;align-items:center;justify-content:center;color:#e0e0f0;}
        .card{background:var(--card);border-radius:18px;padding:40px 32px;width:100%;max-width:400px;box-shadow:0 20px 60px rgba(115,103,240,.25);border:1px solid rgba(115,103,240,.2);}
        h1{text-align:center;font-size:22px;color:#fff;margin-bottom:6px;}
        .badge{text-align:center;margin-bottom:20px;}
        .badge span{display:inline-block;background:linear-gradient(90deg,var(--grad-1),var(--grad-2));padding:4px 14px;border-radius:20px;font-size:12px;font-weight:600;}
        .sub{text-align:center;font-size:13px;color:#a0a0c0;margin-bottom:24px;}
        label{display:block;font-size:13px;font-weight:500;margin-bottom:6px;color:#c0c0e0;}
        input[type=email],input[type=password]{width:100%;padding:12px 14px;border-radius:10px;border:1px solid rgba(115,103,240,.3);background:rgba(255,255,255,.04);color:#fff;font-size:15px;margin-bottom:16px;font-family:inherit;}
        input:focus{outline:none;border-color:var(--primary);box-shadow:0 0 0 3px rgba(115,103,240,.15);}
        input::placeholder{color:#7070a0;}
        .btn{width:100%;padding:13px;border:none;border-radius:10px;background:linear-gradient(90deg,var(--grad-1),var(--grad-2));color:#fff;font-size:16px;font-weight:600;cursor:pointer;}
        .btn:hover{box-shadow:0 8px 24px rgba(115,103,240,.4);transform:translateY(-1px);}
        .erro{background:rgba(234,84,85,.1);border:1px solid rgba(234,84,85,.4);color:#ff9a9b;padding:10px;border-radius:10px;font-size:13px;margin-bottom:16px;text-align:center;}
        .link{text-align:center;margin-top:16px;}
        .link a{color:var(--primary);text-decoration:none;font-size:13px;}
    </style>
</head>
<body>
<div class="card">
    <h1>🛡️ Área Admin</h1>
    <div class="badge"><span>Painel Administrativo</span></div>
    <p class="sub">Entre com seu e-mail e senha</p>
    <?php if ($erro): ?><div class="erro"><?= htmlspecialchars($erro) ?></div><?php endif; ?>
    <form method="POST">
        <label>E-mail</label>
        <input type="email" name="email" placeholder="seu@email.com" required autofocus>
        <label>Senha</label>
        <input type="password" name="senha" placeholder="Sua senha" required>
        <button type="submit" class="btn">Entrar</button>
    </form>
    <div class="link"><a href="../cliente/">→ Área do Cliente</a></div>
</div>
</body>
</html>
