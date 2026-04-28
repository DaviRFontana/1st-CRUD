<?php
require_once "db.connection.php";

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    header("Location: index.php");
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT id, nome, email, data_nasc, senha FROM usuarios WHERE id = :id");
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
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="styles.css?v=<?php echo (int)@filemtime(__DIR__ . DIRECTORY_SEPARATOR . 'styles.css'); ?>" />
    <title>Ver usuário - CRUD.admin</title>
</head>
<body>
    <section>
        <h1>CRUD.admin</h1>
        <a class="btn btn-outline" title="Voltar" href="index.php">Voltar</a>
    </section>

    <div class="panel panel--elevated" role="region" aria-label="Detalhes do usuário">
        <div class="panel-header">
            <div>
                <h2>Ver usuário</h2>
                <p class="meta">Dados cadastrados do usuário selecionado.</p>
            </div>
            <span class="badge" aria-label="Somente leitura">Somente leitura</span>
        </div>

        <table class="user-view">
            <thead title="Cabeçalho da tabela - ID, Nome, Email, Nascimento, Senha">
                <tr>
                    <th id="id">ID</th>
                    <th id="nome">Nome</th>
                    <th id="email">Email</th>
                    <th id="nascimento">Nascimento</th>
                    <th id="senha">Senha</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td title="ID - <?php echo htmlspecialchars($usuario['id']); ?>"><?php echo htmlspecialchars($usuario['id']); ?></td>
                    <td title="Nome - <?php echo htmlspecialchars($usuario['nome']); ?>"><?php echo htmlspecialchars($usuario['nome']); ?></td>
                    <td title="Email - <?php echo htmlspecialchars($usuario['email']); ?>"><?php echo htmlspecialchars($usuario['email']); ?></td>
                    <td title="Nascimento - <?php echo htmlspecialchars($usuario['data_nasc']); ?>"><?php echo htmlspecialchars($usuario['data_nasc']); ?></td>
                    <td>
                        <span class="senha-preview" title="Senha (hash) - <?php echo htmlspecialchars($usuario['senha']); ?>">
                            <?php echo htmlspecialchars($usuario['senha']); ?>
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="divider" role="separator" aria-label="Separador"></div>

        <div class="actions">
            <a class="btn btn-neutral" href="index.php" title="Voltar para a lista">Voltar</a>
            <a class="btn btn-primary" href="usuarios.edit.view.php?id=<?php echo (int)$usuario['id']; ?>" title="Editar usuário">Editar</a>
        </div>
    </div>
</body>
</html>