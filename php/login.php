<?php
session_start();
require_once 'Database.php';

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $errors[] = "Todos los campos son obligatorios.";
    } else {
        $user = Database::query("SELECT * FROM usuarios WHERE correo_electronico = ?", [$email])->fetch();
        if ($user && password_verify($password, $user['contrasena_hash'])) {
            $_SESSION['user_id'] = $user['id_usuario'];
            $_SESSION['user_name'] = $user['nombre'];
            header('Location: ../php/index.php'); 
            exit;
        } else {
            $errors[] = "Correo o contraseña incorrectos.";
        }
    }
}

$pageTitle = "Iniciar Sesión";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo $pageTitle; ?></title>
    <link rel="stylesheet" href="../css/proyecto.css">
    <script defer src="https://kit.fontawesome.com/e70a51930d.js" crossorigin="anonymous"></script>
</head>
<body>
<div class="main-container" style="max-width:400px;margin:3rem auto;">
    <h2>Iniciar Sesión</h2>
    <?php if ($errors): ?>
        <div style="color:red;">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?php echo htmlspecialchars($e); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form method="post" action="login.php" id="loginForm">
        <label for="email">Correo:</label>
        <input type="email" name="email" id="email" required value="<?php echo htmlspecialchars($email); ?>"><br>
        <label for="password">Contraseña:</label>
        <input type="password" name="password" id="password" required><br>
        <button type="submit" class="btn">Entrar</button>
    </form>
    <p>¿No tienes cuenta? <a href="../php/register.php">Regístrate aquí</a></p>
    <a href="../php/index.php" class="btn" style="margin-top:1rem;">Volver a la tienda</a>
</div>
<script>
document.getElementById("loginForm").addEventListener("submit", function(event) {
    let valid = true;
    valid &= validarCorreo("email");
    valid &= validarCampoVacio("password", "La contraseña es obligatoria.");
    if (!valid) event.preventDefault();
});
</script>
</body>
</html>
