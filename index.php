<?php
// Verifica se está na raiz e redireciona para o gestorssh
if (strpos($_SERVER['REQUEST_URI'], '/gestorssh/') === false) {
    header("Location: /gestorssh/");
    exit;
}
