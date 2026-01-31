-- Database untuk Blog CRUD
-- Pastikan buat database dulu, lalu import file ini

-- Buat database
CREATE DATABASE IF NOT EXISTS blog_crud;
USE blog_crud;

-- Tabel users
CREATE TABLE users (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel posts
CREATE TABLE posts (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,
    user_id INT(11) NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert data contoh (optional)
-- Password untuk semua user contoh: "password123"

INSERT INTO users (username, email, password) VALUES
('admin', 'admin@blog.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'),
('john_doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

INSERT INTO posts (user_id, title, content) VALUES
(1, 'Selamat Datang di Blog CRUD!', 'Ini adalah post pertama di blog kami. Blog ini dibuat menggunakan PHP native, MySQL, dan Tailwind CSS. Semoga bermanfaat untuk belajar!'),
(1, 'Tutorial PHP untuk Pemula', 'PHP adalah bahasa pemrograman server-side yang sangat populer. Dengan PHP, kita bisa membuat website dinamis dengan mudah. Mari belajar bersama!'),
(2, 'Tips Belajar Programming', 'Belajar programming memang tidak mudah, tapi dengan konsisten dan praktek terus menerus, pasti bisa! Jangan menyerah ya!');