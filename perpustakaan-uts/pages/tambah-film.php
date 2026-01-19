<?php
// Proteksi agar file tidak dapat diakses langsung
if (!defined('MY_APP')) {
    die('Akses langsung tidak diperbolehkan!');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $judul        = $_POST['judul'];
    $sutradara     = $_POST['sutradara'];
    $produser = $_POST['produser'];

    // Proses upload cover
    $cover_name = null;
    if (!empty($_FILES['cover']['name'])) {
        $target_dir  = "uploads/film/";
        $file_name   = time() . '_' . basename($_FILES['cover']['name']);
        $target_file = $target_dir . $file_name;

        if (move_uploaded_file($_FILES['cover']['tmp_name'], $target_file)) {
            $cover_name = $file_name;
        }
    }

    // Proses insert ke database
    $sql  = "INSERT INTO film (judul, sutradara, produser, cover_film) VALUES (?, ?, ?, ?)";
    $stmt = $mysqli->prepare($sql);
    $stmt->bind_param("ssss", $judul, $sutradara, $produser, $cover_name);

    if ($stmt->execute()) {
        $id_film = $stmt->insert_id;

        // Proses genre
        if (!empty($_POST['genre'])) {
            $stmt_genre = $mysqli->prepare("INSERT INTO film_genre (id_film, id_genre) VALUES (?, ?)");
            foreach ($_POST['genre'] as $id_genre) {
                $stmt_genre->bind_param("ii", $id_film, $id_genre);
                $stmt_genre->execute();
            }
            $stmt_genre->close();
        }

        $pesan = "Film berhasil ditambahkan.";
    } else {
        $pesan_error = "Gagal menambahkan film.";
    }

    $stmt->close();
}
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Film</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Tambah Film</li>
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
                    <input type="text" name="judul" class="form-control" id="judul" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Pilih Genre</label><br>
                    <?php
                    $sql_genre    = "SELECT * FROM genre ORDER BY nama_genre ASC";
                    $result_genre = $mysqli->query($sql_genre);
                    while ($genre = $result_genre->fetch_assoc()) :
                    ?>
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="genre[]" value="<?= $genre['id_genre'] ?>" id="genre<?= $genre['id_genre'] ?>">
                            <label class="form-check-label" for="genre<?= $genre['id_genre'] ?>"><?= htmlspecialchars($genre['nama_genre']) ?></label>
                        </div>
                    <?php endwhile; ?>
                </div>

                <div class="mb-3">
                    <label for="sutradara" class="form-label">Sutradara</label>
                    <input type="text" name="sutradara" class="form-control" id="sutradara" required>
                </div>

                <div class="mb-3">
                    <label for="produser" class="form-label">Produser</label>
                    <input type="text" name="produser" class="form-control" id="produser" required>
                </div>

                <div class="mb-3">
                    <label for="cover" class="form-label">Cover Film</label>
                    <input type="file" name="cover" class="form-control" id="cover">
                </div>

                <button type="submit" class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
</div>