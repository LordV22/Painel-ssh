<?php
require_once("funcoes.php");
require_once('pass.php');

$_SG['conectaServidor'] = true;
$_SG['abreSessao'] = true;
$_SG['caseSensitive'] = true;
$_SG['validaSempre'] = true;

// Suporte a variáveis de ambiente do Railway e localhost
if (getenv('MYSQLHOST')) {
    $_SG['servidor'] = getenv('MYSQLHOST');
    $_SG['usuario'] = getenv('MYSQLUSER');
    $_SG['senha'] = getenv('MYSQLPASSWORD');
    $_SG['banco'] = getenv('MYSQLDATABASE');
    $_SG['porta'] = getenv('MYSQLPORT') ?: '3306';
} elseif (getenv('DATABASE_URL')) {
    $dbUrl = parse_url(getenv('DATABASE_URL'));
    $_SG['servidor'] = $dbUrl['host'];
    $_SG['usuario'] = $dbUrl['user'];
    $_SG['senha'] = $dbUrl['pass'];
    $_SG['banco'] = ltrim($dbUrl['path'], '/');
    $_SG['porta'] = $dbUrl['port'] ?: '3306';
} else {
    $_SG['servidor'] = 'localhost';
    $_SG['usuario'] = 'root';
    $_SG['senha'] = $pass;
    $_SG['banco'] = 'sshplus';
    $_SG['porta'] = '3306';
}

$_SG['paginaLogin'] = 'login.php';
$_SG['paginaBloquear'] = 'tela-bloqueada.php';

if ($_SG['conectaServidor'] == true) {
    try {
        $pdoOpts = array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );
        if (defined('PDO::MYSQL_ATTR_INIT_COMMAND')) {
            $pdoOpts[PDO::MYSQL_ATTR_INIT_COMMAND] = "SET NAMES utf8";
        }
        $conn = new PDO(
            'mysql:host='.$_SG['servidor'].';port='.$_SG['porta'].';dbname='.$_SG['banco'].';charset=utf8',
            $_SG['usuario'],
            $_SG['senha'],
            $pdoOpts
        );
    } catch(PDOException $e) {
        echo 'ERROR: ' . $e->getMessage();
    }
}

function my_Sql_regcase($str)
{
    $res = "";
    $chars = str_split($str);
    foreach ($chars as $char) {
        if (preg_match("/[A-Za-z]/", $char)) {
            $res .= "[" . mb_strtoupper($char, 'UTF-8') . mb_strtolower($char, 'UTF-8') . "]";
        } else {
            $res .= $char;
        }
    }
    return $res;
}

function sql_injector($sql)
{
    $seg = preg_replace(my_Sql_regcase("/(from|select|insert|delete|where|drop table|show tables|#|\*|--|\\\\)/"),"",$sql);
    $seg = trim($seg);
    $seg = strip_tags($seg);
    $seg = addslashes($seg);
    return $seg;
}

function pega_ip() {
    $ipaddress = '';
    if (getenv('HTTP_CLIENT_IP'))
        $ipaddress = getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED'))
        $ipaddress = getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR'))
        $ipaddress = getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED'))
        $ipaddress = getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR'))
        $ipaddress = getenv('REMOTE_ADDR');
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}

function validaUsuario($usuario, $senha,$tipo) {
    global $_SG;
    session_start();
    $cS = ($_SG['caseSensitive']) ? 'BINARY' : '';
    $login_usuario = addslashes($usuario);
    $senha_usuario = addslashes($senha);
    if($tipo=="admin"){
        $sql = "SELECT * FROM admin WHERE login = '".$login_usuario."' AND senha = '".$senha_usuario."' LIMIT 1";
    }else{
        $sql = "SELECT * FROM usuario WHERE login = '".$login_usuario."' AND senha = '".$senha_usuario."' LIMIT 1";
    }
    global $conn;
    $sql = $conn->prepare($sql);
    $sql->execute();
    $resultado = $sql->fetch();
    if (empty($resultado)) {
        return false;
    } else {
        if($tipo=="admin"){
            $_SESSION['usuarioID'] = $resultado['id_administrador'];
            $_SESSION['usuarioNome'] = $resultado['nome'];
            $_SESSION['tipo'] = 'admin';
            $_SESSION['usuarioLogin'] = $resultado['login'];
            $_SESSION['usuarioSenha'] = $resultado['senha'];
        }else{
            $_SESSION['usuarioID'] = $resultado['id_usuario'];
            $_SESSION['usuarioNome'] = $resultado['nome'];
            $_SESSION['usuarioLogin'] = $resultado['login'];
            $_SESSION['usuarioSenha'] = $resultado['senha'];
            $_SESSION['tipo'] = 'user';
        }
        return true;
    }
}

function protegePagina($tipo) {
    global $_SG;
    session_start();
    if (!isset($_SESSION['usuarioID']) or !isset($_SESSION['usuarioNome'])) {
        expulsaVisitante();
    } else if (!isset($_SESSION['usuarioID']) or !isset($_SESSION['usuarioNome'])) {
        if ($_SG['validaSempre'] == true) {
            if($_SESSION['tipo']=="admin"){
                if (!validaUsuario($_SESSION['usuarioLogin'], $_SESSION['usuarioSenha'], "admin")) {
                    expulsaVisitante();
                }
            }else{
                if (!validaUsuario($_SESSION['usuarioLogin'], $_SESSION['usuarioSenha'], "user")) {
                    expulsaVisitante();
                }
            }
        }
    }
}

function expulsaVisitante() {
    global $_SG;
    session_start();
    unset($_SESSION['usuarioID'], $_SESSION['usuarioNome'], $_SESSION['usuarioLogin'], $_SESSION['usuarioSenha']);
    header("Location: index.php");
}

function expulsaSair() {
    session_start();
    global $_SG;
    unset($_SESSION['usuarioID'], $_SESSION['usuarioNome'], $_SESSION['usuarioLogin'], $_SESSION['usuarioSenha']);
    session_destroy();
    header("Location: ../index.php");
}
?>
