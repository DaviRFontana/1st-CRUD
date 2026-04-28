<?php
require_once "db.connection.php";

$errors = [];
$nome = '';
$email = '';
$data_nasc = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $data_nasc = trim($_POST['data_nasc'] ?? '');
    $senha = (string)($_POST['senha'] ?? '');
    $senha2 = (string)($_POST['senha2'] ?? '');

    if ($nome === '' || mb_strlen($nome) < 3) {
        $errors[] = "Nome é obrigatório (mínimo 3 caracteres).";
    }

    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email inválido.";
    }

    if ($data_nasc === '') {
        $errors[] = "Nascimento é obrigatório.";
    }

    if ($senha === '' || strlen($senha) < 8) {
        $errors[] = "Senha é obrigatória (mínimo 8 caracteres).";
    }

    if ($senha !== $senha2) {
        $errors[] = "As senhas não conferem.";
    }

    if (!$errors) {
        try {
            $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, data_nasc, senha) VALUES (:nome, :email, :data_nasc, :senha)");
            $stmt->execute([
                ':nome' => $nome,
                ':email' => $email,
                ':data_nasc' => $data_nasc,
                ':senha' => $senha_hash,
            ]);

            header("Location: index.php");
            exit;
        } catch (PDOException $e) {
            error_log("Erro ao criar usuário: " . $e->getMessage());
            http_response_code(500);
        }
    } else {
        http_response_code(422);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="styles.css?v=<?php echo (int)@filemtime(__DIR__ . DIRECTORY_SEPARATOR . 'styles.css'); ?>" />
    <title>Novo usuário - CRUD.admin</title>
</head>
<body>
    <section>
        <h1>CRUD.admin</h1>
        <a class="btn btn-outline" title="Voltar" href="index.php">Voltar</a>
    </section>

    <div class="panel panel--elevated" role="region" aria-label="Formulário de cadastro de usuário">
        <div class="panel-header">
            <div>
                <h2>Novo usuário</h2>
                <p class="meta">Cadastre um usuário com dados básicos e credenciais.</p>
            </div>
                <span class="badge" aria-label="Modo dinâmico (com backend)">MySQL</span>
        </div>

        <form class="form" action="usuarios.create.view.php" method="post">
            <div class="form-grid" role="group" aria-label="Dados pessoais">
                <div class="field">
                    <label for="nome">Nome</label>
                    <input id="nome" name="nome" type="text" placeholder="Ex.: João da Silva" autocomplete="name" required minlength="3" value="<?php echo htmlspecialchars($nome); ?>" />
                    <p class="hint">Como o usuário será exibido na lista.</p>
                </div>

                <div class="field">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" placeholder="exemplo@dominio.com" autocomplete="email" required value="<?php echo htmlspecialchars($email); ?>" />
                    <p class="hint">Usado para login e notificações.</p>
                </div>

                <div class="field">
                    <label for="data_nasc">Nascimento</label>
                    <input id="data_nasc" name="data_nasc" type="date" required value="<?php echo htmlspecialchars($data_nasc); ?>" />
                    <p class="hint">Formato padrão do navegador.</p>
                </div>
            </div>

            <div class="divider" role="separator" aria-label="Separador"></div>

            <div class="form-grid" role="group" aria-label="Credenciais">
                <div class="field">
                    <label for="senha">Senha</label>
                    <input id="senha" name="senha" type="password" placeholder="Mínimo 8 caracteres" autocomplete="new-password" minlength="8" required />
                    <p class="hint">Evite reutilizar senhas. Use uma combinação forte.</p>
                </div>

                <div class="field">
                    <label for="senha2">Confirmar senha</label>
                    <input id="senha2" name="senha2" type="password" placeholder="Repita a senha" autocomplete="new-password" minlength="8" required />
                    <p class="hint">Os dois campos devem ser iguais.</p>
                </div>
            </div>

            <div class="actions actions--spread">
                <a class="btn btn-neutral" href="index.php" title="Cancelar">Cancelar</a>
                <div class="actions">
                    <button class="btn btn-neutral" type="reset" title="Limpar campos">Limpar</button>
                    <button class="btn btn-primary" type="submit" title="Salvar">
                        Criar usuário
                    </button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>