<?php
require __DIR__ . '/../../../includes/auth.php';
require __DIR__ . '/../../../config/database.php';

$sale_id = $_GET['id'] ?? 0;
$isModal = isset($_GET['modal']); // Verifica se foi chamado via AJAX para o Modal

// 1. Busca os dados da venda, cliente e os nomes de quem criou/editou
$sql = "SELECT s.*, 
               c.name as client_name, c.email as client_email, c.phone as client_phone,
               u1.name as creator_name, 
               u2.name as editor_name 
        FROM sales s
        LEFT JOIN clients c ON s.client_id = c.id
        LEFT JOIN users u1 ON s.user_id = u1.id
        LEFT JOIN users u2 ON s.updated_by = u2.id
        WHERE s.id = ? AND s.company_id = ?";

$stmt = $pdo->prepare($sql);
$stmt->execute([$sale_id, getCompanyId()]);
$sale = $stmt->fetch();

if (!$sale) {
    die("Venda não encontrada ou você não tem permissão para vê-la.");
}

// 2. Busca os itens (produtos) desta venda específica
$sqlItems = "SELECT si.*, p.name as product_name 
             FROM sale_items si 
             LEFT JOIN products p ON si.product_id = p.id 
             WHERE si.sale_id = ?";
$stmtItems = $pdo->prepare($sqlItems);
$stmtItems->execute([$sale_id]);
$items = $stmtItems->fetchAll();
?>

<?php if (!$isModal): ?>
    <!DOCTYPE html>
    <html lang="pt-BR">

    <head>
        <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Venda #<?= $sale['id'] ?> - Detalhes</title>
        <link rel="stylesheet" href="../../css/style.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    </head>

    <body>
        <?php $basePath = '../../';
        include __DIR__ . '/../../../includes/header.php'; ?>
        <main>
        <?php endif; ?>

        <h2 style="margin-top: 0;"><i class="fas fa-shopping-cart"></i> Detalhes da Venda #<?= $sale['id'] ?></h2>

        <div class="dashboard-grid">
            <div class="card-stat card-blue" style="display: block; border-left: 4px solid #3498db; padding: 1.5rem;">
                <h3 style="margin-top: 0; color: #3498db; margin-bottom: 1rem;"><i class="fas fa-user"></i> Cliente</h3>
                <p style="font-size: 1rem; margin-bottom: 0.5rem;"><strong>Nome:</strong> <?= htmlspecialchars($sale['client_name'] ?? 'Cliente Removido') ?></p>
                <p style="font-size: 1rem; margin-bottom: 0.5rem;"><strong>E-mail:</strong> <?= htmlspecialchars($sale['client_email'] ?? '-') ?></p>
                <p style="font-size: 1rem; margin-bottom: 0;"><strong>Telefone:</strong> <?= htmlspecialchars($sale['client_phone'] ?? '-') ?></p>
            </div>
            <div class="card-stat card-green" style="display: block; border-left: 4px solid #2ecc71; padding: 1.5rem;">
                <h3 style="margin-top: 0; color: #2ecc71; margin-bottom: 1rem;"><i class="fas fa-info-circle"></i> Resumo</h3>
                <p style="font-size: 1rem; margin-bottom: 0.5rem;"><strong>Status:</strong> <span class="badge"
                        style="background: #2ecc71; color: white; padding: 2px 8px; border-radius: 4px;"><?= $sale['status'] ?></span>
                </p>
                <p style="font-size: 1rem; margin-bottom: 0.5rem;"><strong>Data:</strong> <?= date('d/m/Y H:i', strtotime($sale['created_at'])) ?></p>
                <p style="font-size: 1rem; margin-bottom: 0;"><strong>Total:</strong> R$ <?= number_format($sale['total'], 2, ',', '.') ?></p>
            </div>
        </div>

        <div class="table-container">
            <h3><i class="fas fa-box"></i> Itens do Pedido</h3>
            <table>
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Qtd</th>
                        <th>Unitário</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td data-label="Produto"><?= htmlspecialchars($item['product_name'] ?? 'Não encontrado') ?></td>
                            <td data-label="Qtd"><?= $item['quantity'] ?></td>
                            <td data-label="Unitário">R$ <?= number_format($item['unit_price'], 2, ',', '.') ?></td>
                            <td data-label="Subtotal">R$ <?= number_format($item['quantity'] * $item['unit_price'], 2, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr style="font-weight: bold; font-size: 1.1rem; background: #fdfdfd;">
                        <td colspan="3" style="text-align: right; padding: 15px;">TOTAL FINAL:</td>
                        <td style="color: #2ecc71; padding: 15px;">R$ <?= number_format($sale['total'], 2, ',', '.') ?>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="form-container" style="margin-top: 2rem; border-left: 5px solid #3498db; padding: 1.5rem;">
            <h4 style="margin-top: 0;"><i class="fas fa-fingerprint"></i> Auditoria do Registro</h4>
            <p style="margin-bottom: 0.5rem;"><i class="fas fa-plus-circle"></i> Venda registrada por:
                <strong><?= htmlspecialchars($sale['creator_name'] ?? 'Sistema') ?></strong> em
                <?= date('d/m/Y H:i', strtotime($sale['created_at'])) ?></p>
            <?php if ($sale['editor_name']): ?>
                <p style="margin-bottom: 0;"><i class="fas fa-pen-square"></i> Última alteração por:
                    <strong><?= htmlspecialchars($sale['editor_name']) ?></strong> em
                    <?= date('d/m/Y H:i', strtotime($sale['updated_at'])) ?></p>
            <?php endif; ?>
        </div>

        <?php if (!$isModal): ?>
        </main>
    </body>

    </html>
<?php endif; ?>
