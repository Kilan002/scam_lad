<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel - Scam LAD</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
    <div class="logo"><h1>Scam LAD</h1></div>
    <p>Bienvenido al Panel</p>
</header>

<div class="container">
    <h2>Hola, <?php echo $_SESSION['usuario_nombre']; ?> 👋</h2>
    <p>Has iniciado sesión correctamente.</p>

    <div class="link">
        <a href="logout.php">Cerrar sesión</a>
    </div>
</div>

<footer>© 2025 Scam LAD</footer>

</body>
</html>
