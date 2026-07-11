<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Painel de gerenciamento vpn">
    <meta name="keywords" content="vpn, ssh, user, servidor">
    <title>EMPRESA 🚀</title>
    <link rel="shortcut icon" href="app-assets/images/ico/favicon.ico">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        :root {
            --primary: #7367f0;
            --primary-dark: #5e50ee;
            --grad-1: #7367f0;
            --grad-2: #9e95f5;
            --bg: #0f1021;
            --card-bg: #1a1b3a;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Montserrat', sans-serif;
            background: linear-gradient(135deg, #0f1021 0%, #1a1b3a 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #e0e0f0;
        }
        .login-card {
            background: var(--card-bg);
            border-radius: 18px;
            padding: 40px 32px;
            width: 100%;
            max-width: 400px;
            box-shadow: 0 20px 60px rgba(115, 103, 240, 0.25);
            border: 1px solid rgba(115, 103, 240, 0.2);
        }
        .brand {
            text-align: center;
            margin-bottom: 28px;
        }
        .brand h1 {
            font-size: 26px;
            font-weight: 700;
            color: #fff;
            letter-spacing: 0.5px;
        }
        .badge-empresa {
            display: inline-block;
            background: linear-gradient(90deg, var(--grad-1), var(--grad-2));
            padding: 4px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            margin-top: 8px;
        }
        .welcome {
            text-align: center;
            margin-bottom: 24px;
        }
        .welcome h2 {
            font-size: 20px;
            color: var(--primary);
            margin-bottom: 6px;
        }
        .welcome p {
            font-size: 14px;
            color: #a0a0c0;
        }
        .form-group {
            margin-bottom: 18px;
        }
        .form-label {
            display: block;
            font-size: 13px;
            font-weight: 500;
            margin-bottom: 8px;
            color: #c0c0e0;
        }
        .input-wrap {
            position: relative;
        }
        .input-wrap .icon {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
        }
        .form-control {
            width: 100%;
            padding: 12px 14px 12px 44px;
            border-radius: 10px;
            border: 1px solid rgba(115, 103, 240, 0.3);
            background: rgba(255, 255, 255, 0.04);
            color: #fff;
            font-size: 15px;
            transition: all 0.2s;
        }
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            background: rgba(115, 103, 240, 0.08);
            box-shadow: 0 0 0 3px rgba(115, 103, 240, 0.15);
        }
        .form-control::placeholder { color: #7070a0; }
        .row-between {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 18px;
            font-size: 13px;
        }
        .remember {
            display: flex;
            align-items: center;
            gap: 8px;
            color: #c0c0e0;
            cursor: pointer;
        }
        .remember input { accent-color: var(--primary); }
        .forgot {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }
        .forgot:hover { text-decoration: underline; }
        .btn-login {
            width: 100%;
            padding: 13px;
            border: none;
            border-radius: 10px;
            background: linear-gradient(90deg, var(--grad-1), var(--grad-2));
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.1s, box-shadow 0.2s;
        }
        .btn-login:hover {
            box-shadow: 0 8px 24px rgba(115, 103, 240, 0.4);
            transform: translateY(-1px);
        }
        .btn-login:active { transform: translateY(0); }
        .divider {
            text-align: center;
            margin: 22px 0;
            position: relative;
            color: #7070a0;
            font-size: 13px;
        }
        .divider::before,
        .divider::after {
            content: "";
            position: absolute;
            top: 50%;
            width: 40%;
            height: 1px;
            background: rgba(115, 103, 240, 0.2);
        }
        .divider::before { left: 0; }
        .divider::after { right: 0; }
        .admin-link {
            text-align: center;
        }
        .admin-link a {
            display: inline-block;
            padding: 10px 28px;
            border-radius: 10px;
            border: 1px solid rgba(115, 103, 240, 0.3);
            color: #c0c0e0;
            text-decoration: none;
            font-size: 14px;
            font-weight: 500;
            transition: all 0.2s;
        }
        .admin-link a:hover {
            border-color: var(--primary);
            color: #fff;
            background: rgba(115, 103, 240, 0.1);
        }
        .user-link {
            text-align: center;
            margin-top: 12px;
        }
        .user-link a {
            color: #7070a0;
            text-decoration: none;
            font-size: 13px;
        }
        .user-link a:hover {
            color: var(--primary);
        }
        .alert-error {
            display: none;
            background: rgba(234, 84, 85, 0.1);
            border: 1px solid rgba(234, 84, 85, 0.4);
            color: #ff9a9b;
            padding: 10px 14px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 16px;
            text-align: center;
        }
        .modal-backdrop {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.6);
            z-index: 9999;
            align-items: center;
            justify-content: center;
        }
        .modal-backdrop.show {
            display: flex;
        }
        .modal-box {
            background: var(--card-bg);
            border-radius: 18px;
            padding: 32px;
            width: 90%;
            max-width: 380px;
            border: 1px solid rgba(115, 103, 240, 0.2);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
        }
        .modal-box h4 {
            color: #fff;
            margin-bottom: 20px;
            text-align: center;
        }
        .modal-actions {
            display: flex;
            gap: 10px;
            margin-top: 16px;
        }
        .btn-cancel, .btn-confirm {
            flex: 1;
            padding: 10px;
            border-radius: 10px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            font-size: 14px;
        }
        .btn-cancel {
            background: rgba(255, 255, 255, 0.05);
            color: #c0c0e0;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .btn-confirm {
            background: linear-gradient(90deg, var(--grad-1), var(--grad-2));
            color: #fff;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="brand">
        <h1>EMPRESA 🚀</h1>
        <span class="badge-empresa">Painel Administrativo</span>
    </div>

    <div class="welcome">
        <h2>👋 Bem-vindo Admin!</h2>
        <p>Entre com seu usuário e senha</p>
    </div>

    <div class="alert alert-error" id="errorBox"></div>

    <div class="form-group">
        <label class="form-label" for="login">Usuário</label>
        <div class="input-wrap">
            <span class="icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
            </span>
            <input type="text" class="form-control" id="login" name="login" placeholder="usuário de acesso" required>
        </div>
    </div>

    <div class="form-group">
        <label class="form-label" for="senha">Senha</label>
        <div class="input-wrap">
            <span class="icon">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
            </span>
            <input type="password" class="form-control" id="senha" name="senha" placeholder="senha de acesso" required>
        </div>
    </div>

    <div class="row-between">
        <label class="remember">
            <input type="checkbox" id="customCheck4"> Lembrar
        </label>
        <a href="#" class="forgot" id="to-recover">Esqueceu a senha?</a>
    </div>

    <button type="button" id="mybtn" class="btn-login">Entrar</button>

    <div class="divider">ou</div>

    <div class="user-link">
        <a href="login.php">ENTRAR COMO USUÁRIO</a>
    </div>
</div>

<!-- Modal Recuperar -->
<div class="modal-backdrop" id="recoverModal">
    <div class="modal-box">
        <h4>🔑 Recuperar Acesso</h4>
        <form name="recupera" action="#" method="post" id="resetForm">
            <div class="form-group">
                <label class="form-label" for="email">Informe o E-mail</label>
                <input type="email" class="form-control" name="email" placeholder="Digite seu e-mail" required style="padding-left:14px;">
            </div>
            <div class="modal-actions">
                <button type="button" class="btn-cancel" id="closeModal">Cancelar</button>
                <button type="button" class="btn-confirm" id="resetBtn">Confirmar</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        $("#mybtn").click(function() {
            var username = $("#login").val().trim();
            var password = $("#senha").val().trim();

            if (username === "" || password === "") {
                $("#errorBox").text("Preencha usuário e senha.").show();
                return;
            }

            $("#errorBox").hide();

            $.ajax({
                url: 'admin/validacao.php',
                type: 'post',
                data: { username: username, password: password },
                success: function(response) {
                    if (response == 1) {
                        window.location = "admin/home.php";
                    } else {
                        Swal.fire({
                            title: 'Usuário ou senha incorreto!',
                            icon: 'error',
                            confirmButtonColor: '#7367f0',
                            confirmButtonText: 'Ok'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'Erro de conexão',
                        icon: 'error',
                        confirmButtonColor: '#7367f0',
                        confirmButtonText: 'Ok'
                    });
                }
            });
        });

        $("#senha").keypress(function(e) {
            if (e.which === 13) $("#mybtn").click();
        });

        $("#to-recover").click(function(e) {
            e.preventDefault();
            $("#recoverModal").addClass("show");
        });
        $("#closeModal").click(function() {
            $("#recoverModal").removeClass("show");
        });
        $("#resetBtn").click(function() {
            var email = $("input[name=email]").val().trim();
            if (email === "") {
                $("#errorBox").text("Digite seu e-mail.").show();
                return;
            }
            $("#errorBox").hide();
            $(this).text("Enviando...").prop("disabled", true);
            $.ajax({
                url: "admin/reset_senha.php",
                type: "post",
                data: { email: email },
                success: function(response) {
                    if (response == "1") {
                        $("#recoverModal").removeClass("show");
                        Swal.fire({
                            title: "Senha enviada!",
                            text: "Verifique seu e-mail. Uma nova senha foi enviada.",
                            icon: "success",
                            confirmButtonColor: "#7367f0",
                            confirmButtonText: "Ok"
                        });
                    } else if (response == "2") {
                        $("#errorBox").text("E-mail inválido.").show();
                        $("#resetBtn").text("Confirmar").prop("disabled", false);
                    } else if (response == "3") {
                        $("#errorBox").text("Erro ao enviar e-mail. Tente novamente.").show();
                        $("#resetBtn").text("Confirmar").prop("disabled", false);
                    } else {
                        $("#errorBox").text("E-mail não encontrado.").show();
                        $("#resetBtn").text("Confirmar").prop("disabled", false);
                    }
                },
                error: function() {
                    $("#errorBox").text("Erro de conexão.").show();
                    $("#resetBtn").text("Confirmar").prop("disabled", false);
                }
            });
        });

        $("#recoverModal").click(function(e) {
            if (e.target === this) $(this).removeClass("show");
        });
    });
</script>

</body>
</html>
