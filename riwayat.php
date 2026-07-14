<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$nama    = $_SESSION['nama'];
$inisial = strtoupper(substr($nama, 0, 1));

// Ambil foto profil pengguna (sama dengan dashboard/profile)
$stmt = mysqli_prepare($conn, 'SELECT foto FROM user WHERE user_id = ? LIMIT 1');
mysqli_stmt_bind_param($stmt, 's', $user_id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);
$foto = $row['foto'] ?? '';
$hari_id = ['Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];

// --- Filter ---
$search  = trim($_GET['search'] ?? '');
$tanggal_filter = trim($_GET['tanggal'] ?? '');
$page    = max(1, (int)($_GET['page'] ?? 1));
$per_page = 4;
$offset  = ($page - 1) * $per_page;

$where = "WHERE user_id = '".mysqli_real_escape_string($conn, $user_id)."'";
if ($tanggal_filter !== '') {
    $where .= " AND tanggal = '".mysqli_real_escape_string($conn, $tanggal_filter)."'";
}
if ($search !== '') {
    $esc = mysqli_real_escape_string($conn, $search);
    $where .= " AND (status_kehadiran LIKE '%$esc%' OR tanggal LIKE '%$esc%')";
}

$count_res = mysqli_query($conn, "SELECT COUNT(*) c FROM absensi $where");
$total_rows = mysqli_fetch_assoc($count_res)['c'] ?? 0;
$total_pages = max(1, ceil($total_rows / $per_page));

$data = mysqli_query($conn, "SELECT * FROM absensi $where ORDER BY tanggal DESC LIMIT $per_page OFFSET $offset");

function jam_kerja($masuk, $pulang) {
    if (empty($masuk) || empty($pulang)) return '0 Jam';
    $m = strtotime(str_replace('.', ':', $masuk));
    $p = strtotime(str_replace('.', ':', $pulang));
    if ($p <= $m) return '0 Jam';
    $jam = round(($p - $m) / 3600);
    return $jam . ' Jam';
}
function badge_class($status) {
    switch ($status) {
        case 'Hadir': return 'badge-green';
        case 'Telat': return 'badge-red';
        case 'Izin': return 'badge-orange';
        default: return 'badge-gray';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Absensi - SIAP Kemenkumham</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="app-body">
    <div class="app-shell">
        <?php include 'sidebar.php'; ?>

        <div class="main-content">
            <div class="content-header">
                <h1>Riwayat Absensi Karyawan</h1>
                <div class="user-chip">
                    <span><?= htmlspecialchars($nama) ?></span>
                    <?php if ($foto !== '' && file_exists(__DIR__ . '/' . $foto)): ?>
                        <img src="<?= htmlspecialchars($foto) ?>" alt="Foto Profil <?= htmlspecialchars($nama) ?>" class="avatar-circle">
                    <?php else: ?>
                        <div class="avatar-circle"><?= $inisial ?></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="panel-card">
                <form method="GET" class="table-toolbar">
                    <div class="search-box" style="min-width:180px;">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                        <input type="text" name="search" placeholder="Search" value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <label class="date-pill" style="cursor:pointer;">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M3 9h18M8 2v4M16 2v4"/></svg>
                        <input type="date" name="tanggal" value="<?= htmlspecialchars($tanggal_filter) ?>" style="border:none;background:transparent;font-family:inherit;font-size:13px;font-weight:600;color:var(--text-dark);outline:none;">
                    </label>
                    <button type="submit" class="btn-pill primary" style="padding:9px 16px;">Filter</button>
                    <?php if ($search !== '' || $tanggal_filter !== ''): ?>
                        <a href="riwayat.php" class="btn-pill ghost" style="background:var(--gray-bg);color:var(--text-dark);">Reset</a>
                    <?php endif; ?>
                </form>

                <table class="styled-table">
                    <thead>
                        <tr>
                            <th>Hari &amp; Tanggal</th>
                            <th>Waktu Masuk</th>
                            <th>Waktu Pulang</th>
                            <th>Total Jam Kerja</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($total_rows == 0): ?>
                        <tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:26px;">Belum ada data absensi.</td></tr>
                    <?php else: while ($r = mysqli_fetch_assoc($data)):
                        $ts = strtotime($r['tanggal']);
                        $hari = $hari_id[date('l', $ts)];
                        $tgl_display = $hari . ', ' . date('d M Y', $ts);
                        $masuk = $r['jam_masuk'] ? str_replace(':', '.', substr($r['jam_masuk'],0,5)) : '-/-';
                        $pulang = $r['jam_pulang'] ? str_replace(':', '.', substr($r['jam_pulang'],0,5)) : '-/-';
                    ?>
                        <tr>
                            <td><?= $tgl_display ?></td>
                            <td><?= $masuk ?></td>
                            <td><?= $pulang ?></td>
                            <td><?= jam_kerja($r['jam_masuk'], $r['jam_pulang']) ?></td>
                            <td><span class="badge <?= badge_class($r['status_kehadiran']) ?>"><?= $r['status_kehadiran'] ?></span></td>
                        </tr>
                    <?php endwhile; endif; ?>
                    </tbody>
                </table>

                <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&tanggal=<?= urlencode($tanggal_filter) ?>" class="<?= $i == $page ? 'current' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>
