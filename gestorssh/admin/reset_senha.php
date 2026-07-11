<?php
require_once("../pages/system/seguranca.php");
require_once("../pages/system/funcoes.php");
require_once("../pages/system/funcoes.system.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo '0';
    exit;
}

$email = isset($_POST['email']) ? trim($_POST['email']) : '';

if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo '2';
    exit;
}

$SQLAdmin = "SELECT * FROM admin WHERE email = :email LIMIT 1";
$SQLAdmin = $conn->prepare($SQLAdmin);
$SQLAdmin->bindParam(':email', $email, PDO::PARAM_STR);
$SQLAdmin->execute();

if ($SQLAdmin->rowCount() > 0) {
    $admin = $SQLAdmin->fetch();
    
    $novaSenha = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#'), 0, 10);
    
    $SQLUpdate = "UPDATE admin SET senha = :senha WHERE id_administrador = :id";
    $SQLUpdate = $conn->prepare($SQLUpdate);
    $SQLUpdate->bindParam(':senha', $novaSenha, PDO::PARAM_STR);
    $SQLUpdate->bindParam(':id', $admin['id_administrador'], PDO::PARAM_INT);
    $SQLUpdate->execute();
    
    $SQLsmtp = "SELECT * FROM smtp";
    $SQLsmtp = $conn->prepare($SQLsmtp);
    $SQLsmtp->execute();
    
    if ($SQLsmtp->rowCount() > 0) {
        $mp = $SQLsmtp->fetch();
        
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
        $mail->Body = "
        <div style='font-family:Montserrat,sans-serif;max-width:500px;margin:0 auto;background:#1a1b3a;border-radius:18px;padding:30px;color:#e0e0f0;border:1px solid rgba(115,103,240,0.3);'>
            <h2 style='color:#7367f0;text-align:center;'>🔑 Senha Resetada</h2>
            <p>Olá <b>{$admin['nome']}</b>,</p>
            <p>Sua senha foi redefinida com sucesso.</p>
            <div style='background:rgba(115,103,240,0.1);border:1px solid rgba(115,103,240,0.3);border-radius:10px;padding:15px;margin:15px 0;text-align:center;'>
                <p style='margin:0;color:#a0a0c0;'>Sua nova senha:</p>
                <p style='margin:5px 0;font-size:20px;color:#fff;font-weight:bold;letter-spacing:1px;'>{$novaSenha}</p>
            </div>
            <p style='font-size:12px;color:#7070a0;'>⚡ Recomendamos alterar a senha após o login.</p>
        </div>";
        
        if ($mail->Send()) {
            echo '1';
        } else {
            echo '3';
        }
    } else {
        echo '1';
    }
} else {
    echo '0';
}
