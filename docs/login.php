<?php

require_once 'config/session.php';
require_once 'config/csrf.php';
require_once 'config/database.php';

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$errors = [];

// Proses jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {


    verify_csrf(); // 🔐 WAJIB — INI YANG NGUNCI CSRFs

     // 🔐 ANTI BRUTE FORCE (TAMBAH DI SINI)
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = 0;
    }

    if ($_SESSION['login_attempts'] >= 10) {
        $errors[] = "Terlalu banyak percobaan login. Coba lagi nanti.";
    }

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Validasi
    if (empty($username)) {
        $errors[] = "Username wajib diisi!";
    }

    if (empty($password)) {
        $errors[] = "Password wajib diisi!";
    }

    // Jika tidak ada error, cek ke database
    if (empty($errors)) {
        // Query untuk cari user berdasarkan username
        $query = "SELECT id, username, password FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($result)) {
            // Verifikasi password (bandingkan dengan hash)
            if (password_verify($password, $user['password'])) {
                // Login berhasil! Simpan data user ke session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];

                // Redirect ke dashboard
                header("Location: dashboard.php");
                exit();
            } else {
                $errors[] = "Password salah!";
            }
        } else {
            $errors[] = "Username tidak ditemukan!";
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Blog CRUD</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-purple-500 to-pink-600 min-h-screen flex items-center justify-center">
    
    <div class="bg-white rounded-lg shadow-2xl p-8 w-full max-w-md">
        <h2 class="text-3xl font-bold text-center mb-6 text-gray-800">🔐 Login</h2>

        <!-- Tampilkan error jika ada -->
        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                <ul class="list-disc list-inside">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Form Login -->

        <form method="POST" action="">
            <?= csrf_input(); ?>

            <!-- Username -->
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Username</label>
                <input type="text" name="username" 
                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                       required>
            </div>

            <!-- Password -->
            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">Password</label>
                <input type="password" name="password" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500"
                       required>
            </div>

            <!-- Tombol Submit -->
            <button type="submit" 
                    class="w-full bg-purple-600 text-white py-3 rounded-lg font-semibold hover:bg-purple-700 transition">
                Login
            </button>
        </form>

        <!-- Link ke Register -->
        <p class="text-center mt-4 text-gray-600">
            Belum punya akun? 
            <a href="register.php" class="text-purple-600 hover:underline font-semibold">Register di sini</a>
        </p>

        <!-- Link ke Home -->
        <p class="text-center mt-2">
            <a href="index.php" class="text-gray-500 hover:underline">← Kembali ke Home</a>
        </p>
    </div>

</body>
</html>