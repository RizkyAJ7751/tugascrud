<?php
session_start();
$server = "localhost";
$username = "root";
$password = "";
$database = "db_crud";

// Buat Koneksi
$koneksi = mysqli_connect($server, $username, $password, $database) or die(mysqli_error($koneksi));

// Jika pengguna sudah login, arahkan mereka ke halaman index
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

if (isset($_POST['register'])) {
    // Pastikan data tersedia dan tidak kosong
    $username = isset($_POST['username']) ? mysqli_real_escape_string($koneksi, $_POST['username']) : '';
    $email = isset($_POST['email']) ? mysqli_real_escape_string($koneksi, $_POST['email']) : '';
    $password = isset($_POST['password']) ? mysqli_real_escape_string($koneksi, $_POST['password']) : '';
    
    if (empty($username) || empty($email) || empty($password)) {
        $register_error = "Semua field harus diisi.";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Cek apakah email atau username sudah digunakan
        $cek_email = mysqli_query($koneksi, "SELECT * FROM users WHERE email = '$email'");
        $cek_username = mysqli_query($koneksi, "SELECT * FROM users WHERE username = '$username'");

        if (mysqli_num_rows($cek_email) > 0) {
            $register_error = "Email sudah digunakan.";
        } elseif (mysqli_num_rows($cek_username) > 0) {
            $register_error = "Username sudah digunakan.";
        } else {
            $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password_hash')";
            if (mysqli_query($koneksi, $sql)) {
                header('Location: login.php');
                exit();
            } else {
                $register_error = "Gagal mendaftar. Silakan coba lagi.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        .container {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 2rem;
            background-color: #ffffff;
            box-shadow: 0 0 1rem rgba(0, 0, 0, 0.1);
        }

        .form-label {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container col-4">
        <h2 class="text-center mb-4">Register</h2>
        <form method="POST" class="col-md-8 mx-auto">
            <?php if (isset($register_error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($register_error) ?></div>
            <?php endif; ?>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" name="username" id="username" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" name="email" id="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" name="password" id="password" required>
            </div>
            <button type="submit" name="register" class="btn btn-primary">Register</button>
        </form>
        <div class="text-center mt-3">
            <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
        </div>
    </div>
</body>
</html>
