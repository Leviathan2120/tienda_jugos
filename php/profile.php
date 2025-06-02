<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ../php/login.php');
    exit;
}
require_once 'Database.php';
$pageTitle = "Mi Perfil";
include 'header.php';

$userId = $_SESSION['user_id'];

// Obtener todos los pedidos del usuario
$orders = Database::select("SELECT * FROM pedidos WHERE id_usuario = ? ORDER BY fecha_pedido DESC", [$userId]);
?>

<h2>Mi Perfil</h2>
<p>Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong></p>

<h3>Historial de compras:</h3>
<?php if ($orders): ?>
    <?php foreach ($orders as $order): ?>
        <div style="margin-bottom:2rem;">
            <h4>Compra #<?php echo $order['id_pedido']; ?> | Fecha: <?php echo $order['fecha_pedido']; ?></h4>
            <table style="width:100%; border-collapse:collapse;">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio unitario</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Obtener los items del pedido
                    $items = Database::select(
                        "SELECT ip.*, p.nombre_producto, ip.precio_unitario_compra, ip.cantidad 
                         FROM items_pedido ip 
                         JOIN productos p ON ip.id_producto = p.id_producto 
                         WHERE ip.id_pedido = ?", 
                        [$order['id_pedido']]
                    );
                    $total = 0;
                    foreach ($items as $item):
                        $subtotal = $item['precio_unitario_compra'] * $item['cantidad'];
                        $total += $subtotal;
                    ?>
                    <tr>
                        <th><?php echo htmlspecialchars($item['nombre_producto']); ?></td>
                        <th>$<?php echo number_format($item['precio_unitario_compra'], 2); ?></th>
                        <th><?php echo $item['cantidad']; ?></th>
                        <th>$<?php echo number_format($subtotal, 2); ?></th>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="3" style="text-align:right;">Total:</th>
                        <th>$<?php echo number_format($total, 2); ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>No tienes compras registradas.</p>
<?php endif; ?>

<?php include 'footer.php'; ?>
