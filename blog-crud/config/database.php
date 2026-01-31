<?php
// File ini untuk koneksi ke database MySQL

// Data koneksi database
$host = 'localhost';        // Server database (biasanya localhost)
$dbname = 'blog_crud';      // Nama database
$username = 'root';         // Username MySQL (default: root)
$password = '';             // Password MySQL (default: kosong di XAMPP)

// Membuat koneksi menggunakan mysqli
$conn = mysqli_connect($host, $username, $password, $dbname);

// Cek apakah koneksi berhasil
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Set charset ke utf8mb4 untuk support emoji dan karakter khusus
mysqli_set_charset($conn, "utf8mb4");
?>