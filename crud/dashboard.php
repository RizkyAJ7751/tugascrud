<?php
// Koneksi ke database
$server = "localhost";
$username = "root";
$password = "";
$database = "db_crud";

$koneksi = mysqli_connect($server, $username, $password, $database) or die(mysqli_error($koneksi));

// Mulai session
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Ambil data pengguna
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = '$user_id'";
$result = mysqli_query($koneksi, $sql);
$user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        
        .container {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 2rem;
            background-color: #ffffff;
            box-shadow: 0 0 1rem rgba(0, 0, 0, 0.1);
        }

        .btn-info {
            background-color: #17a2b8;
            border: none;
        }
        
        .btn-info:hover {
            background-color: #138496;
        }
        
        .btn-danger {
            background-color: #dc3545;
            border: none;
        }
        
        .btn-danger:hover {
            background-color: #c82333;
        }
    </style>
</head>

<body>
    <div class="container col-4 p-5">
        <h2 class="text-center mb-4">Dashboard</h2>
        <div class="text-center">
            <p class="mb-4">Selamat datang, <?= htmlspecialchars($user['username']) ?>!</p>
            <a href="index.php" class="btn btn-info text-light me-2">Data Inventaris</a>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-2r29OnnUQIBlG9GxH59XOGy8BD04w4L6tjFVF5E8MHA2a7IO4P39i2aYJ8eHiE7h" crossorigin="anonymous"></script>
</body>

</html>
