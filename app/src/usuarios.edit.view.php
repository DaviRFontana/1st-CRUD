<?php
require_once "db.connection.php";

$errors = [];
$id = (int)($_GET['id'] ?? ($_POST['id'] ?? 0));
if ($id <= 0) {
    header("Location: index.php");
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, nome, email, data_nasc FROM usuarios WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $usuario = $stmt->fetch();

    if (!$usuario) {
        header("Location: index.php");
        exit;
    }
} catch (PDOException $e) {
    error_log("Erro ao buscar usuário: " . $e->getMessage());
    http_response_code(500);
    exit;
}

$nome = (string)$usuario['nome'];
$email = (string)$usuario['email'];
$data_nasc = (string)$usuario['data_nasc'];

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

    $vaiAtualizarSenha = ($senha !== '' || $senha2 !== '');
    if ($vaiAtualizarSenha) {
        if (strlen($senha) < 8) {
            $errors[] = "Nova senha deve ter no mínimo 8 caracteres.";
        }
        if ($senha !== $senha2) {
            $errors[] = "As senhas não conferem.";
        }
    }

    if (!$errors) {
        try {
            if ($vaiAtualizarSenha) {
                $senha_hash = password_hash($senha, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE usuarios SET nome = :nome, email = :email, data_nasc = :data_nasc, senha = :senha WHERE id = :id");
                $stmt->execute([
                    ':nome' => $nome,
                    ':email' => $email,
                    ':data_nasc' => $data_nasc,
                    ':senha' => $senha_hash,
                    ':id' => $id,
                ]);
            } else {
                $stmt = $pdo->prepare("UPDATE usuarios SET nome = :nome, email = :email, data_nasc = :data_nasc WHERE id = :id");
                $stmt->execute([
                    ':nome' => $nome,
                    ':email' => $email,
                    ':data_nasc' => $data_nasc,
                    ':id' => $id,
                ]);
            }

            header("Location: usuarios.view.view.php?id=" . $id);
            exit;
        } catch (PDOException $e) {
            error_log("Erro ao salvar usuário: " . $e->getMessage());
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
    <title>Editar usuário - CRUD.admin</title>
</head>
<body>
    <section>
        <h1>CRUD.admin</h1>
        <a class="btn btn-outline" title="Voltar" href="index.php">Voltar</a>
    </section>

    <div class="panel panel--elevated" role="region" aria-label="Formulário de edição de usuário">
        <div class="panel-header">
            <div>
                <h2>Editar usuário</h2>
                <p class="meta">Ajuste os valores e clique em salvar.</p>
            </div>
                <span class="badge" aria-label="Modo dinâmico (com backend)">MySQL</span>
        </div>

        <form class="form" action="usuarios.edit.view.php?id=<?php echo (int)$id; ?>" method="post">
            <div class="form-grid">
                <div class="field">
                    <label for="id">ID</label>
                    <input id="id" name="id" type="text" value="<?php echo (int)$id; ?>" readonly />
                    <p class="hint">Identificador somente leitura.</p>
                </div>

                <div class="field">
                    <label for="nome">Nome</label>
                    <input id="nome" name="nome" type="text" value="<?php echo htmlspecialchars($nome); ?>" autocomplete="name" required minlength="3" />
                    <p class="hint">Nome público exibido no sistema.</p>
                </div>

                <div class="field">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" value="<?php echo htmlspecialchars($email); ?>" autocomplete="email" required />
                    <p class="hint">Conferir antes de salvar.</p>
                </div>

                <div class="field">
                    <label for="data_nasc">Nascimento</label>
                    <input id="data_nasc" name="data_nasc" type="date" value="<?php echo htmlspecialchars($data_nasc); ?>" required />
                    <p class="hint">Use o seletor do navegador.</p>
                </div>
            </div>

            <div class="divider" role="separator" aria-label="Separador"></div>

            <div class="form-grid" role="group" aria-label="Atualizar senha (opcional)">
                <div class="field">
                    <label for="senha">Nova senha (opcional)</label>
                    <input id="senha" name="senha" type="password" placeholder="Deixe em branco para manter" autocomplete="new-password" minlength="8" />
                    <p class="hint">Se preencher, precisa ter no mínimo 8 caracteres.</p>
                </div>

                <div class="field">
                    <label for="senha2">Confirmar nova senha</label>
                    <input id="senha2" name="senha2" type="password" placeholder="Repita a nova senha" autocomplete="new-password" minlength="8" />
                    <p class="hint">Deixe em branco para manter a senha atual.</p>
                </div>
            </div>

            <div class="actions actions--spread">
                <a class="btn btn-neutral" href="index.php" title="Cancelar">Cancelar</a>
                <div class="actions">
                    <button class="btn btn-neutral" type="reset" title="Desfazer alterações">Desfazer</button>
                    <button class="btn btn-primary" type="submit" title="Salvar">Salvar</button>
                </div>
            </div>
        </form>
    </div>
</body>
</html>