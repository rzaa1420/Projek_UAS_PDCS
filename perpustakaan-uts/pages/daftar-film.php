<?php
// Proteksi agar file tidak dapat diakses langsung
if (!defined('MY_APP')) {
    die('Akses langsung tidak diperbolehkan!');
}

// Query untuk film
$sql = "SELECT * FROM film ORDER BY id_film DESC";
$result = $mysqli->query($sql);
if (!$result) {
    die("QUERY Error: " . $mysqli->error);
}

// Query genre per film
$genre_per_film = [];
$sql_genre = "
    SELECT fm.id_film, fg.nama_genre 
    FROM film_genre fm 
    JOIN genre fg ON fm.id_genre = fg.id_genre
";
$result_genre = $mysqli->query($sql_genre);
if ($result_genre) {
    while ($row = $result_genre->fetch_assoc()) {
        $genre_per_film[$row['id_film']][] = $row['nama_genre'];
    }
}
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Film</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Daftar Film</li>
    </ol>

    <div class="card mb-4">
        <div class="card-body">
            <a href="index.php?hal=tambah-genre" class="btn btn-success mb-3">Tambah Genre</a>

            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Judul</th>
                        <th>Genre</th>
                        <th>Sutradara</th>
                        <th>Produser</th>
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
                                    <?php if (!empty($row['cover_film'])): ?>
                                        <img src="uploads/film/<?= $row['cover_film'] ?>" alt="Cover Film" width="50"
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
                                if (isset($genre_per_film[$row['id_film']])) {
                                    echo implode(', ', $genre_per_film[$row['id_film']]);
                                } else {
                                    echo "<em>Tidak ada genre</em>";
                                }
                                ?>
                            </td>
                            <td><?= htmlspecialchars($row['sutradara']) ?></td>
                            <td><?= htmlspecialchars($row['produser']) ?></td>
                            <td>
                                <a href="index.php?hal=ubah-film&id=<?= $row['id_film'] ?>"
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