<?php
//proteksi agar file tidak dapat diakses langsung
if (!defined('MY_APP')) {
    die('Akses langsung tidak diperbolehkan!');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_genre = $_POST['nama_genre'];
    $sql = "INSERT INTO genre (nama_genre) VALUES (?)";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("s", $nama_genre);
        if ($stmt->execute()) {
            $pesan = "Data genre berhasil di simpan";
        } else {
            $pesan_error = "Terjadi kesalahan saat menyimpan data";
        }
        $stmt->close();
    }
    $mysqli->close();
}
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Genre</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Tambah Genre</li>
    </ol>
    <?php if (!empty($pesan)): ?>
        <div class="alert alert-success" role="alert">
            <?php echo $pesan ?>
        </div>
    <?php endif ?>

    <?php if (!empty($pesan_error)): ?>
        <div class="alert alert-danger" role="alert">
            <?php echo $pesan_error ?>
        </div>
    <?php endif ?>
    <div class="card mb-4">
        <div class="card-body">
            <form method="post">
                <div class="mb-3">
                    <label for="nama_genre" class="form-label">Nama Genre</label>
                    <input type="text" name="nama_genre" class="form-control" id="nama_genre" required>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="index.php?hal=daftar-genre" class="btn btn-danger">Kembali</a>
            </form>
        </div>
    </div>
</div>