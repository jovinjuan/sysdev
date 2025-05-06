<?php
session_start();
require 'config.php'; 

if (isset($_POST['simpan'])) {
    $nama = $_POST['nama'];
    $password = $_POST['password'];

    if (isset($conn)) {
        // Cek apakah nama ada di database
        $cekNama = $conn->prepare("SELECT * FROM pengguna WHERE nama = :nama");
        $cekNama->bindParam(':nama', $nama);
        $cekNama->execute();

        if ($cekNama->rowCount() > 0) {
            // Nama ditemukan, sekarang cek password
            $user = $cekNama->fetch(PDO::FETCH_ASSOC);

            if ($user['password'] === $password) {
                // Password cocok
                if ($user['level'] === "Admin") {
                    $_SESSION['admin_id'] = $user['id'];
                    $_SESSION['username'] = $user['nama'];
                    header("Location: dashboardadmin.php");
                    exit;
                } elseif ($user['level'] === "User") {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['nama'];
                    header("Location: dashboardpelanggan.php");
                    exit;
                }
            } else {
                // Password salah
                $_SESSION['error'] = "Password salah.";
                header('Location: index.php');
                exit;
            }
        } else {
            // Nama tidak ditemukan
            $_SESSION['error'] = "Nama tidak ditemukan.";
            header('Location: index.php');
            exit;
        }
    }
}
?>
