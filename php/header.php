<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'Database.php';
// Cambia la consulta y los nombres de campos para que coincidan con tu base de datos
$categories = Database::select("SELECT * FROM categorias");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle ?? 'Tienda Online Aura Zumo'; ?></title>
    <link rel="stylesheet" href="../css/proyecto.css">
    <link rel="stylesheet" href="../css/styles.css">
    <script defer src="https://kit.fontawesome.com/e70a51930d.js" crossorigin="anonymous"></script>
</head>
<body class="d-flex flex-column min-vh-100">
    <header class="mi-header">
        <div class="header-container">
            <nav class="navbar-custom">
                <a href="index.php" class="navbar-brand">Aura Zumo</a>
                <ul class="navbar-nav-custom">
                    <li class="nav-item-custom"><a href="index.php" class="nav-link-custom">Inicio</a></li>
                    <li class="nav-item-custom dropdown-custom">
                        <a href="#" class="nav-link-custom">Categoría</a>
                        <ul class="dropdown-menu-custom">
                            <?php foreach ($categories as $cat): ?>
                                <li>
                                    <a href="index.php?categoria=<?php echo $cat['id_categoria']; ?>" class="dropdown-item-custom">
                                        <?php echo htmlspecialchars($cat['nombre_categoria']); ?>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li class="nav-item-custom"><a href="profile.php" class="nav-link-custom">Mi Cuenta (<?php echo htmlspecialchars($_SESSION['user_name']); ?>)</a></li>
                        <li class="nav-item-custom"><a href="logout.php" class="nav-link-custom">Cerrar Sesión</a></li>
                    <?php else: ?>
                        <li class="nav-item-custom"><a href="login.php" class="nav-link-custom">Iniciar Sesión</a></li>
                        <li class="nav-item-custom"><a href="register.php" class="nav-link-custom">Registrarse</a></li>
                    <?php endif; ?>
                    <li class="nav-item-custom">
                        <a href="<?php echo isset($_SESSION['user_id']) ? 'carro.php' : 'carro.php'; ?>" class="nav-link-custom">
                            Carrito (<?php echo isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0; ?>)
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </header>
    <main class="main-container">
