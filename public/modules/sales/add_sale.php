<?php
require __DIR__ . '/../../../includes/auth.php';
require __DIR__ . '/../../../config/database.php';

// Clientes
$stmt = $pdo->prepare("SELECT id, name FROM clients WHERE company_id = ?");
$stmt->execute([getCompanyId()]);
$clients = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Produtos
$stmt = $pdo->prepare("
    SELECT id, name, price, stock
    FROM products
    WHERE company_id = ?
    ORDER BY name
");
$stmt->execute([getCompanyId()]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Venda</title>
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>

<body>
    <?php $basePath = '../../'; include __DIR__ . '/../../../includes/header.php'; ?>

    <main>
        <div class="page-header">
            <div>
                <h2><i class="fas fa-cart-plus"></i> Nova Venda</h2>
                <p>Preencha os dados abaixo para registrar uma venda</p>
            </div>
        </div>

        <form method="POST" action="sales_process.php">
            <!-- Dados do Cliente -->
            <div class="table-container" style="margin-bottom: 2rem;">
                <div class="form-row-flex">
                    <div class="form-group col-grow-2">
                        <label>Cliente *</label>
                        <div class="input-icon">
                            <i class="fas fa-user"></i>
                            <select name="client_id" class="form-control" required>
                                <option value="">Selecione um cliente</option>
                                <?php foreach ($clients as $c): ?>
                                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group" style="flex: 1; min-width: 200px;">
                        <label>Status</label>
                        <div class="input-icon">
                            <i class="fas fa-info-circle"></i>
                            <select name="status" class="form-control">
                                <option value="Finalizada">Finalizada</option>
                                <option value="Pendente">Pendente</option>
                                <option value="Cancelada">Cancelada</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Itens da Venda (Table Container) -->
            <div class="table-container">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                    <h3><i class="fas fa-boxes"></i> Itens da Venda</h3>
                </div>

                <div class="form-row-flex" style="align-items: flex-end;">
                    <div class="form-group col-grow-3">
                        <label>Produto</label>
                        <div class="input-icon">
                            <i class="fas fa-box-open"></i>
                            <select id="product" class="form-control">
                                <option value="">Selecione um produto</option>
                                <?php foreach ($products as $p): ?>
                                    <option value="<?= $p['id'] ?>" data-name="<?= htmlspecialchars($p['name']) ?>"
                                        data-price="<?= $p['price'] ?>" data-stock="<?= $p['stock'] ?>">
                                        <?= htmlspecialchars($p['name']) ?> (Estoque: <?= $p['stock'] ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group col-fixed-150">
                        <label>Quantidade</label>
                        <div class="input-icon">
                            <i class="fas fa-sort-numeric-up"></i>
                            <input type="number" id="quantity" min="1" placeholder="1" class="form-control">
                        </div>
                    </div>
                    <div class="form-group col-auto">
                        <button type="button" class="btn btn-primary" onclick="addItem()">
                            <i class="fas fa-plus"></i> Adicionar
                        </button>
                    </div>
                </div>

                <div class="table-responsive" style="margin-top: 2rem;">
                    <table id="itemsTable">
                        <thead>
                            <tr>
                                <th>Produto</th>
                                <th class="col-qty">Qtd</th>
                                <th>Valor Unit.</th>
                                <th>Total</th>
                                <th class="col-action">Ação</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>

                <div style="display: flex; justify-content: flex-end; margin-top: 3rem; border-top: 1px solid #eee; padding-top: 2rem;">
                    <div class="total-summary-card">
                        <span class="label">Total Geral</span>
                        <div class="amount"><span class="currency">R$</span><span id="saleTotal">0.00</span></div>
                    </div>
                </div>
            </div>

            <input type="hidden" name="items" id="itemsInput">

            <div style="margin-top: 2rem; display: flex; justify-content: flex-end;">
                <button type="submit" class="btn btn-primary" style="padding: 1rem 3rem; font-size: 1.1rem; box-shadow: 0 4px 15px rgba(67, 97, 238, 0.4);">
                    <i class="fas fa-save"></i> Finalizar Venda
                </button>
            </div>
        </form>
    </main>

    <script>
        let items = [];

        function addItem() {
            const productSelect = document.getElementById('product');
            const quantityInput = document.getElementById('quantity');

            const productId = productSelect.value;
            const quantity = parseInt(quantityInput.value);

            if (!productId || isNaN(quantity) || quantity <= 0) {
                alert('Selecione um produto e quantidade válida');
                return;
            }

            const option = productSelect.selectedOptions[0];
            const stock = parseInt(option.dataset.stock);

            if (quantity > stock) {
                alert('Quantidade maior que o estoque disponível');
                return;
            }

            // Verifica se produto já existe na lista
            const existingItemIndex = items.findIndex(item => item.product_id === productId);
            if (existingItemIndex > -1) {
                if (items[existingItemIndex].quantity + quantity > stock) {
                    alert('Quantidade total maior que o estoque disponível');
                    return;
                }
                items[existingItemIndex].quantity += quantity;
            } else {
                items.push({
                    product_id: productId,
                    name: option.dataset.name,
                    quantity: quantity,
                    unit_price: parseFloat(option.dataset.price)
                });
            }

            quantityInput.value = '';
            productSelect.value = ''; // Reset select
            renderTable();
        }

        function removeItem(index) {
            if(confirm('Tem certeza que deseja remover este item?')) {
                items.splice(index, 1);
                renderTable();
            }
        }

        function renderTable() {
            const tbody = document.querySelector('#itemsTable tbody');
            tbody.innerHTML = '';

            let total = 0;

            if (items.length === 0) {
                tbody.innerHTML = '<tr><td colspan="5" style="text-align: center; color: #999; padding: 2rem;">Nenhum item adicionado</td></tr>';
            }

            items.forEach((item, index) => {
                const itemTotal = item.quantity * item.unit_price;
                total += itemTotal;

                tbody.innerHTML += `
            <tr>
                <td data-label="Produto"><strong>${item.name}</strong></td>
                <td data-label="Qtd">${item.quantity}</td>
                <td data-label="Valor Unit.">R$ ${item.unit_price.toFixed(2)}</td>
                <td data-label="Total" style="color: var(--primary-color); font-weight: bold;">R$ ${itemTotal.toFixed(2)}</td>
                <td data-label="Ação" style="text-align: center;">
                    <button type="button" class="btn-remove" onclick="removeItem(${index})" title="Remover item">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            </tr>
        `;
            });

            document.getElementById('saleTotal').innerText = total.toFixed(2);
            document.getElementById('itemsInput').value = JSON.stringify(items);
        }
        
        // Render initial empty table
        renderTable();
    </script>
</body>

</html>