<?php
require_once("../../../pages/system/seguranca.php");
require_once("../../../pages/system/config.php");
require_once("../../../pages/system/classe.ssh.php");
require_once("../../../pages/system/funcoes.system.php");

protegePagina("admin");

if (isset($_GET["id"])) {
    $id = anti_sql_injection($_GET['id']);

    $SQLSubSSH = "SELECT * FROM ovpn where id='" . $id . "'";
    $SQLSubSSH = $conn->prepare($SQLSubSSH);
    $SQLSubSSH->execute();
    $conta = $SQLSubSSH->rowCount();
    
    if ($conta > 0) {
        $arquivo = $SQLSubSSH->fetch();
        $file = $arquivo['arquivo'];

        if (file_exists("../../admin/pages/servidor/ovpn/" . $file)) {
            $separa = explode('.', $file);
            $novoNome = $separa[0];
            $local = "../../admin/pages/servidor/ovpn/" . $file;
            // Configuramos os headers que serão enviados para o browser
            header('Cache-control: private');
            header('Content-Type: application/octet-stream');
            header('Content-Length: ' . filesize($local));
            header('Content-Disposition: filename=' . $novoNome);
            header("Content-Disposition: attachment; filename=" . basename($local));
            // Envia o arquivo para o cliente
            readfile($local);
        } else {
            echo myalertuser('error', 'Arquivo APK não foi encontrado no servidor', '../../home.php?page=servidor/listar');
        }
    } else {
        echo myalertuser('error', 'Arquivo não encontrado no banco de dados', '../../home.php?page=servidor/listar');
    }
}
?>
