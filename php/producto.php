<?php
session_start();
require_once 'Database.php';

$product = null;
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $product = Database::query("SELECT * FROM productos WHERE id_producto = ?", [$id])->fetch();
}

$pageTitle = "Detalle del Producto";
include 'header.php';
?>

<?php if ($product): ?>
    <div class="product-card" style="max-width:400px;margin:2rem auto;">
        <img src="<?php echo htmlspecialchars($product['url_imagen']); ?>" alt="<?php echo htmlspecialchars($product['nombre_producto']); ?>">
        <div class="product-title"><?php echo htmlspecialchars($product['nombre_producto']); ?></div>
        <div class="product-price">$<?php echo number_format($product['precio'], 2); ?></div>
        <div style="margin-bottom:1rem;"><?php echo htmlspecialchars($product['descripcion']); ?></div>
        <div class="product-actions">
            <form action="carro.php" method="post" style="display:inline;">
                <input type="hidden" name="id_producto" value="<?php echo $product['id_producto']; ?>">
                <button type="submit" class="btn">Agregar al carrito</button>
            </form>
            <a href="index.php" class="btn" style="margin-left:1rem;">Volver</a>
        </div>
    </div>
<?php else: ?>
    <div style="color:red;text-align:center;margin:2rem;">Producto no encontrado.</div>
    <div style="text-align:center;"><a href="index.php" class="btn">Volver</a></div>
<?php endif; ?>

<?php include 'footer.php'; ?>
