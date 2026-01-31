<?php
require_once __DIR__ . '/config/session.php';



// Include koneksi database
require_once 'config/database.php';

// Query untuk ambil semua post (JOIN dengan tabel users untuk ambil nama penulis)
$query = "SELECT posts.*, users.username 
          FROM posts 
          LEFT JOIN users ON posts.user_id = users.id 
          ORDER BY posts.created_at DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog CRUD - Halaman Utama</title>
    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    
    <!-- Navbar -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <h1 class="text-2xl font-bold">📝 Blog CRUD</h1>
            <div class="space-x-4">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <!-- Jika sudah login -->
                    <a href="dashboard.php" class="bg-white text-blue-600 px-4 py-2 rounded hover:bg-gray-100">Dashboard</a>
                    <a href="logout.php" class="bg-red-500 px-4 py-2 rounded hover:bg-red-600">Logout</a>
                <?php else: ?>
                    <!-- Jika belum login -->
                    <a href="login.php" class="bg-white text-blue-600 px-4 py-2 rounded hover:bg-gray-100">Login</a>
                    <a href="register.php" class="bg-green-500 px-4 py-2 rounded hover:bg-green-600">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <div class="container mx-auto px-4 py-8">
        <h2 class="text-3xl font-bold mb-6 text-gray-800">📚 Semua Post Blog</h2>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <!-- Grid untuk menampilkan post -->
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php while ($post = mysqli_fetch_assoc($result)): ?>
                    <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-xl transition">
                        <h3 class="text-xl font-bold mb-2 text-gray-800">
                            <?php echo htmlspecialchars($post['title']); ?>
                        </h3>
                        <p class="text-gray-600 mb-4">
                            <?php 
                            // Ambil 150 karakter pertama dari content
                            $preview = substr($post['content'], 0, 150);
                            echo htmlspecialchars($preview) . '...'; 
                            ?>
                        </p>
                        <div class="flex justify-between items-center text-sm text-gray-500 mb-4">
                            <span>👤 <?php echo htmlspecialchars($post['username']); ?></span>
                            <span>📅 <?php echo date('d M Y', strtotime($post['created_at'])); ?></span>
                        </div>
                        <a href="posts/show.php?id=<?php echo $post['id']; ?>" 
                           class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 inline-block">
                            Baca Selengkapnya
                        </a>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <!-- Jika belum ada post -->
            <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4">
                <p>Belum ada post. Silakan login dan buat post pertama!</p>
            </div>
        <?php endif; ?>
    </div>

</body>
</html>