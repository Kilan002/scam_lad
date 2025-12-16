session_start();
if (isset($_SESSION['SESSION_EMAIL'])) {
    // Si ya está logueado, lo mandamos directo al panel
    header("Location: welcome.php");
    die();
}
?>
