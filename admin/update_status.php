<?php
require 'includes/db.php'; // Koneksi ke database

$id = $_POST['id'];
$status = $_POST['status'];
$comment = isset($_POST['comment']) ? $_POST['comment'] : '';

// Mengambil data dari tabel pendaftaran
$sql = "SELECT * FROM pendaftaran WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Memperbarui status dan komentar di tabel pendaftaran
$sql = "UPDATE pendaftaran SET status = ?, comment = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $status, $comment, $id);
$stmt->execute();

if ($status == 'acc') {
    // Memasukkan data ke tabel diterima
    $sql = "INSERT INTO diterima (id, nama, email, nim, transkrip_nilai, krs, instagram, reg_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isssssss", $row['id'], $row['nama'], $row['email'], $row['nim'], $row['transkrip_nilai'], $row['krs'], $row['instagram'], $row['reg_date']);
    $stmt->execute();
} else if ($status == 'rejected') {
    // Memasukkan data ke tabel tidak_diterima
    $sql = "INSERT INTO tidak_diterima (id, nama, email, nim, transkrip_nilai, krs, instagram, reg_date, comment) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssssss", $row['id'], $row['nama'], $row['email'], $row['nim'], $row['transkrip_nilai'], $row['krs'], $row['instagram'], $row['reg_date'], $comment);
    $stmt->execute();
}

$stmt->close();
$conn->close();

// Redirect kembali ke halaman index
header("Location: index.php");
exit();
?>