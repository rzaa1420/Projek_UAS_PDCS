<?php
// Proteksi agar file tidak dapat diakses langsung
if (!defined('MY_APP')) {
    die('Akses langsung tidak diperbolehkan!');
}

// Query untuk buku
$sql = "SELECT * FROM buku ORDER BY id_buku DESC";
$result = $mysqli->query($sql);
if (!$result) {
    die("QUERY Error: " . $mysqli->error);
}

// Query kategori per buku
$kategori_per_buku = [];
$sql_kategori = "
    SELECT bk.id_buku, kb.nama_kategori 
    FROM buku_kategori bk 
    JOIN kategori kb ON bk.id_kategori = kb.id_kategori
";
$result_kategori = $mysqli->query($sql_kategori);
if ($result_kategori) {
    while ($row = $result_kategori->fetch_assoc()) {
        $kategori_per_buku[$row['id_buku']][] = $row['nama_kategori'];
    }
}
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Buku</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Daftar Buku</li>
    </ol>

    <div class="card mb-4">
        <div class="card-body">
            <a href="index.php?hal=tambah-kategori" class="btn btn-success mb-3">Tambah Kategori</a>

            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Judul</th>
                        <th>Kategori</th>
                        <th>Penulis</th>
                        <th>Penerbit</th>
                        <th>Tahun</th>
                        <th>Stok</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1 ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if (!empty($row['cover_buku'])): ?>
                                        <img src="uploads/buku/<?= $row['cover_buku'] ?>" alt="Cover Buku" width="50"
                                            height="70" style="object-fit: cover; border-radius: 5px; margin-right: 10px;" />
                                    <?php else: ?>
                                        <div
                                            style="width: 50px; height:70px; background:#ddd; border-radius:5px; margin-right:10px; display:flex; align-items:center; justify-content: center; color: #999;">
                                            No Cover</div>
                                    <?php endif ?>
                                    <span><?= htmlspecialchars($row['judul']) ?></span>
                                </div>
                            </td>
                            <td>
                                <?php
                                if (isset($kategori_per_buku[$row['id_buku']])) {
                                    echo implode(', ', $kategori_per_buku[$row['id_buku']]);
                                } else {
                                    echo "<em>Tidak ada kategori</em>";
                                }
                                ?>
                            </td>
                            <td><?= htmlspecialchars($row['penulis']) ?></td>
                            <td><?= htmlspecialchars($row['penerbit']) ?></td>
                            <td><?= htmlspecialchars($row['tahun_terbit']) ?></td>
                            <td><?= htmlspecialchars($row['stok']) ?></td>
                            <td>
                                <a href="index.php?hal=ubah-buku&id=<?= $row['id_buku'] ?>"
                                    class="btn btn-warning btn-sm">Ubah</a>
                            </td>
                        </tr>
                        <?php $no++ ?>
                    <?php endwhile;
                    $mysqli->close(); ?>
                </tbody>
            </table>
        </div>
    </div>
</div>