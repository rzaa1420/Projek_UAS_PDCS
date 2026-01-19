<?php
//proteksi agar file tidak dapat diakses langsung
if(!defined('MY_APP')) {
    die('Akses langsung tidak diperbolehkan!');
}

$sql = "SELECT * FROM genre ORDER BY id_genre DESC";

$result = $mysqli->query($sql);
if(!$result) {
    die("Query Error" . $mysqli->error);
}
?>

<div class="container-fluid px-4">
    <h1 class="mt-4">Genre</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item active">Daftar Genre</li>
    </ol>

    <div class="card mb-4">
        <div class="card-body">
        <a href="index.php?hal=tambah-genre" class="btn btn-success mb-3">Tambah Genre</a>
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>No</th>
                        <th>Nama Genre</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1 ?>
                    <?php while($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo $no ?></td>
                        <td><?php echo $row['nama_genre'] ?></td>
                        <td>
                            <a href="index.php?hal=ubah-genre&id=<?php echo $row['id_genre'] ?>" class="btn btn-warning btn-sm">Ubah</a>
                        </td>
                    </tr>
                        <?php $no++ ?>
                    <?php endwhile ?>
                </tbody>
            </table>
        </div>
    </div>
</div>