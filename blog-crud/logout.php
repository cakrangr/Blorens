<?php
require_once 'config/session.php';
require_once 'config/database.php';

// Hapus semua data session
session_unset();

// Hancurkan session
session_destroy();

// Redirect ke halaman utama
header("Location: index.php");
exit();
?>