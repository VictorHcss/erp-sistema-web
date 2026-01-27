<?php
require __DIR__ . '/../../../includes/auth.php';
require __DIR__ . '/../../../config/database.php';

$data_inicio = $_GET['data_inicio'] ?? '';
$data_fim = $_GET['data_fim'] ?? '';
$params = [getCompanyId()];

// Query Unificada: Filtros + Joins de Auditoria
$sql = "SELECT s.*, 
               c.name as client_name, 
               uc.name as creator_name, 
               uu.name as updater_name 
        FROM sales s 
        LEFT JOIN clients c ON s.client_id = c.id
        LEFT JOIN users uc ON s.user_id = uc.id 
        LEFT JOIN users uu ON s.updated_by = uu.id 
        WHERE s.company_id = ?";

if ($data_inicio) {
    $sql .= " AND DATE(s.created_at) >= ?";
    $params[] = $data_inicio;
}

if ($data_fim) {
    $sql .= " AND DATE(s.created_at) <= ?";
    $params[] = $data_fim;
}

$sql .= " ORDER BY s.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vendas - ERP</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php $basePath = '../../';
    include __DIR__ . '/../../../includes/header.php'; ?>

    <main>
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
            <h2><i class="fas fa-list"></i> Histórico de Vendas</h2>
            <a href="add_sale.php" class="btn btn-success"><i class="fas fa-plus"></i> Nova Venda</a>
        </div>

        <div class="table-container" style="margin-bottom: 1.5rem">
            <form method="GET" action="sales.php"
                style="display: flex; gap: 1rem; align-items:flex-end; flex-wrap: wrap;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label>Data Inicio:</label>
                    <input type="date" name="data_inicio" class="form-control"
                        value="<?= htmlspecialchars($data_inicio) ?>">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label>Data Fim:</label>
                    <input type="date" name="data_fim" class="form-control" value="<?= htmlspecialchars($data_fim) ?>">
                </div>
                <button type="submit" class="btn btn-primary"><i class="fas fa-filter"></i> Filtrar</button>
                <a href="sales.php" class="btn btn-danger"
                    style="background: #95a5a6; text-decoration: none;">Limpar</a>
            </form>
        </div>

        <div class="table-container">
            <table id="tableModule">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Total</th>
                        <th>Data</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sales as $row):
                        $statusClass = '';
                        switch ($row['status']) {
                            case 'Finalizada':
                                $statusClass = 'color: #2ecc71; font-weight: bold;';
                                break;
                            case 'Pendente':
                                $statusClass = 'color: #f39c12; font-weight: bold;';
                                break;
                            case 'Cancelada':
                                $statusClass = 'color: #e74c3c; font-weight: bold;';
                                break;
                        }
                        ?>
                        <tr>
                            <td>
                                <a href="javascript:void(0)" onclick="viewSale(<?= $row['id'] ?>)" class="btn-view"
                                    title="Visualizar" style="text-decoration: none;">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?= $row['id'] ?>
                            </td>
                            <td><?= htmlspecialchars($row['client_name'] ?? 'Cliente Removido') ?></td>
                            <td>R$ <?= number_format($row['total'], 2, ',', '.') ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></td>
                            <td style="<?= $statusClass ?>"><?= htmlspecialchars($row['status']) ?></td>
                            <td>
                                <a href="edit_sale.php?id=<?= $row['id'] ?>" class="action-btn btn-edit"><i
                                        class="fas fa-edit"></i> Editar</a><br>
                                <small style="color: #666;">Criado por:
                                    <?= htmlspecialchars($row['creator_name'] ?? 'Sistema') ?></small>
                                <?php if ($row['updater_name']): ?>
                                    <br><small style="color: #666;">Atualizado por:
                                        <?= htmlspecialchars($row['updater_name']) ?></small>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

    <!-- Modal Structure (Consistent with style.css) -->
    <div id="saleModalOverlay" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h3><i class="fas fa-file-invoice-dollar"></i> Detalhes da Venda</h3>
                <button class="modal-close" onclick="document.getElementById('saleModalOverlay').classList.remove('active')">&times;</button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Conteúdo carregado via AJAX -->
            </div>
        </div>
    </div>

    <script src="../../js/main.js"></script>
</body>
</html>
