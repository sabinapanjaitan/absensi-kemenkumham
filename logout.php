<?php
session_start();
session_destroy(); // Menghapus semua sesi
header("Location: index.php"); // Kembali ke halaman login
exit;
?>