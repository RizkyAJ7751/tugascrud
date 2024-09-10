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

if (isset($_POST['login'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Menggunakan prepared statement untuk menghindari SQL Injection
    $stmt = $koneksi->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param('s', $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        header('Location: dashboard.php');
        exit();
    } else {
        $login_error = "Username atau password salah!";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
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
        <h2 class="text-center mb-4">Login</h2>
        <form method="POST" class="col-md-8 mx-auto">
            <?php if (isset($login_error)): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($login_error) ?></div>
            <?php endif; ?>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" name="username" id="username" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" name="password" id="password" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary">Login</button>
            <div class="text-center mt-3">
                <p>Belum punya akun? <a href="register.php">Daftar di sini</a></p>
            </div>
        </form>
    </div>
</body>

</html>
