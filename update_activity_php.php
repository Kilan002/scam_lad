<?php
/**
 * UPDATE_ACTIVITY.PHP
 * Actualiza el timestamp de última actividad del usuario
 * Llamado vía AJAX desde las páginas protegidas
 */

session_start();

// Verificar que hay sesión activa
if (!isset($_SESSION['SESSION_EMAIL'])) {
    http_response_code(401);
    echo json_encode(['error' => 'No hay sesión activa']);
    exit();
}

// Actualizar timestamp de última actividad
$_SESSION['LAST_ACTIVITY'] = time();

// Respuesta exitosa
http_response_code(200);
echo json_encode([
    'success' => true,
    'last_activity' => $_SESSION['LAST_ACTIVITY'],
    'message' => 'Actividad actualizada'
]);
?>