<?php
require "config.php";
// Inisialisasi variabel pesan
$success = '';
$error = '';

// Proses form signup
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['daftar'])) {
    $username = $_POST['nama'];
    $password = $_POST['password'];
    $telepon = $_POST['telepon'];

    // Validasi input
    if (empty($username) || empty($password) || empty($telepon)) {
        $error = "Semua field harus diisi.";
    } else {
        try {
            // Cek apakah username sudah ada
            $sql_check = "SELECT id FROM pengguna WHERE nama = :username";
            $query_check = $conn->prepare($sql_check);
            $query_check->bindParam(':username', $username, PDO::PARAM_STR);
            $query_check->execute();
            if ($query_check->rowCount() > 0) {
                $error = "Username sudah digunakan.";
            } else {
                // Enkripsi password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                // Simpan data ke tabel pengguna
                $sql_insert = "INSERT INTO pengguna (nama, password, telepon, level) VALUES (:username, :password, :telepon, 'User')";
                $query_insert = $conn->prepare($sql_insert);
                $query_insert->bindParam(':username', $username, PDO::PARAM_STR);
                $query_insert->bindParam(':password', $hashed_password, PDO::PARAM_STR);
                $query_insert->bindParam(':telepon', $telepon, PDO::PARAM_STR);
                $query_insert->execute();

                $success = "Registrasi berhasil! Anda akan diarahkan ke halaman login.";
                // Redirect ke halaman login setelah 2 detik
                header("refresh:2;url=index.php");
            }
        } catch (PDOException $e) {
            $error = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            background: linear-gradient(135deg, #2d3748, #4a5568);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Poppins', sans-serif;
            overflow: hidden;
        }
        .login-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            max-width: 500px;
            width: 100%;
            padding: 3rem;
            animation: fadeIn 1s ease-in-out;
        }
        .form-control {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            transition: all 0.3s ease;
            font-size: 1.1rem;
            padding: 12px;
        }
        .form-control:focus {
            background: rgba(255, 255, 255, 0.2);
            border-color: #f59e0b;
            box-shadow: 0 0 0 0.2rem rgba(245, 158, 11, 0.3);
            color: white;
        }
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        .btn-primary {
            background: linear-gradient(90deg, #f59e0b, #d97706);
            border: none;
            border-radius: 10px;
            padding: 14px;
            font-weight: 600;
            font-size: 1.1rem;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.5);
            background: linear-gradient(90deg, #d97706, #f59e0b);
        }
        .input-group-text {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            cursor: pointer;
            font-size: 1.1rem;
            padding: 12px;
        }
        .text-glow {
            text-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-5">
            <h1 class="h2 fw-bold text-white text-glow">Sign-up</h1>
        </div>
        <form method = "POST">
            <div class="mb-4">
                <label for="username" class="form-label text-white fs-5">Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" name = "nama" class="form-control" id="username" placeholder="Masukkan username" required>
                </div>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label text-white fs-5">Password</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-lock"></i></span>
                    <input type="password" name = "password" class="form-control" id="password" placeholder="Masukkan password" required>
                    <span class="input-group-text toggle-password"><i class="fas fa-eye"></i></span>
                </div>
            </div>
            <div class="mb-4">
                <label for="username" class="form-label text-white fs-5">Nomor Telepon</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-phone" style="color: #ffffff;"></i></span>
                    <input type="text" name = "telepon" class="form-control" id="telepon" placeholder="Masukkan no. telp" required>
                </div>
            </div>
            <button type="submit" name= "daftar" class="btn btn-primary w-100">Daftar</button>
        </form>
        <div class="text-center mt-3">
            <p class="text-white">Sudah punya akun? <a href="index.php" class="text-warning">Login di sini</a></p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        document.querySelector('.toggle-password').addEventListener('click', function () {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
    </script>
</body>
</html>