<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $nim = $_POST['nim'];
    
    $transkrip_nilai = $_FILES['transkrip_nilai']['name'];
    $krs = $_FILES['krs']['name'];
    $instagram = $_FILES['instagram']['name'];

    $transkrip_nilai_size = $_FILES['transkrip_nilai']['size'];
    $krs_size = $_FILES['krs']['size'];
    $instagram_size = $_FILES['instagram']['size'];

    // Maximum file size in bytes (1MB)
    $max_file_size = 1 * 1024 * 1024;

    if ($transkrip_nilai_size > $max_file_size || $krs_size > $max_file_size || $instagram_size > $max_file_size) {
        echo "Error: One or more files exceed the maximum size of 1MB.";
        exit();
    }

    $target_dir_transkrip = "../uploads/transkrip_nilai/";
    $target_dir_krs = "../uploads/krs/";
    $target_dir_instagram = "../uploads/instagram/";

    if (!file_exists($target_dir_transkrip)) {
        mkdir($target_dir_transkrip, 0777, true);
    }
    if (!file_exists($target_dir_krs)) {
        mkdir($target_dir_krs, 0777, true);
    }
    if (!file_exists($target_dir_instagram)) {
        mkdir($target_dir_instagram, 0777, true);
    }

    move_uploaded_file($_FILES['transkrip_nilai']['tmp_name'], $target_dir_transkrip . $transkrip_nilai);
    move_uploaded_file($_FILES['krs']['tmp_name'], $target_dir_krs . $krs);
    move_uploaded_file($_FILES['instagram']['tmp_name'], $target_dir_instagram . $instagram);

    $sql = "INSERT INTO pendaftaran (nama, email, nim, transkrip_nilai, krs, instagram) VALUES ('$nama', '$email', '$nim', '$transkrip_nilai', '$krs', '$instagram')";

    if ($conn->query($sql) === TRUE) {
        header("Location: ../../index.html");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
    
    $conn->close();
}
?>