<?php
require_once '../config/session.php';
require_once '../config/database.php';
require_once '../config/csrf.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Ambil ID post dari URL
if (!isset($_GET['id'])) {
    header("Location: ../dashboard.php");
    exit();
}

$post_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Ambil data post (cek apakah milik user yang login)
$query = "SELECT * FROM posts WHERE id = ? AND user_id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "ii", $post_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);


// Jika post tidak ditemukan atau bukan milik user
if (mysqli_num_rows($result) == 0) {
header("Location: ../dashboard.php");
exit();
}
$post = mysqli_fetch_assoc($result);

$errors = [];
$success = '';

// Proses jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
$title = trim($_POST['title']);

$content = trim($_POST['content']);


// Validasi
if (empty($title)) {
    $errors[] = "Judul wajib diisi!";
} elseif (strlen($title) < 5) {
    $errors[] = "Judul minimal 5 karakter!";
}

if (empty($content)) {
    $errors[] = "Konten wajib diisi!";
} elseif (strlen($content) < 20) {
    $errors[] = "Konten minimal 20 karakter!";
}

// Jika tidak ada error, update database
if (empty($errors)) {
    $update_query = "UPDATE posts SET title = ?, content = ? WHERE id = ? AND user_id = ?";
    $stmt = mysqli_prepare($conn, $update_query);
    mysqli_stmt_bind_param($stmt, "ssii", $title, $content, $post_id, $user_id);

    if (mysqli_stmt_execute($stmt)) {
        $success = "Post berhasil diupdate!";
        // Refresh data post
        $post['title'] = $title;
        $post['content'] = $content;
        header("refresh:2;url=../dashboard.php");
    } else {
        $errors[] = "Terjadi kesalahan. Silakan coba lagi!";
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
    <title>Edit Post - Blog CRUD</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
<!-- Navbar -->
<nav class="bg-blue-600 text-white shadow-lg">
    <div class="container mx-auto px-4 py-4">
        <h1 class="text-2xl font-bold">✏️ Edit Post</h1>
    </div>
</nav>

<!-- Content -->
<div class="container mx-auto px-4 py-8">
    <div class="max-w-3xl mx-auto bg-white rounded-lg shadow-md p-8">
        
        <!-- Tampilkan error -->
        <?php if (!empty($errors)): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4">
                <ul class="list-disc list-inside">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Tampilkan sukses -->
        <?php if ($success): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <!-- Form Edit Post -->
        <form method="POST" action="">
            <?= csrf_input(); ?>
            <!-- Judul -->
            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">Judul Post</label>
                <input type="text" name="title" 
                       value="<?php echo htmlspecialchars($post['title']); ?>"
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                       required>
            </div>

            <!-- Konten -->
            <div class="mb-6">
                <label class="block text-gray-700 font-semibold mb-2">Konten Post</label>
                <textarea name="content" rows="10"
                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                          required><?php echo htmlspecialchars($post['content']); ?></textarea>
            </div>

            <!-- Tombol -->
            <div class="flex space-x-4">
                <button type="submit" 
                        class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold">
                    💾 Update Post
                </button>
                <a href="../dashboard.php" 
                   class="bg-gray-500 text-white px-6 py-3 rounded-lg hover:bg-gray-600 font-semibold">
                    ← Batal
                </a>
            </div>
        </form>

    </div>
</div>
</body>
</html>
````