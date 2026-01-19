<?php
session_start();
require_once '../includes/config.php';

// buat fungsi untuk respon json
function response($status, $msg, $data = null) {
    // ini format response yang akan kita kirim ke client (android app)
    echo json_encode([
        'status' => $status,
        'message' => $msg,
        'data' => $data
    ]);
    exit; // ini untuk menghentikan semua proses
}

// pastikan bahwa method yang digunakan adalah GET
if ($_SERVER["REQUEST_METHOD"] !== "GET") {
    response("error", "Gunakan methode GET.!!");
    // ini akan berhenti disini, karena exit sudah ada di dalam function response yang kita buat sebelumnya.
}

// query. yang diambil hanya id buku, judul, penulis, dan cover_buku. Pastikan nama column dan column ada di database kalian di dalam table buku
$result = $mysqli->query("SELECT id_buku, judul, penulis, cover_buku FROM buku ORDER BY id_buku DESC");
// order by dari yang id nya terbesar DESC

// inisialisasi variable $daftar
$daftar = [];

while ($row = $result->fetch_assoc()) {
    $daftar[] = [
        "id_buku" => $row['id_buku'],
        "judul" => $row['judul'],
        "penulis" => $row['penulis'],
        "cover" => "http://10.15.202.119/perpustakaan-uts/uploads/buku/" . $row['cover_buku']
    ];
}

response("success", "Daftar buku diambil", $daftar);
// Sekarang kita bisa mencoba rest api yang kita buat.
?>
