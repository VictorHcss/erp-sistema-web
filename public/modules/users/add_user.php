<?php
require_once __DIR__ . '/../../../includes/auth.php';
require_once __DIR__ . '/../../../config/database.php';

$basePath = '../../';
require_once __DIR__ . '/../../../includes/header.php';

$erro = '';
$sucesso = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? 'user';

    if (!$name || !$email || !$pass) {
        $erro = 'Preencha todos os campos obrigatórios.';
    } else {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $erro = 'Este e-mail já está cadastrado.';
        } else {
            $hash = password_hash($pass, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare("
                INSERT INTO users (name, email, password, role, company_id)
                VALUES (?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $name,
                $email,
                $hash,
                $role,
                getCompanyId()
            ]);

            $sucesso = 'Usuário cadastrado com sucesso!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Usuário</title>
    <link rel="stylesheet" href="<?= $basePath ?>css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <main>
        <h2><i class="fas fa-user-plus"></i> Novo Usuário</h2>

        <?php if ($erro): ?>
            <div class="error-message"><i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <?php if ($sucesso): ?>
            <div class="success-message"><i class="fas fa-check-circle"></i> <?= htmlspecialchars($sucesso) ?></div>
        <?php endif; ?>

        <form method="POST" class="form-container">
            <div class="form-row">
                <div class="form-group">
                    <label>Nome *</label>
                    <input type="text" name="name" class="form-control" placeholder="Digite o nome completo" required>
                </div>

                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" class="form-control" placeholder="exemplo@email.com" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Senha *</label>
                    <input type="password" name="password" class="form-control" placeholder="Crie uma senha" required>
                </div>

                <div class="form-group">
                    <label>Função</label>
                    <select name="role" class="form-control">
                        <option value="user">Vendedor</option>
                        <option value="admin">Administrador</option>
                    </select>
                </div>
            </div>

            <div style="margin-top:2rem; display:flex; justify-content:flex-end; gap:1rem;">
                <a href="users.php" class="btn">Cancelar</a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save"></i> Cadastrar
                </button>
            </div>
        </form>
    </main>
</body>
</html>
