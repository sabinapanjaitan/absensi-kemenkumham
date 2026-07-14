<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}
if (($_SESSION['role'] ?? 'karyawan') !== 'admin') {
    header("Location: dashboard.php");
    exit;
}

$search = trim($_GET['search'] ?? '');
$sql = "SELECT * FROM user WHERE role = 'karyawan'";
if ($search !== '') {
    $esc = mysqli_real_escape_string($conn, $search);
    $sql .= " AND (nama LIKE '%$esc%' OR departemen LIKE '%$esc%' OR user_id LIKE '%$esc%')";
}
$sql .= " ORDER BY nama ASC";
$data = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pegawai - Admin SIAP</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="app-body">
    <div class="app-shell">
        <?php include 'admin_sidebar.php'; ?>

        <div class="main-content">
            <div class="content-header">
                <h1>Data Pegawai</h1>
                <div class="search-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                    <form method="GET" style="width:100%;">
                        <input type="text" name="search" placeholder="Cari pegawai..." value="<?= htmlspecialchars($search) ?>" onkeyup="if(event.key==='Enter')this.form.submit()">
                    </form>
                </div>
            </div>

            <div class="panel-card">
                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>ID Pegawai</th>
                            <th>Nama</th>
                            <th>Departemen</th>
                            <th>Jabatan</th>
                            <th>Email</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (mysqli_num_rows($data) == 0): ?>
                        <tr><td colspan="6" style="text-align:center;color:var(--text-muted);padding:26px;">Belum ada pegawai terdaftar.</td></tr>
                    <?php else: while ($u = mysqli_fetch_assoc($data)): ?>
                        <tr>
                            <td><?= htmlspecialchars($u['user_id']) ?></td>
                            <td><?= htmlspecialchars($u['nama']) ?></td>
                            <td><?= htmlspecialchars($u['departemen'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($u['jabatan'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($u['email'] ?? '-') ?></td>
                            <td><span class="badge badge-green"><?= htmlspecialchars($u['status_karyawan'] ?? 'Tetap') ?></span></td>
                        </tr>
                    <?php endwhile; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>