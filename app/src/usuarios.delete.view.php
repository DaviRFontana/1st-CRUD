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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = :id");
        $stmt->execute([':id' => $id]);
        header("Location: index.php");
        exit;
    } catch (PDOException $e) {
        error_log("Erro ao excluir usuário: " . $e->getMessage());
        http_response_code(500);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="styles.css?v=<?php echo (int)@filemtime(__DIR__ . DIRECTORY_SEPARATOR . 'styles.css'); ?>" />
    <title>Excluir usuário - CRUD.admin</title>
</head>
<body>
    <section>
        <h1>CRUD.admin</h1>
        <a class="btn btn-outline" title="Voltar" href="index.php">Voltar</a>
    </section>

    <div class="panel panel--elevated" role="alertdialog" aria-label="Confirmação de exclusão">
        <div class="panel-header">
            <div>
                <h2>Excluir usuário</h2>
                <p class="meta">Tem certeza que deseja excluir o usuário abaixo? Essa ação não pode ser desfeita.</p>
            </div>
            <span class="badge badge--danger" aria-label="Atenção">Atenção</span>
        </div>

        <table>
            <thead title="Cabeçalho da tabela - ID, Nome, Email, Nascimento">
                <tr>
                    <th id="id">ID</th>
                    <th id="nome">Nome</th>
                    <th id="email">Email</th>
                    <th id="nascimento">Nascimento</th>
                    <th id="acoes">Ações</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td title="ID - <?php echo htmlspecialchars($usuario['id']); ?>"><?php echo htmlspecialchars($usuario['id']); ?></td>
                    <td title="Nome - <?php echo htmlspecialchars($usuario['nome']); ?>"><?php echo htmlspecialchars($usuario['nome']); ?></td>
                    <td title="Email - <?php echo htmlspecialchars($usuario['email']); ?>"><?php echo htmlspecialchars($usuario['email']); ?></td>
                    <td title="Nascimento - <?php echo htmlspecialchars($usuario['data_nasc']); ?>"><?php echo htmlspecialchars($usuario['data_nasc']); ?></td>
                    <td class="table-actions">
                        <a class="btn btn-primary" title="Editar usuário" href="usuarios.edit.view.php?id=<?php echo (int)$usuario['id']; ?>">Editar</a>
                        <a class="btn btn-neutral" title="Visualizar usuário" href="usuarios.view.view.php?id=<?php echo (int)$usuario['id']; ?>">Ver</a>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="divider" role="separator" aria-label="Separador"></div>

        <div class="actions actions--spread">
            <a class="btn btn-neutral" href="index.php" title="Cancelar exclusão">Cancelar</a>
            <form method="post" action="usuarios.delete.view.php?id=<?php echo (int)$usuario['id']; ?>">
                <input type="hidden" name="id" value="<?php echo (int)$usuario['id']; ?>" />
                <button class="btn btn-danger" type="submit" title="Confirmar exclusão">Confirmar exclusão</button>
            </form>
        </div>
    </div>
</body>
</html>