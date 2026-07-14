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

$tanggal_filter = trim($_GET['tanggal'] ?? date('Y-m-d'));
$search = trim($_GET['search'] ?? '');

$esc_tgl = mysqli_real_escape_string($conn, $tanggal_filter);
$sql = "SELECT u.user_id, u.nama, u.departemen,
               a.jam_masuk, a.jam_pulang, a.status_kehadiran
        FROM user u
        LEFT JOIN absensi a ON a.user_id = u.user_id AND a.tanggal = '$esc_tgl'
        WHERE u.role = 'karyawan'";
if ($search !== '') {
    $esc = mysqli_real_escape_string($conn, $search);
    $sql .= " AND (u.nama LIKE '%$esc%' OR u.departemen LIKE '%$esc%' OR u.user_id LIKE '%$esc%')";
}
$sql .= " ORDER BY u.nama ASC";
$data = mysqli_query($conn, $sql);

$total_pegawai = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM user WHERE role = 'karyawan'"))['c'] ?? 0;
$stat_res = mysqli_query($conn, "SELECT status_kehadiran, COUNT(*) c FROM absensi WHERE tanggal = '$esc_tgl' GROUP BY status_kehadiran");
$stat = ['Hadir'=>0,'Telat'=>0,'Izin'=>0,'Absen'=>0];
while ($s = mysqli_fetch_assoc($stat_res)) {
    if (isset($stat[$s['status_kehadiran']])) $stat[$s['status_kehadiran']] = (int)$s['c'];
}
$sudah_absen = $stat['Hadir'] + $stat['Telat'];
$belum_absen = max(0, $total_pegawai - ($stat['Hadir'] + $stat['Telat'] + $stat['Izin'] + $stat['Absen']));

function badge_class_admin($status) {
    switch ($status) {
        case 'Hadir': return 'badge-green';
        case 'Telat': return 'badge-red';
        case 'Izin': return 'badge-orange';
        case 'Absen': return 'badge-gray';
        default: return 'badge-gray';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Absensi - Admin SIAP</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="app-body">
    <div class="app-shell">
        <?php include 'admin_sidebar.php'; ?>

        <div class="main-content">
            <div class="content-header">
                <h1>Rekap Absensi Pegawai</h1>
                <div class="user-chip">
                    <span><?= htmlspecialchars($_SESSION['nama']) ?></span>
                    <div class="avatar-circle"><?= strtoupper(substr($_SESSION['nama'],0,1)) ?></div>
                </div>
            </div>

            <div class="stat-grid" style="grid-template-columns:repeat(5,1fr); margin-bottom:20px;">
                <div class="stat-card">
                    <h4>Total Pegawai</h4>
                    <div class="big-num" style="margin-bottom:0;"><?= $total_pegawai ?></div>
                </div>
                <div class="stat-card">
                    <h4>Sudah Absen</h4>
                    <div class="big-num" style="margin-bottom:0; color:#16a34a;"><?= $sudah_absen ?></div>
                </div>
                <div class="stat-card">
                    <h4>Telat</h4>
                    <div class="big-num" style="margin-bottom:0; color:#dc2626;"><?= $stat['Telat'] ?></div>
                </div>
                <div class="stat-card">
                    <h4>Izin</h4>
                    <div class="big-num" style="margin-bottom:0; color:#d97706;"><?= $stat['Izin'] ?></div>
                </div>
                <div class="stat-card">
                    <h4>Belum Absen</h4>
                    <div class="big-num" style="margin-bottom:0; color:#6b7280;"><?= $belum_absen + $stat['Absen'] ?></div>
                </div>
            </div>

            <div class="panel-card">
                <form method="GET" class="table-toolbar">
                    <div class="search-box" style="min-width:200px;">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                        <input type="text" name="search" placeholder="Cari nama / departemen / ID" value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <label class="date-pill" style="cursor:pointer;">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M3 9h18M8 2v4M16 2v4"/></svg>
                        <input type="date" name="tanggal" value="<?= htmlspecialchars($tanggal_filter) ?>" style="border:none;background:transparent;font-family:inherit;font-size:13px;font-weight:600;color:var(--text-dark);outline:none;">
                    </label>
                    <button type="submit" class="btn-pill primary" style="padding:9px 16px;">Filter</button>
                </form>

                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>ID Pegawai</th>
                            <th>Nama</th>
                            <th>Departemen</th>
                            <th>Jam Masuk</th>
                            <th>Jam Pulang</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (mysqli_num_rows($data) == 0): ?>
                        <tr><td colspan="6" style="text-align:center;color:var(--text-muted);padding:26px;">Tidak ada data pegawai.</td></tr>
                    <?php else: while ($r = mysqli_fetch_assoc($data)):
                        $status = $r['status_kehadiran'] ?? 'Belum Absen';
                        $masuk  = $r['jam_masuk'] ? str_replace(':', '.', substr($r['jam_masuk'],0,5)) : '-/-';
                        $pulang = $r['jam_pulang'] ? str_replace(':', '.', substr($r['jam_pulang'],0,5)) : '-/-';
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($r['user_id']) ?></td>
                            <td><?= htmlspecialchars($r['nama']) ?></td>
                            <td><?= htmlspecialchars($r['departemen'] ?? '-') ?></td>
                            <td><?= $masuk ?></td>
                            <td><?= $pulang ?></td>
                            <td><span class="badge <?= badge_class_admin($status) ?>"><?= htmlspecialchars($status) ?></span></td>
                        </tr>
                    <?php endwhile; endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>