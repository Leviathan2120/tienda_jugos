<?php
session_start();
require_once 'Database.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: ../php/login.php');
    exit;
}

// Inicializar carrito si no existe
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Agregar producto al carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_producto'])) {
    $productId = intval($_POST['id_producto']);
    if (!isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] = 1;
    } else {
        $_SESSION['cart'][$productId]++;
    }
    // Redirige a la página anterior (por ejemplo, index.php)
    $redirect = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : 'index.php';
    header("Location: $redirect");
    exit;
}

// Eliminar producto del carrito
if (isset($_GET['remove'])) {
    $removeId = intval($_GET['remove']);
    unset($_SESSION['cart'][$removeId]);
    header('Location: carro.php');
    exit;
}

// Obtener detalles de productos del carrito
$cartProducts = [];
$total = 0;
if (!empty($_SESSION['cart'])) {
    $ids = implode(',', array_keys($_SESSION['cart']));
    $stmt = Database::query("SELECT * FROM productos WHERE id_producto IN ($ids)");
    $cartProducts = $stmt->fetchAll();
}

$pageTitle = "Carrito de Compras";
include 'header.php';
?>

<h2>Carrito de Compras</h2>
<?php if (empty($_SESSION['cart'])): ?>
    <p>Tu carrito está vacío.</p>
    <a href="../php/index.php" class="btn">Volver a la tienda</a>
<?php else: ?>
    <table style="width:100%; border-collapse:collapse;">
        <thead>
            <tr>
                <th>Producto</th>
                <th>Precio</th>
                <th>Cantidad</th>
                <th>Subtotal</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($cartProducts as $product): 
                $qty = $_SESSION['cart'][$product['id_producto']];
                $subtotal = $qty * $product['precio'];
                $total += $subtotal;
            ?>
            <tr>
                <td><?php echo htmlspecialchars($product['nombre_producto']); ?></td>
                <td>$<?php echo number_format($product['precio'], 2); ?></td>
                <td><?php echo $qty; ?></td>
                <td>$<?php echo number_format($subtotal, 2); ?></td>
                <td>
                    <a href="carro.php?remove=<?php echo $product['id_producto']; ?>" class="btn">Eliminar</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <h3>Total: $<?php echo number_format($total, 2); ?></h3>
    <form method="post" action="carro.php">
        <button type="submit" name="finalizar" class="btn">Finalizar compra</button>
    </form>
<?php endif; ?>

<?php
// Procesar la finalización de la compra
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['finalizar'])) {
    if (!empty($_SESSION['cart'])) {
        Database::beginTransaction();
        try {
            $userId = $_SESSION['user_id'];
            $total = 0;
            $cartProducts = [];
            $ids = implode(',', array_keys($_SESSION['cart']));
            $stmt = Database::query("SELECT * FROM productos WHERE id_producto IN ($ids)");
            $cartProducts = $stmt->fetchAll();
            foreach ($cartProducts as $product) {
                $qty = $_SESSION['cart'][$product['id_producto']];
                $subtotal = $qty * $product['precio'];
                $total += $subtotal;
            }
            // Crear la orden
            $orderId = Database::insert('pedidos', [
                'id_usuario' => $userId,
                // Puedes agregar 'id_direccion_envio' si tienes dirección seleccionada
                'total_pedido' => $total,
                'estado_pedido' => 'pagado'
            ]);
            // Insertar los productos de la orden
            foreach ($cartProducts as $product) {
                $qty = $_SESSION['cart'][$product['id_producto']];
                Database::insert('items_pedido', [
                    'id_pedido' => $orderId,
                    'id_producto' => $product['id_producto'],
                    'cantidad' => $qty,
                    'precio_unitario_compra' => $product['precio']
                ]);
            }
            Database::commit();
            $_SESSION['cart'] = []; // Vacía el carrito
            header('Location: profile.php?compra=ok');
            exit;
        } catch (Exception $e) {
            Database::rollBack();
            $error = "Error al finalizar la compra. Intenta de nuevo.";
        }
    }
}
?>

<?php include 'footer.php'; ?>
