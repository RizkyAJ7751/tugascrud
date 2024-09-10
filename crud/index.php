<?php
// Koneksi
$server = "localhost";
$username = "root";
$password = "";
$database = "db_crud";

// Buat Koneksi
$koneksi = mysqli_connect($server, $username, $password, $database);
if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

// Kode Otomatis
$isitabel = mysqli_query($koneksi, "SELECT kode FROM tb_barang ORDER BY kode DESC LIMIT 1");
$dataterbaru = mysqli_fetch_array($isitabel);

if ($dataterbaru) {
    $no_terakhir = (int)substr($dataterbaru['kode'], strpos($dataterbaru['kode'], '-') + 1);
    $no = $no_terakhir + 1;
    $kodebaru = "BRG-" . $no;
} else {
    $kodebaru = "BRG-1";
}

session_start();

// Cek jika pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Logout
if (isset($_POST['logout'])) {
    session_unset();
    session_destroy();
    header('Location: login.php');
    exit();
}
// Validasi dan proses input
if (isset($_POST["submit"])) {
    $kode = trim($_POST['kodebarang']);
    $nama = trim($_POST['namabarang']);
    $asal = trim($_POST['asalbarang']);
    $jumlah = trim($_POST['jumlahbarang']);
    $satuan = trim($_POST['unit']);
    $tanggal_diterima = trim($_POST['tanggalditerima']);
    $gambar = '';

    // Validasi input
    if (empty($kode) || empty($nama) || empty($asal) || empty($jumlah) || empty($satuan) || empty($tanggal_diterima)) {
        echo "<script>
                alert('Semua field harus diisi.');
                document.location='index.php';
            </script>";
        exit();
    }
    // Proses upload gambar
    if (isset($_FILES['gambarbarang']) && $_FILES['gambarbarang']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['gambarbarang']['tmp_name'];
        $fileName = $_FILES['gambarbarang']['name'];
        $fileSize = $_FILES['gambarbarang']['size'];
        $fileType = $_FILES['gambarbarang']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        $allowedExts = array('jpg', 'jpeg', 'png');

        // Cek ekstensi file
        if (in_array($fileExtension, $allowedExts)) {
            // Cek ukuran file (maks 5MB)
            if ($fileSize < 5000000) {
                $uploadFileDir = './uploads/';
                if (!is_dir($uploadFileDir)) {
                    mkdir($uploadFileDir, 0777, true);
                }
                $dest_file_path = $uploadFileDir . $fileName;

                if (move_uploaded_file($fileTmpPath, $dest_file_path)) {
                    $gambar = $fileName;
                } else {
                    echo "<script>
                        alert('Gagal meng-upload gambar.');
                        document.location='index.php';
                    </script>";
                    exit();
                }
            } else {
                echo "<script>
                    alert('Ukuran file terlalu besar.');
                    document.location='index.php';
                </script>";
                exit();
            }
        } else {
            echo "<script>
                alert('Hanya file gambar (jpg, jpeg, png) yang diizinkan.');
                document.location='index.php';
            </script>";
            exit();
        }
    } else {
        // Handle error case
        if ($_FILES['gambarbarang']['error'] !== UPLOAD_ERR_NO_FILE) {
            echo "<script>
                alert('Terjadi kesalahan saat meng-upload file.');
                document.location='index.php';
            </script>";
            exit();
        }
    }


    if (isset($_GET['hal']) && $_GET['hal'] == "edit") {
        $sql = "UPDATE tb_barang SET kode = ?, nama = ?, gambar = ?, asal = ?, jumlah = ?, satuan = ?, tanggal_diterima = ? WHERE id = ?";
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("ssssissi", $kode, $nama, $gambar, $asal, $jumlah, $satuan, $tanggal_diterima, $_GET['id']);
    } else {
        $sql = "INSERT INTO tb_barang (kode, nama, gambar, asal, jumlah, satuan, tanggal_diterima) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $koneksi->prepare($sql);
        $stmt->bind_param("sssssss", $kode, $nama, $gambar, $asal, $jumlah, $satuan, $tanggal_diterima);
    }

    if ($stmt->execute()) {
        $message = isset($_GET['hal']) && $_GET['hal'] == "edit" ? "Berhasil Edit Data!" : "Berhasil Simpan Data!";
        echo "<script>
                alert('$message');
                document.location='index.php';
            </script>";
    } else {
        echo "<script>
                alert('Gagal menyimpan data: " . $stmt->error . "');
                document.location='index.php';
            </script>";
    }
    $stmt->close();
}


