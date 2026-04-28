<?php

require_once "db.connection.php";

try {
    $stmt = $pdo->query("SELECT id, nome, email, data_nasc FROM usuarios ORDER BY id");
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
catch (PDOException $e) {
    error_log("Erro ao buscar usuarios: " . $e->getMessage());
    http_response_code(500);
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css?v=<?php echo (int)@filemtime(__DIR__ . DIRECTORY_SEPARATOR . 'styles.css'); ?>">
    <title>Admin - PHP</title>
</head>
<body>
    <section>
        <h1>CRUD.admin</h1>
        <a class="btn btn-outline" title="Novo usuário" href="usuarios.create.view.php">Novo usuário</a>
    </section>
    <div class="panel panel--elevated" role="region" aria-label="Lista de usuários">
        <div class="panel-header">
            <div>
                <h2>Usuários</h2>
                <p class="meta">Lista de usuários cadastrados no sistema.</p>
            </div>
            <span class="badge" aria-label="Dados do banco">MySQL</span>
        </div>

        <table>
            <thead title='Cabeçalho da tabela - ID, Nome, Email, Nascimento, Ações'>
                <tr>
                    <th id='id'>ID</th>
                    <th id='nome'>Nome</th>
                    <th id='email'>Email</th>
                    <th id='nascimento'>Nascimento</th>
                    <th id='acoes'>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!$usuarios): ?>
                    <tr>
                        <td style="grid-column: 1 / -1; padding: 1rem;">Nenhum usuário cadastrado.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td title='ID - <?php echo htmlspecialchars($usuario['id']); ?>'><?php echo htmlspecialchars($usuario['id']); ?></td>
                        <td title='Nome - <?php echo htmlspecialchars($usuario['nome']); ?>'><?php echo htmlspecialchars($usuario['nome']); ?></td>
                        <td title='Email - <?php echo htmlspecialchars($usuario['email']); ?>'><?php echo htmlspecialchars($usuario['email']); ?></td>
                        <td title='Nascimento - <?php echo htmlspecialchars($usuario['data_nasc']); ?>'><?php echo htmlspecialchars($usuario['data_nasc']); ?></td>
                        <td class="table-actions">
                            <a class="btn btn-neutral" title="Visualizar usuário" href="usuarios.view.view.php?id=<?php echo (int)$usuario['id']; ?>">Ver</a>
                            <a class="btn btn-primary" title='Editar usuário' href="usuarios.edit.view.php?id=<?php echo (int)$usuario['id']; ?>">Editar</a>
                            <a class="btn btn-danger" title='Excluir usuário' href="usuarios.delete.view.php?id=<?php echo (int)$usuario['id']; ?>">Excluir</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>
</html>