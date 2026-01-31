<?php
require_once '../config/session.php';
require_once '../config/database.php';

// Ambil ID post dari URL
if (!isset($_GET['id'])) {
    header("Location: ../index.php");
    exit();
}

$post_id = (int)$_GET['id'];

// Ambil data post dengan JOIN ke tabel users
$query = "SELECT posts.*, users.username 
          FROM posts 
          LEFT JOIN users ON posts.user_id = users.id 
          WHERE posts.id = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $post_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

// Jika post tidak ditemukan
if (mysqli_num_rows($result) == 0) {
    header("Location: ../index.php");
    exit();
}

$post = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?> - Blog CRUD</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    
    <!-- Navbar -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <h1 class="text-2xl font-bold">📖 Detail Post</h1>
        </div>
    </nav>

    <!-- Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            
            <!-- Tombol Kembali -->
            <a href="../index.php" class="inline-block mb-4 text-blue-600 hover:underline">
                ← Kembali ke Home
            </a>

            <!-- Card Post -->
            <div class="bg-white rounded-lg shadow-lg p-8">
                <!-- Header Post -->
                <h1 class="text-4xl font-bold mb-4 text-gray-800">
                    <?php echo htmlspecialchars($post['title']); ?>
                </h1>

                <!-- Meta Info -->
                <div class="flex items-center space-x-4 text-gray-600 mb-6 pb-6 border-b">
                    <span class="flex items-center">
                        👤 <span class="ml-2 font-semibold"><?php echo htmlspecialchars($post['username']); ?></span>
                    </span>
                    <span class="flex items-center">
                        📅 <span class="ml-2"><?php echo date('d F Y, H:i', strtotime($post['created_at'])); ?></span>
                    </span>
                </div>

                <!-- Konten Post -->
                <div class="prose prose-lg max-w-none text-gray-700 leading-relaxed">
                    <?php echo nl2br(htmlspecialchars($post['content'])); ?>
                </div>

                <!-- Tombol Edit/Delete (hanya jika pemilik post) -->
                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']): ?>
                    <div class="mt-8 pt-6 border-t flex space-x-4">
                        <a href="edit.php?id=<?php echo $post['id']; ?>" 
                           class="bg-yellow-500 text-white px-6 py-3 rounded-lg hover:bg-yellow-600 font-semibold">
                            ✏️ Edit Post
                        </a>
                        <a href="delete.php?id=<?php echo $post['id']; ?>" 
                           onclick="return confirm('Yakin ingin menghapus post ini?')"
                           class="bg-red-500 text-white px-6 py-3 rounded-lg hover:bg-red-600 font-semibold">
                            🗑️ Hapus Post
                        </a>
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>

</body>
</html>