<?php
include 'includes/db.php';
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Pendaftaran Praktikum</title>
    <link rel="shortcut icon" href="../img/logo.jpg" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
  </head>
  <body>
    <!-- Navbar -->
  <nav class="navbar bg-primary navbar-dark">
  <div class="container justify-content-center">
    <a class="navbar-brand mb-0 h1" href="#">
      <img src="../img/logo.jpg" alt="Logo" width="30" height="30" class="d-inline-block align-text-top rounded-circle">
      Praktikum Perpindahan Kalor dan Massa
    </a>
  </div>
</nav>
<!-- End navbar -->

<!-- Header -->
 <h3 class="text-center mt-3">Admin Panel</h3>
<!-- End Header -->

<!-- Filter -->
<div class="container mt-3">
  <form method="get" action="">
    <div class="row">
      <div class="col-md-4">
        <select name="filter" class="form-select" onchange="this.form.submit()">
          <option value="all" <?php if (!isset($_GET['filter']) || $_GET['filter'] == 'all') echo 'selected'; ?>>Semua</option>
          <option value="acc" <?php if (isset($_GET['filter']) && $_GET['filter'] == 'acc') echo 'selected'; ?>>Diterima</option>
          <option value="rejected" <?php if (isset($_GET['filter']) && $_GET['filter'] == 'rejected') echo 'selected'; ?>>Ditolak</option>
          <option value="none" <?php if (isset($_GET['filter']) && $_GET['filter'] == 'none') echo 'selected'; ?>>Belum Diberi Status</option>
        </select>
      </div>
    </div>
  </form>
</div>
<!-- End Filter -->

<!-- Button download -->
<div class="container mt-3 text-center">
    <a href="download_csv.php" class="btn btn-success">Download Spreadsheet</a>
  </div>
<!-- End Button download -->

<!-- Tabel -->
<div class="container mt-3">
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>ID</th>
        <th>Nama</th>
        <th>Email</th>
        <th>NIM</th>
        <th>Transkrip Nilai</th>
        <th>KRS</th>
        <th>Instagram</th>
        <th>Reg Date</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      <?php
      $filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
      $sql = "SELECT * FROM pendaftaran";
      if ($filter == 'acc') {
        $sql .= " WHERE status = 'acc'";
      } elseif ($filter == 'rejected') {
        $sql .= " WHERE status = 'rejected'";
      } elseif ($filter == 'none') {
        $sql .= " WHERE status IS NULL";
      }
      $result = $conn->query($sql);

      if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
          echo "<tr>";
          echo "<td>" . $row["id"] . "</td>";
          echo "<td>" . $row["nama"] . "</td>";
          echo "<td>" . $row["email"] . "</td>";
          echo "<td>" . $row["nim"] . "</td>";
          echo "<td><img src='uploads/transkrip_nilai/" . $row["transkrip_nilai"] . "' alt='Transkrip Nilai' width='100' class='img-thumbnail' data-bs-toggle='modal' data-bs-target='#imageModal' data-bs-src='uploads/transkrip_nilai/" . $row["transkrip_nilai"] . "'></td>";
          echo "<td><img src='uploads/krs/" . $row["krs"] . "' alt='KRS' width='100' class='img-thumbnail' data-bs-toggle='modal' data-bs-target='#imageModal' data-bs-src='uploads/krs/" . $row["krs"] . "'></td>";
          echo "<td><img src='uploads/instagram/" . $row["instagram"] . "' alt='Instagram' width='100' class='img-thumbnail' data-bs-toggle='modal' data-bs-target='#imageModal' data-bs-src='uploads/instagram/" . $row["instagram"] . "'></td>";
          echo "<td>" . $row["reg_date"] . "</td>";
          echo "<td>";
          if ($row["status"] == 'acc') {
              echo "<span class='badge bg-success'>Diterima</span>";
          } elseif ($row["status"] == 'rejected') {
              echo "<span class='badge bg-danger'>Ditolak</span>";
              echo "<br><small>" . $row["comment"] . "</small>";
          } else {
              echo "<form method='post' action='update_status.php' style='display:inline;'>";
              echo "<input type='hidden' name='id' value='" . $row["id"] . "'>";
              echo "<button type='submit' name='status' value='acc' class='btn btn-success btn-sm'><i class='fas fa-check'></i></button>";
              echo "<button type='button' class='btn btn-danger btn-sm' onclick='showRejectModal(" . $row["id"] . ")'><i class='fas fa-times'></i></button>";
              echo "</form>";
          }
          echo "</td>";
          echo "</tr>";
        }
      } else {
        echo "<tr><td colspan='9' class='text-center'>No records found</td></tr>";
      }
      $conn->close();
      ?>
    </tbody>
  </table>
</div>
<!-- End Tabel -->

<!-- Modal for Image Preview -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="imageModalLabel">Image Preview</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body text-center">
        <img src="" id="modalImage" class="img-fluid">
      </div>
    </div>
  </div>
</div>
<!-- End Modal for Image Preview -->

<!-- Modal for Reject Comment -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="rejectModalLabel">Add Comment</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="rejectForm" method="post" action="update_status.php">
          <input type="hidden" name="id" id="rejectId">
          <div class="mb-3">
            <textarea name="comment" class="form-control" placeholder="Add comment" required></textarea>
          </div>
          <button type="submit" name="status" value="rejected" class="btn btn-danger">Submit</button>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- End Modal for Reject Comment -->

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <script>
    var imageModal = document.getElementById('imageModal');
    imageModal.addEventListener('show.bs.modal', function (event) {
      var button = event.relatedTarget;
      var imgSrc = button.getAttribute('data-bs-src');
      var modalImage = document.getElementById('modalImage');
      modalImage.src = imgSrc;
    });

    function showRejectModal(id) {
      var rejectModal = new bootstrap.Modal(document.getElementById('rejectModal'));
      document.getElementById('rejectId').value = id;
      rejectModal.show();
    }
  </script>
  </body>
</html>