<?php
include 'auth.php'; 
include '_header.php';

// pastikan session aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// cek apakah user sudah login
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit;
}
?>
<h2>Dashboard</h2>
<p>Welcome <b><?php echo htmlspecialchars($_SESSION['user'], ENT_QUOTES, 'UTF-8'); ?></b>!</p>
<p>Use the menu above to access the web page.</p>
<?php include '_footer.php'; ?>
