<?php
// Proteksi agar file tidak dapat diakses langsung
if (!defined('MY_APP')) {
    die('Akses langsung tidak diperbolehkan!');
}

if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id_film = $_GET['id'];

    // Ambil data film
    $sql = "SELECT * FROM film WHERE id_film = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("i", $id_film);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows != 1) {
        echo "Data film tidak ditemukan";
        exit();
    }
    $film = $result->fetch_assoc();
    $stmt->close();

    // Ambil genre yang sudah dipilih
    $genre_terpilih = [];
    $result_genre = $mysqli->query("SELECT id_genre FROM film_genre WHERE id_film = $id_film");
    while ($row = $result_genre->fetch_assoc()) {
        $genre_terpilih[] = $row['id_genre'];
    }
} else {
    echo "ID Film tidak boleh kosong";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul        = $_POST['judul'];
    $sutradara     = $_POST['sutradara'];
    $produser = $_POST['produser'];

    // Proses upload cover
    $cover_name = $film['cover_film'];
    if (!empty($_FILES['cover']['name'])) {
        $target_dir  = "uploads/";
        $file_name   = time() . '_' . basename($_FILES['cover']['name']);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES['cover']['tmp_name'], $target_file)) {
            $cover_name = $file_name;
        }
    }

    // Proses update ke database
    $sql  = "UPDATE film SET judul = ?, sutradara = ?, produser = ?, cover_film = ? WHERE id_film = ?";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("sssssi", $judul, $sutradara, $produser, $cover_name, $id_film);

    if ($stmt->execute()) {
        // Update genre
        $mysqli->query("DELETE FROM film_genre WHERE id_film = $id_film");
        if (!empty($_POST['genre'])) {
            $stmt_genre = $mysqli->prepare("INSERT INTO film_genre (id_film, id_genre) VALUES (?, ?)");
            foreach ($_POST['genre'] as $id_genre) {
                $stmt_genre->bind_param("ii", $id_film, $id_genre);
                $stmt_genre->execute();
            }
            $stmt_genre->close();
        }

        $pesan = "Film berhasil diubah.";
    } else {
        $pesan_error = "Gagal mengubah film.";
    }

    $stmt->close();
}
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Film</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Ubah Film</li>
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
                    <label for="judul" class="form-label">Judul Film</label>
                    <input type="text" name="judul" class="form-control" id="judul" value="<?= htmlspecialchars($film['judul']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Pilih Genre</label><br>
                    <?php
                    $sql_genre    = "SELECT * FROM genre ORDER BY nama_genre ASC";
                    $result_genre = $mysqli->query($sql_genre);
                    while ($genre = $result_genre->fetch_assoc()) :
                        $checked = in_array($genre['id_genre'], $genre_terpilih) ? 'checked' : '';
                    ?>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="genre[]" value="<?= $genre['id_genre'] ?>" id="genre<?= $genre['id_genre'] ?>" <?= $checked ?>>
                            <label class="form-check-label" for="genre<?= $genre['id_genre'] ?>"><?= htmlspecialchars($genre['nama_genre']) ?></label>
                        </div>
                    <?php endwhile; ?>
                </div>

                <div class="mb-3">
                    <label for="sutradara" class="form-label">Sutradara</label>
                    <input type="text" name="sutradara" class="form-control" id="sutradara" value="<?= htmlspecialchars($film['sutradara']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="produser" class="form-label">Produser</label>
                    <input type="text" name="produser" class="form-control" id="produser" value="<?= htmlspecialchars($film['produser']) ?>" required>
                </div>

                <div class="mb-3">
                    <label for="cover" class="form-label">Cover Film</label><br>
                    <?php if (!empty($film['cover_film'])) : ?>
                        <img src="uploads/film/<?= $film['cover_film'] ?>" alt="Cover Film" width="100" style="margin-bottom:10px;"><br>
                    <?php endif ?>
                    <input type="file" name="cover" class="form-control" id="cover">
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
                <a href="index.php?hal=daftar-film" class="btn btn-danger">Kembali</a>
            </form>
        </div>
    </div>
</div>