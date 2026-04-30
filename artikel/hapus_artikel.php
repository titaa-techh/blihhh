<?php
header('Content-Type: application/json');
require 'koneksi.php';

$id = (int)($_POST['id'] ?? 0);

if ($id <= 0) {
    echo json_encode(['status' => 'error', 'pesan' => 'ID tidak valid']);
    exit;
}

// Ambil nama gambar sebelum hapus
$cek = $koneksi->prepare("SELECT gambar FROM artikel WHERE id = ?");
$cek->bind_param('i', $id);
$cek->execute();
$row = $cek->get_result()->fetch_assoc();
$cek->close();

if (!$row) {
    echo json_encode(['status' => 'error', 'pesan' => 'Data tidak ditemukan']);
    exit;
}

$stmt = $koneksi->prepare("DELETE FROM artikel WHERE id = ?");
$stmt->bind_param('i', $id);

if ($stmt->execute()) {
    // Hapus file gambar dari server
    $path = __DIR__ . '/uploads_artikel/' . $row['gambar'];
    if (file_exists($path)) unlink($path);

    echo json_encode(['status' => 'sukses', 'pesan' => 'Artikel berhasil dihapus']);
} else {
    echo json_encode(['status' => 'error', 'pesan' => 'Gagal menghapus artikel']);
}

$stmt->close();
$koneksi->close();
