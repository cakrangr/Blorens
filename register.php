<?php
require_once 'config/session.php';
require_once 'config/database.php';
require_once 'config/csrf.php';
// Array untuk menyimpan error
$errors = [];
$success = '';

// Proses jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form dan bersihkan
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // VALIDASI SERVER-SIDE (WAJIB!)
    
    // 1. Validasi username
    if (empty($username)) {
        $errors[] = "Username wajib diisi!";
    } elseif (strlen($username) < 3) {
        $errors[] = "Username minimal 3 karakter!";
    }

    // 2. Validasi email
    if (empty($email)) {
        $errors[] = "Email wajib diisi!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid!";
    }

    // 3. Validasi password
    if (empty($password)) {
        $errors[] = "Password wajib diisi!";
    } elseif (strlen($password) < 6) {
        $errors[] = "Password minimal 6 karakter!";
    }

    // 4. Validasi konfirmasi password
    if ($password !== $confirm_password) {
        $errors[] = "Password dan konfirmasi password tidak sama!";
    }

    // Jika tidak ada error, lanjut proses registrasi
    if (empty($errors)) {
        // Cek apakah username sudah ada
        $check_username = "SELECT id FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $check_username);
        mysqli_stmt_bind_param($stmt, "s", $username);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors[] = "Username sudah digunakan!";
        }
        mysqli_stmt_close($stmt);

        // Cek apakah email sudah ada
        $check_email = "SELECT id FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $check_email);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors[] = "Email sudah digunakan!";
        }
        mysqli_stmt_close($stmt);

        // Jika username dan email belum ada, simpan ke database
        if (empty($errors)) {
            // Hash password untuk keamanan
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert ke database menggunakan prepared statement (aman dari SQL injection)
            $insert_query = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $insert_query);
            mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hashed_password);

            if (mysqli_stmt_execute($stmt)) {
                $success = "Registrasi berhasil! Silakan login.";
                // Redirect ke login setelah 2 detik
                header("refresh:2;url=login.php");
            } else {
                $errors[] = "Terjadi kesalahan. Silakan coba lagi!";
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Blog CRUD</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-r from-blue-500 to-purple-600 min-h-screen flex items-center justify-center">
    
    <div class="bg-white rounded-lg shadow-2xl p-8 w-full max-w-md">
        <h2 class="text-3xl font-bold text-center mb-6 text-gray-800">📝 Register</h2>

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

        <!-- Tampilkan pesan sukses -->
        <?php if ($success): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <!-- Form Register -->
        <form method="POST" action="">
            <?= csrf_input(); ?>

            <!-- Username -->
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Username</label>
                <input type="text" name="username" 
                       value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required>
            </div>

            <!-- Email -->
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Email</label>
                <input type="email" name="email" 
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required>
            </div>

            <!-- Password -->
            <div class="mb-4">
                <label class="block text-gray-700 font-semibold mb-2">Password</label>
                <input type="password" name="password" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required>
            </div>

            <!-- Konfirmasi Password -->
            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">Konfirmasi Password</label>
                <input type="password" name="confirm_password" 
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required>
            </div>

            <!-- Tombol Submit -->
            <button type="submit" 
                    class="w-full bg-blue-600 text-white py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                Register
            </button>
        </form>

        <!-- Link ke Login -->
        <p class="text-center mt-4 text-gray-600">
            Sudah punya akun? 
            <a href="login.php" class="text-blue-600 hover:underline font-semibold">Login di sini</a>
        </p>
    </div>

</body>
</html>