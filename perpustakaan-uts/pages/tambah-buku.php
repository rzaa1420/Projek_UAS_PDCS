<?php
// Proteksi agar file tidak dapat diakses langsung
if (!defined('MY_APP')) {
    die('Akses langsung tidak diperbolehkan!');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul        = $_POST['judul'];
    $penulis      = $_POST['penulis'];
    $penerbit     = $_POST['penerbit'];
    $tahun_terbit = $_POST['tahun_terbit'];
    $stok         = $_POST['stok'];

    // Proses upload cover
    $cover_name = null;
    if (!empty($_FILES['cover']['name'])) {
        $target_dir  = "uploads/buku/";
        $file_name   = time() . '_' . basename($_FILES['cover']['name']);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES['cover']['tmp_name'], $target_file)) {
            $cover_name = $file_name;
        }
    }

    // Proses insert ke database
    $sql  = "INSERT INTO buku (judul, penulis, penerbit, tahun_terbit, stok, cover_buku) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ssssss", $judul, $penulis, $penerbit, $tahun_terbit, $stok, $cover_name);

    if ($stmt->execute()) {
        $id_buku = $stmt->insert_id;

        // Proses kategori
        if (!empty($_POST['kategori'])) {
            $stmt_kat = $mysqli->prepare("INSERT INTO buku_kategori (id_buku, id_kategori) VALUES (?, ?)");
            foreach ($_POST['kategori'] as $id_kategori) {
                $stmt_kat->bind_param("ii", $id_buku, $id_kategori);
                $stmt_kat->execute();
            }
            $stmt_kat->close();
        }

        $pesan = "Buku berhasil ditambahkan.";
    } else {
        $pesan_error = "Gagal menambahkan buku.";
    }

    $stmt->close();
}
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Buku</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Tambah Buku</li>
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
                    <input type="text" name="judul" class="form-control" id="judul" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Pilih Kategori</label><br>
                    <?php
                    $sql_kategori    = "SELECT * FROM kategori ORDER BY nama_kategori ASC";
                    $result_kategori = $mysqli->query($sql_kategori);
                    while ($kat = $result_kategori->fetch_assoc()) :
                    ?>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="kategori[]" value="<?= $kat['id_kategori'] ?>" id="kat<?= $kat['id_kategori'] ?>">
                            <label class="form-check-label" for="kat<?= $kat['id_kategori'] ?>"><?= htmlspecialchars($kat['nama_kategori']) ?></label>
                        </div>
                    <?php endwhile; ?>
                </div>

                <div class="mb-3">
                    <label for="penulis" class="form-label">Penulis</label>
                    <input type="text" name="penulis" class="form-control" id="penulis" required>
                </div>

                <div class="mb-3">
                    <label for="penerbit" class="form-label">Penerbit</label>
                    <input type="text" name="penerbit" class="form-control" id="penerbit" required>
                </div>

                <div class="mb-3">
                    <label for="tahun_terbit" class="form-label">Tahun Terbit</label>
                    <input type="text" name="tahun_terbit" class="form-control" id="tahun_terbit" required>
                </div>

                <div class="mb-3">
                    <label for="stok" class="form-label">Stok</label>
                    <input type="number" name="stok" class="form-control" id="stok" required>
                </div>

                <div class="mb-3">
                    <label for="cover" class="form-label">Cover Buku</label>
                    <input type="file" name="cover" class="form-control" id="cover">
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
</div>