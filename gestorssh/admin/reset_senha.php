<?php
require_once("../pages/system/seguranca.php");
require_once("../pages/system/funcoes.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo '0'; exit; }

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) { echo '2'; exit; }

if ($conn === null) { echo '3'; exit; }

$q = $conn->query("SELECT * FROM admin WHERE email = " . $conn->quote($email) . " LIMIT 1");

if ($q && $q->rowCount() > 0) {
    $admin = $q->fetch(PDO::FETCH_ASSOC);
    $novaSenha = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#'), 0, 10);
    $conn->exec("UPDATE admin SET senha = " . $conn->quote($novaSenha) . " WHERE id_administrador = " . intval($admin['id_administrador']));

    $SQLsmtp = $conn->query("SELECT * FROM smtp");
    if ($SQLsmtp && $SQLsmtp->rowCount() > 0) {
        $mp = $SQLsmtp->fetch(PDO::FETCH_ASSOC);
        require_once("../phpmailer/class.phpmailer.php");
        require_once("../phpmailer/class.smtp.php");
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->SMTPSecure = $mp['ssl_secure'];
        $mail->Host = $mp['servidor'];
        $mail->Port = $mp['porta'];
        $mail->SMTPDebug = 0;
        $mail->SMTPAuth = true;
        $mail->Username = $mp['email'];
        $mail->Password = $mp['senha'];
        $mail->From = $mp['email'];
        $mail->FromName = "Suporte - Painel";
        $mail->AddAddress($admin['email'], $admin['nome']);
        $mail->IsHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = "Recuperação de Senha - Painel Admin";
        $mail->Body = "<div style='font-family:Montserrat,sans-serif;max-width:500px;margin:0 auto;background:#1a1b3a;border-radius:18px;padding:30px;color:#e0e0f0;border:1px solid #a0a0c0;'><h2 style='color:#7367f0;text-align:center;'>🔑 Senha Resetada</h2><p>Olá <b>{$admin['nome']}</b>,</p><p>Sua senha foi redefinida com sucesso.</p><div style='background:rgba(115,103,240,0.1);border:1px solid #a0a0c0;border-radius:10px;padding:15px;text-align:center;'><p style='margin:0;color:#a0a0c0;'>Sua nova senha:</p><p style='font-size:20px;color:#fff;font-weight:bold;'>{$novaSenha}</p></div><p style='font-size:12px;color:#a0a0c0;'>⚡ Recomendamos alterar a senha após o login.</p></div>";
        if ($mail->Send()) { echo '1'; } else { echo '3'; }
    } else { echo '1'; }
} else { echo '0'; }
?>
