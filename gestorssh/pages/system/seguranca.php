<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once("funcoes.php");
require_once('pass.php');

$_SG['conectaServidor'] = true;
$_SG['abreSessao'] = true;
$_SG['caseSensitive'] = true;
$_SG['validaSempre'] = true;

if (getenv('MYSQLHOST')) {
    $_SG['servidor'] = getenv('MYSQLHOST');
    $_SG['usuario'] = getenv('MYSQLUSER');
    $_SG['senha'] = getenv('MYSQLPASSWORD');
    $_SG['banco'] = getenv('MYSQLDATABASE');
    $_SG['porta'] = getenv('MYSQLPORT') ?: '3306';
} else {
    $_SG['servidor'] = 'mysql.railway.internal';
    $_SG['usuario'] = 'root';
    $_SG['senha'] = 'IlvTDnpJyMitmGnTMJjHRTVjCWFRAxFG';
    $_SG['banco'] = 'sshplus';
    $_SG['porta'] = '3306';
}

$_SG['paginaLogin'] = 'login.php';
$_SG['paginaBloquear'] = 'tela-bloqueada.php';

$conn = null;
if ($_SG['conectaServidor'] == true) {
    $conn = @new mysqli($_SG['servidor'], $_SG['usuario'], $_SG['senha'], $_SG['banco'], $_SG['porta']);
    if ($conn->connect_error) {
        error_log("MySQL ERROR: " . $conn->connect_error);
        $conn = null;
    } else {
        $conn->set_charset("utf8mb4");
    }
}

function my_Sql_regcase($str) {
    $res = "";
    $chars = str_split($str);
    foreach ($chars as $char) {
        if (preg_match("/[A-Za-z]/", $char)) {
            $res .= "[" . mb_strtoupper($char, 'UTF-8') . mb_strtolower($char, 'UTF-8') . "]";
        } else { $res .= $char; }
    }
    return $res;
}

function sql_injector($sql) {
    $seg = preg_replace(my_Sql_regcase("/(from|select|insert|delete|where|drop table|show tables|#|\*|--|\\\\)/"),"",$sql);
    $seg = trim($seg); $seg = strip_tags($seg); $seg = addslashes($seg);
    return $seg;
}

function pega_ip() {
    if (getenv('HTTP_CLIENT_IP')) return getenv('HTTP_CLIENT_IP');
    else if(getenv('HTTP_X_FORWARDED_FOR')) return getenv('HTTP_X_FORWARDED_FOR');
    else if(getenv('HTTP_X_FORWARDED')) return getenv('HTTP_X_FORWARDED');
    else if(getenv('HTTP_FORWARDED_FOR')) return getenv('HTTP_FORWARDED_FOR');
    else if(getenv('HTTP_FORWARDED')) return getenv('HTTP_FORWARDED');
    else if(getenv('REMOTE_ADDR')) return getenv('REMOTE_ADDR');
    else return 'UNKNOWN';
}

function validaUsuario($usuario, $senha, $tipo) {
    global $conn;
    global $_SG;

    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    if ($conn === null) {
        error_log("DB connection is null in validaUsuario");
        return false;
    }

    $login_usuario = $conn->real_escape_string($usuario);
    $senha_usuario = $conn->real_escape_string($senha);

    if ($tipo == "admin") {
        if (filter_var($login_usuario, FILTER_VALIDATE_EMAIL)) {
            $sql = "SELECT * FROM admin WHERE email = '$login_usuario' AND senha = '$senha_usuario' LIMIT 1";
        } else {
            $sql = "SELECT * FROM admin WHERE login = '$login_usuario' AND senha = '$senha_usuario' LIMIT 1";
        }
    } else {
        if (filter_var($login_usuario, FILTER_VALIDATE_EMAIL)) {
            $sql = "SELECT * FROM usuario WHERE email = '$login_usuario' AND senha = '$senha_usuario' LIMIT 1";
        } else {
            $sql = "SELECT * FROM usuario WHERE login = '$login_usuario' AND senha = '$senha_usuario' LIMIT 1";
        }
    }

    $result = $conn->query($sql);
    if (!$result) {
        error_log("Query error: " . $conn->error);
        return false;
    }
    $resultado = $result->fetch_assoc();

    if (empty($resultado)) {
        return false;
    } else {
        if ($tipo == "admin") {
            $_SESSION['usuarioID'] = $resultado['id_administrador'];
            $_SESSION['usuarioNome'] = $resultado['nome'];
            $_SESSION['tipo'] = 'admin';
            $_SESSION['usuarioLogin'] = $resultado['login'];
            $_SESSION['usuarioSenha'] = $resultado['senha'];
        } else {
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
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['usuarioID']) or !isset($_SESSION['usuarioNome'])) {
        expulsaVisitante();
    }
}

function expulsaVisitante() {
    global $_SG;
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    unset($_SESSION['usuarioID'], $_SESSION['usuarioNome'], $_SESSION['usuarioLogin'], $_SESSION['usuarioSenha']);
    header("Location: index.php");
}

function expulsaSair() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    global $_SG;
    unset($_SESSION['usuarioID'], $_SESSION['usuarioNome'], $_SESSION['usuarioLogin'], $_SESSION['usuarioSenha']);
    session_destroy();
    header("Location: ../index.php");
}
?>
