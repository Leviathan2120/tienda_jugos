<?php
$pageTitle = "Aura Zumos | Tienda Online";
include 'header.php';

require_once 'Database.php';

// Obtener categoría seleccionada (si existe)
$categoria_id = isset($_GET['categoria']) ? intval($_GET['categoria']) : null;

// Consulta de productos (filtrada o no)
if ($categoria_id) {
    $productos = Database::select("SELECT * FROM productos WHERE id_categoria = ?", [$categoria_id]);
} else {
    $productos = Database::select("SELECT * FROM productos");
}
?>

<section style="text-align:center; margin-bottom:2rem;">
    <h1 style="color:#E54949; margin-bottom:0.5rem;">Bienvenido a Aura Zumos</h1>
    <p style="font-size:1.2rem; color:#444;">
        Descubre la mejor selección de jugos naturales, detox y energéticos.<br>
        ¡Compra fácil, rápido y seguro!
    </p>
</section>
<section class="product-list">
    <?php if (empty($productos)): ?>
        <div style="color:red;">No hay productos en esta categoría.</div>
    <?php else: ?>
        <?php foreach ($productos as $producto): ?>
            <div class="product-card">
                <img src="<?php echo htmlspecialchars($producto['url_imagen']); ?>" alt="<?php echo htmlspecialchars($producto['nombre_producto']); ?>">
                <div class="product-title"><?php echo htmlspecialchars($producto['nombre_producto']); ?></div>
                <div class="product-price">$<?php echo number_format($producto['precio'], 2); ?></div>
                <div class="product-actions">
                    <a href="producto.php?id=<?php echo $producto['id_producto']; ?>" class="btn">Ver detalle</a>
                    <form action="carro.php" method="post" style="display:inline;">
                        <input type="hidden" name="id_producto" value="<?php echo $producto['id_producto']; ?>">
                        <button type="submit" class="btn">Agregar</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>

<?php include 'footer.php'; ?>