<?php
// Proteksi agar file tidak dapat diakses langsung
if (!defined('MY_APP')) {
    die('Akses langsung tidak diperbolehkan!');
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_buku = $_GET['id'];

    // Ambil data buku
    $sql = "SELECT * FROM buku WHERE id_buku = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $id_buku);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows != 1) {
        echo "Data buku tidak ditemukan";
        exit();
    }
    $buku = $result->fetch_assoc();
    $stmt->close();

    // Ambil kategori yang sudah dipilih
    $kategori_terpilih = [];
    $result_kategori = $mysqli->query("SELECT id_kategori FROM buku_kategori WHERE id_buku = $id_buku");
    while ($row = $result_kategori->fetch_assoc()) {
        $kategori_terpilih[] = $row['id_kategori'];
    }
} else {
    echo "ID Buku tidak boleh kosong";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul        = $_POST['judul'];
    $penulis      = $_POST['penulis'];
    $penerbit     = $_POST['penerbit'];
    $tahun_terbit = $_POST['tahun_terbit'];
    $stok         = $_POST['stok'];

    // Proses upload cover
    $cover_name = $buku['cover_buku'];
    if (!empty($_FILES['cover']['name'])) {
        $target_dir  = "uploads/";
        $file_name   = time() . '_' . basename($_FILES['cover']['name']);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES['cover']['tmp_name'], $target_file)) {
            $cover_name = $file_name;
        }
    }

    // Proses update ke database
    $sql  = "UPDATE buku SET judul = ?, penulis = ?, penerbit = ?, tahun_terbit = ?, stok = ?, cover_buku = ? WHERE id_buku = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ssssssi", $judul, $penulis, $penerbit, $tahun_terbit, $stok, $cover_name, $id_buku);

    if ($stmt->execute()) {
        // Update kategori
        $mysqli->query("DELETE FROM buku_kategori WHERE id_buku = $id_buku");
        if (!empty($_POST['kategori'])) {
            $stmt_kat = $mysqli->prepare("INSERT INTO buku_kategori (id_buku, id_kategori) VALUES (?, ?)");
            foreach ($_POST['kategori'] as $id_kategori) {
                $stmt_kat->bind_param("ii", $id_buku, $id_kategori);
                $stmt_kat->execute();
            }
            $stmt_kat->close();
        }

        $pesan = "Buku berhasil diubah.";
    } else {
        $pesan_error = "Gagal mengubah buku.";
    }

    $stmt->close();
}
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Buku</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Ubah Buku</li>
    </ol>

    <?php if (!empty($pesan)) : ?>
        <div class="alert alert-success" role="alert">
            <?= htmlspecialchars($pesan) ?>
        </div>
    <?php endif ?>

    <?php if (!empty($pesan_error)) : ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($pesan_error) ?>
        </div>
    <?php endif ?>

    <div class="card mb-4">
        <div class="card-body">
            <form method="post" enctype="multipart/form-data">
                <div class="mb-3">
                    <label for="judul" class="form-label">Judul Buku</label>
                    <input type="text" name="judul" class="form-control" id="judul" value="<?= htmlspecialchars($buku['judul']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Pilih Kategori</label><br>
                    <?php
                    $sql_kategori    = "SELECT * FROM kategori ORDER BY nama_kategori ASC";
                    $result_kategori = $mysqli->query($sql_kategori);
                    while ($kat = $result_kategori->fetch_assoc()) :
                        $checked = in_array($kat['id_kategori'], $kategori_terpilih) ? 'checked' : '';
                    ?>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="kategori[]" value="<?= $kat['id_kategori'] ?>" id="kat<?= $kat['id_kategori'] ?>" <?= $checked ?>>
                            <label class="form-check-label" for="kat<?= $kat['id_kategori'] ?>"><?= htmlspecialchars($kat['nama_kategori']) ?></label>
                        </div>
                    <?php endwhile; ?>
                </div>

                <div class="mb-3">
                    <label for="penulis" class="form-label">Penulis</label>
                    <input type="text" name="penulis" class="form-control" id="penulis" value="<?= htmlspecialchars($buku['penulis']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="penerbit" class="form-label">Penerbit</label>
                    <input type="text" name="penerbit" class="form-control" id="penerbit" value="<?= htmlspecialchars($buku['penerbit']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="tahun_terbit" class="form-label">Tahun Terbit</label>
                    <input type="text" name="tahun_terbit" class="form-control" id="tahun_terbit" value="<?= htmlspecialchars($buku['tahun_terbit']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="stok" class="form-label">Stok</label>
                    <input type="number" name="stok" class="form-control" id="stok" value="<?= htmlspecialchars($buku['stok']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="cover" class="form-label">Cover Buku</label><br>
                    <?php if (!empty($buku['cover_buku'])) : ?>
                        <img src="uploads/buku/<?= $buku['cover_buku'] ?>" alt="Cover Buku" width="100" style="margin-bottom:10px;"><br>
                    <?php endif ?>
                    <input type="file" name="cover" class="form-control" id="cover">
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="index.php?hal=daftar-buku" class="btn btn-danger">Kembali</a>
            </form>
        </div>
    </div>
</div>