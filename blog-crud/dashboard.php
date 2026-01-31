<?php
require_once 'config/session.php';
require_once 'config/database.php';
require_once 'config/csrf.php';
// Cek apakah user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil post milik user yang sedang login
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Blog CRUD</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    
    <!-- Navbar -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold">📊 Dashboard</h1>
            <div class="space-x-4">
                <span class="bg-white text-blue-600 px-4 py-2 rounded">
                    👤 <?php echo htmlspecialchars($_SESSION['username']); ?>
                </span>
                <a href="index.php" class="bg-green-500 px-4 py-2 rounded hover:bg-green-600">Home</a>
                <a href="logout.php" class="bg-red-500 px-4 py-2 rounded hover:bg-red-600">Logout</a>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-3xl font-bold text-gray-800">📝 Post Saya</h2>
            <a href="posts/create.php" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 font-semibold">
                ➕ Buat Post Baru
            </a>
        </div>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <!-- Tabel Post -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <table class="w-full">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Judul</th>
                            <th class="px-6 py-3 text-left font-semibold text-gray-700">Tanggal</th>
                            <th class="px-6 py-3 text-center font-semibold text-gray-700">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($post = mysqli_fetch_assoc($result)): ?>
                            <tr class="border-b hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <a href="posts/show.php?id=<?php echo $post['id']; ?>" 
                                       class="text-blue-600 hover:underline font-semibold">
                                        <?php echo htmlspecialchars($post['title']); ?>
                                    </a>
                                </td>
                                <td class="px-6 py-4 text-gray-600">
                                    <?php echo date('d M Y H:i', strtotime($post['created_at'])); ?>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <a href="posts/edit.php?id=<?php echo $post['id']; ?>" 
                                       class="bg-yellow-500 text-white px-4 py-2 rounded hover:bg-yellow-600 mr-2">
                                        ✏️ Edit
                                    </a>
                                    <form method="POST" action="posts/delete.php" onsubmit="return confirm('Hapus post ini?')" style="display:inline;">
                                        <?= csrf_input(); ?>
                                        <input type="hidden" name="post_id" value="<?= $post['id'] ?>">
                                        <button type="submit" class="text-red-600 font-semibold hover:underline">
                                            Delete
                                        </button>
                                    </form>


                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <!-- Jika belum ada post -->
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4">
                <p>Kamu belum memiliki post. Klik tombol "Buat Post Baru" untuk membuat post pertama!</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- JavaScript untuk konfirmasi delete -->
    <script src="assets/js/script.js"></script>

</body>
</html>