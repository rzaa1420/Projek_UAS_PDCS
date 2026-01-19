<?php
//proteksi agar file tidak dapat diakses langsung
if (!defined('MY_APP')) {
    die('Akses langsung tidak diperbolehkan!');
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_genre = $_GET['id'];
    $sql = "SELECT * FROM genre WHERE id_genre = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("i", $id_genre);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result->num_rows == 1) {
                $genre = $result->fetch_assoc();
            } else {
                echo "Data genre tidak ditemukan";
                exit();
            }
            $stmt->close();
        }
    } else {
        header("Location: index.php?hal=daftar-genre");
        exit();
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_genre = $_POST['nama_genre'];
    $sql = "UPDATE genre SET nama_genre = ? WHERE id_genre = ?";
    if ($stmt = $mysqli->prepare($sql)) {
        $stmt->bind_param("si", $nama_genre, $id_genre);
        if ($stmt->execute()) {
            $pesan = "Data genre berhasil di ubah";
            $result_genre = $mysqli->query("SELECT * FROM genre WHERE id_genre = $id_genre");
            $genre = $result_genre->fetch_assoc();
        } else {
            $pesan_error = "Terjadi kesalahan saat mengubah data";
        }
        $stmt->close();
    }
    $mysqli->close();
}
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Genre</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Ubah Genre</li>
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
                    <input type="text" name="nama_genre" class="form-control" id="nama_genre" value="<?php echo $genre ['nama_genre'] ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="index.php?hal=daftar-genre" class="btn btn-danger">Kembali</a>
            </form>
        </div>
    </div>
</div>