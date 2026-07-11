<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
require_once("../pages/system/funcoes.php");
require_once("../pages/system/seguranca.php");

$usuario = $_POST['username'];
$senha = $_POST['password'];

if (empty($usuario)) {
  echo '0';
} elseif (empty($senha)) {
  echo '0';
} else {
  if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = (isset($usuario)) ? $usuario : '';
    $senha = (isset($senha)) ? $senha : '';
    if (validaUsuario($usuario, $senha, "admin") == true) {
      echo '1';
    } else {
      echo '0';
    }
  }
}