// Deklarasi Variabel Penampung Data yang Akan Diedit
$vkode = $kodebaru;
$vnama = "";
$vgambar = "";
$vasal = "";
$vjumlah = "";
$vsatuan = "";
$vtanggal_diterima = "";

// Pengujian Tombol Edit / Hapus Ketika DiKlik
if (isset($_GET['hal'])) {
    if ($_GET['hal'] == 'edit') {
        $id = intval($_GET['id']);
        $tampil = $koneksi->prepare("SELECT * FROM tb_barang WHERE id = ?");
        $tampil->bind_param("i", $id);
        $tampil->execute();
        $data = $tampil->get_result()->fetch_assoc();
        if ($data) {
            $vkode = $data["kode"];
            $vnama = $data["nama"];
            $vgambar = $data["gambar"];
            $vasal = $data["asal"];
            $vjumlah = $data["jumlah"];
            $vsatuan = $data["satuan"];
            $vtanggal_diterima = $data["tanggal_diterima"];
        }
        $tampil->close();
    } else if ($_GET['hal'] == 'hapus') {
        $id = intval($_GET['id']);
        $hapus = $koneksi->prepare("DELETE FROM tb_barang WHERE id = ?");
        $hapus->bind_param("i", $id);
        if ($hapus->execute()) {
            echo "<script>
                    alert('Berhasil Hapus Data!');
                    document.location='index.php';
                </script>";
        } else {
            echo "<script>
                    alert('Gagal Hapus Data!');
                    document.location='index.php';
                </script>";
        }
        $hapus->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Inventaris</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-header {
            background: linear-gradient(135deg, #5a9, #58d);
            color: #fff;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.3);
        }

        .card-footer {
            background: #f8f9fa;
            box-shadow: 0 -2px 8px rgba(0, 0, 0, 0.1);
        }

        .form-control:hover,
        .form-select:hover {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }

        .btn {
            transition: transform 0.2s ease;
        }

        .btn:hover {
            transform: scale(1.05);
        }

        .table-striped>tbody>tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>

<body class="p-3">
    <div class="container">
        <div class="d-flex justify-content-end gap-3 mb-4">
            <?php if (isset($_SESSION['user_id'])): ?>
                <form method="POST" action="" class="d-inline">
                    <button type="submit" name="logout" class="btn btn-danger" onclick="return confirm('Apakah anda yakin ingin logout?')">Logout</button>
                </form>
            <?php endif; ?>
        </div>
        <h2 class="text-center mb-2">Data Inventaris</h2>
        <h3 class="text-secondary text-center mb-4">PT.Serbaguna Aqil Jaya</h3>

        <div class="card col-lg-8 mx-auto mb-4">
            <div class="card-header text-center">Form Data Barang</div>
            <form method="POST" enctype="multipart/form-data" class="card-body px-3 py-1">
                <div class="mb-3">
                    <label for="kode-barang" class="form-label">Kode Barang</label>
                    <input type="text" class="form-control" name="kodebarang" value="<?= $vkode ?>" id="kode-barang" placeholder="Kode Barang" required />
                </div>
                <div class="mb-3">
                    <label for="nama-barang" class="form-label">Nama Barang</label>
                    <input type="text" class="form-control" name="namabarang" value="<?= $vnama ?>" id="nama-barang" placeholder="Nama Barang" required />
                </div>
                <div class="mb-3">
                    <label for="gambar-barang" class="form-label">Gambar Barang</label>
                    <input type="file" class="form-control" name="gambarbarang" accept=".jpg,.png,.jpeg" id="gambar-barang" required />
                </div>
                <div class="mb-3">
                    <label for="asal-barang" class="form-label">Asal Barang</label>
                    <select name="asalbarang" id="asal-barang" class="form-select" required>
                        <option value="<?= $vasal ?>"><?= $vasal ? $vasal : 'Pilih Asal Barang' ?></option>
                        <option value="Sumatera">Sumatera</option>
                        <option value="Jawa">Jawa</option>
                        <option value="Kalimantan">Kalimantan</option>
                        <option value="Sulawesi">Sulawesi</option>
                        <option value="Papua">Papua</option>
                    </select>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="jumlah-barang" class="form-label">Jumlah Barang</label>
                        <input type="number" class="form-control" name="jumlahbarang" value="<?= $vjumlah ?>" id="jumlah-barang" placeholder="Jumlah Barang" required />
                    </div>
                    <div class="col-md-6">
                        <label for="unit-barang" class="form-label">Unit Barang</label>
                        <select name="unit" id="unit-barang" class="form-select" required>
                            <option value="<?= $vsatuan ?>"><?= $vsatuan ? $vsatuan : 'Pilih Unit Barang' ?></option>
                            <option value="Kg">Kg</option>
                            <option value="Pcs">Pcs</option>
                            <option value="Box">Box</option>
                            <option value="Pack">Pack</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="tanggal-diterima" class="form-label">Tanggal Diterima</label>
                    <input type="date" name="tanggalditerima" value="<?= $vtanggal_diterima ?>" id="tanggal-diterima" class="form-control" required />
                </div>
                <div class="card-footer text-center">
                    <button class="btn btn-primary" name="submit" type="submit">Simpan</button>
                    <button class="btn btn-danger" name="reset" type="reset">Kosongkan</button>
                </div>
            </form>


        </div>

        <div class="card col-12">
            <div class="card-header">Data Barang</div>
            <div class="card-body">
                <div class="container-fluid d-flex align-items-center justify-content-center">
                    <form method="POST" class="col-8 my-2 text-center d-flex align-items-center justify-content-center">
                        <input type="search" name="cari" id="cari" value="<?= @$_POST['cari'] ?>" class="form-control" placeholder="Cari Barang">
                        <button class="btn btn-primary" name="bcari" type="submit">Cari</button>
                        <button class="btn btn-danger" name="breset" type="submit">Reset</button>
                    </form>
                </div>
                <table class="table table-striped table-hover table-bordered">
                    <tr>
                        <th>No.</th>
                        <th>Kode Barang</th>
                        <th>Nama Barang</th>
                        <th>Gambar Barang</th>
                        <th>Asal Barang</th>
                        <th>Jumlah</th>
                        <th>Tanggal Diterima</th>
                        <th>Perbarui</th>
                    </tr>
                    <?php
                    $no = 1;
                    if (isset($_POST['bcari'])) {
                        $keyword = $_POST['cari'];
                        $isitabel = "SELECT * FROM tb_barang WHERE kode LIKE '%$keyword%' OR nama LIKE '%$keyword%' OR asal LIKE '%$keyword%' ORDER BY id DESC";
                    } else {
                        $isitabel = "SELECT * FROM tb_barang ORDER BY id DESC";
                    }

                    $tampilkan = mysqli_query($koneksi, $isitabel);
                    while ($data = mysqli_fetch_array($tampilkan)) :
                    ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $data['kode'] ?></td>
                            <td><?= $data['nama'] ?></td>
                            <td><img src="./uploads/<?= htmlspecialchars($data['gambar']) ?>" alt="Gambar Barang" style="max-width:100px; max-height:100px;"></td>
                            <td><?= $data['asal'] ?></td>
                            <td><?= $data['jumlah'] . ' ' . $data['satuan'] ?></td>
                            <td><?= $data['tanggal_diterima'] ?></td>
                            <td>
                                <a href="index.php?hal=edit&id=<?= $data['id'] ?>" class="btn btn-primary btn-sm text-light">Edit</a>
                                <a href="index.php?hal=hapus&id=<?= $data['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Apakah anda ingin menghapus?')">Hapus</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </table>

            </div>
        </div>
    </div>
</body>

</html>
