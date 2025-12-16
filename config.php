<?php
// Datos de conexión
$server = "localhost";
$user = "root";
$pass = "123"; // <--- ¡CAMBIA ESTO POR TU CLAVE REAL!
$database = "scamlad_db"; // Nombre de la base de datos

// Intentar conectar
$conn = mysqli_connect($server, $user, $pass, $database);

// Verificar si hubo error
if (!$conn) {
    die("<h3 style='color:red'>Error de Conexión: " . mysqli_connect_error() . "</h3>");
}
?>
