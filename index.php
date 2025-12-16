<?php
session_start();
if (isset($_SESSION['SESSION_EMAIL'])) {
    // Si ya está logueado, lo mandamos directo a home.php
    header("Location: home.php");
    die();
}
// Si no está logueado, redirigir al index estático
header("Location: index.html");
exit();
?>
