<?php
/**
 * GESTOR DE SESIONES - Control de Inactividad
 * Incluir este archivo en TODAS las páginas protegidas
 */

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configuración: Tiempo de inactividad en segundos (5 minutos = 300 segundos)
define('TIEMPO_INACTIVIDAD', 300); // Cambiar según necesites: 300 = 5 min, 600 = 10 min, 900 = 15 min

// Verificar si el usuario está autenticado
if (!isset($_SESSION['SESSION_EMAIL'])) {
    // No hay sesión activa, redirigir al login
    header("Location: login.php?error=no_session");
    exit();
}

// Verificar tiempo de inactividad
if (isset($_SESSION['LAST_ACTIVITY'])) {
    $tiempo_transcurrido = time() - $_SESSION['LAST_ACTIVITY'];
    
    if ($tiempo_transcurrido > TIEMPO_INACTIVIDAD) {
        // Tiempo de inactividad excedido - Destruir sesión
        session_unset();
        session_destroy();
        header("Location: login.php?error=timeout&time=" . round($tiempo_transcurrido / 60));
        exit();
    }
}

// Actualizar el timestamp de última actividad
$_SESSION['LAST_ACTIVITY'] = time();

// Regenerar ID de sesión periódicamente (seguridad adicional)
if (!isset($_SESSION['CREATED'])) {
    $_SESSION['CREATED'] = time();
} else if (time() - $_SESSION['CREATED'] > 1800) {
    // Regenerar cada 30 minutos
    session_regenerate_id(true);
    $_SESSION['CREATED'] = time();
}
?>