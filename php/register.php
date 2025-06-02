<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

require_once 'Database.php';

$errors = [];
$name = '';
$email = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if (empty($name)) $errors[] = "El nombre es obligatorio.";
    if (empty($email)) $errors[] = "El correo es obligatorio.";
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Correo no válido.";
    if (empty($password)) $errors[] = "La contraseña es obligatoria.";
    elseif (strlen($password) < 8) $errors[] = "La contraseña debe tener al menos 8 caracteres.";
    if ($password !== $password_confirm) $errors[] = "Las contraseñas no coinciden.";

    if (empty($errors)) {
        // Verificar si el correo ya existe
        $user = Database::query("SELECT id_usuario FROM usuarios WHERE correo_electronico = ?", [$email])->fetch();
        if ($user) {
            $errors[] = "Este correo electrónico ya está registrado.";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $inserted = Database::insert('usuarios', [
                'nombre' => $name,
                'correo_electronico' => $email,
                'contrasena_hash' => $password_hash
            ]);
            if ($inserted) {
                header('Location: ../php/login.php?registered=1');
                exit;
            } else {
                $errors[] = "Error al registrar. Intenta de nuevo.";
            }
        }
    }
}

$pageTitle = "Registrarse";
//include 'header.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registrarse</title>
    <link rel="stylesheet" href="../css/proyecto.css">
</head>
<body>
<div class="contenedor-principal" style="max-width:400px;margin:3rem auto;">
    <h2>Registrarse</h2>
    <?php if ($success): ?>
        <div style="color:green;">Registro exitoso. <a href="../php/login.php">Inicia sesión aquí</a></div>
    <?php elseif ($errors): ?>
        <div style="color:red;">
            <ul>
                <?php foreach ($errors as $e): ?>
                    <li><?php echo htmlspecialchars($e); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>
    <form method="post" action="register.php" id="registerForm">
        <label for="name">Nombre:</label>
        <input type="text" name="name" id="name" required value="<?php echo htmlspecialchars($name); ?>"><br>
        <label for="email">Correo:</label>
        <input type="email" name="email" id="email" required value="<?php echo htmlspecialchars($email); ?>"><br>
        <label for="password">Contraseña:</label>
        <input type="password" name="password" id="password" required><br>
        <label for="password_confirm">Confirmar Contraseña:</label>
        <input type="password" name="password_confirm" id="password_confirm" required><br>
        <button type="submit" class="btn">Registrarse</button>
    </form>
    <p>¿Ya tienes cuenta? <a href="../php/login.php">Inicia sesión aquí</a></p>
    <a href="../php/index.php" class="btn" style="margin-top:1rem;">Volver a la tienda</a>
</div>
<script>
document.getElementById("registerForm").addEventListener("submit", function(event) {
    let valid = true;
    valid &= validarCampoVacio("name", "El nombre es obligatorio.");
    valid &= validarCorreo("email");
    valid &= validarContrasena("password");
    valid &= validarCampoVacio("password_confirm", "La confirmación es obligatoria.");
    if (document.getElementById("password").value !== document.getElementById("password_confirm").value) {
        mostrarMensajeError("password_confirm", "Las contraseñas no coinciden.");
        valid = false;
    }
    if (!valid) event.preventDefault();
});
</script>
</body>
</html>
<?php //include 'footer.php'; ?>
