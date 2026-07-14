<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$nama    = $_SESSION['nama'];
$today   = date('Y-m-d');

// Ambil foto yang sama dengan foto pada halaman profil.
$stmt = mysqli_prepare($conn, 'SELECT * FROM user WHERE user_id = ? LIMIT 1');
mysqli_stmt_bind_param($stmt, 's', $user_id);
mysqli_stmt_execute($stmt);
$profil_user = mysqli_stmt_get_result($stmt)->fetch_assoc() ?: [];
mysqli_stmt_close($stmt);
$foto_dashboard = $profil_user['foto'] ?? '';

// Ambil data absensi hari ini
$stmt = mysqli_prepare($conn, "SELECT * FROM absensi WHERE user_id = ? AND tanggal = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "ss", $user_id, $today);
mysqli_stmt_execute($stmt);
$today_absen = mysqli_stmt_get_result($stmt)->fetch_assoc();
mysqli_stmt_close($stmt);

$sudah_masuk  = $today_absen && !empty($today_absen['jam_masuk']) && $today_absen['jam_masuk'] !== '00:00' && $today_absen['jam_masuk'] !== '00:00:00';
$sudah_pulang = $today_absen && !empty($today_absen['jam_pulang']) && $today_absen['jam_pulang'] !== '00:00' && $today_absen['jam_pulang'] !== '00:00:00';
$jam_masuk_display = $sudah_masuk ? date('H:i', strtotime(str_replace('.', ':', $today_absen['jam_masuk']))) : '-/-';

// Total kehadiran bulan ini (Hadir + Telat) dan target efektif kerja 22 hari
$bulan_ini = date('Y-m');
$q2 = mysqli_query($conn, "SELECT COUNT(*) c FROM absensi WHERE user_id = '".mysqli_real_escape_string($conn,$user_id)."' AND DATE_FORMAT(tanggal,'%Y-%m') = '$bulan_ini' AND status_kehadiran IN ('Hadir','Telat')");
$total_hadir = mysqli_fetch_assoc($q2)['c'] ?? 0;

$q3 = mysqli_query($conn, "SELECT COUNT(*) c FROM absensi WHERE user_id = '".mysqli_real_escape_string($conn,$user_id)."' AND DATE_FORMAT(tanggal,'%Y-%m') = '$bulan_ini' AND status_kehadiran IN ('Telat','Izin')");
$total_telat_izin = mysqli_fetch_assoc($q3)['c'] ?? 0;

$target_hari_kerja = 22;
$persen_progress = round(min(100, ($total_hadir / $target_hari_kerja) * 100));
$selesai_kehadiran = $total_hadir >= $target_hari_kerja;

$inisial = strtoupper(substr($nama, 0, 1));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - SIAP Kemenkumham</title>
    <link rel="stylesheet" href="style.css?v=5">
</head>
<body class="app-body">
    <div class="app-shell">
        <?php include 'sidebar.php'; ?>

        <div class="main-content">
            <div class="content-header">
                <h1>Dashboard</h1>
                <div class="search-box">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                    <input type="text" placeholder="Search">
                </div>
            </div>

            <div class="greeting-banner">
                <div>
                    <h2>Hai <?= htmlspecialchars($nama) ?>, Siap Untuk Hari ini?</h2>
                    <p>Selamat Datang! Ayo Mulai &amp; Akhiri Hari dengan Tepat.</p>
                    <div class="banner-actions">
                        <?php if (!$sudah_masuk): ?>
                            <a href="proses_absensi.php?action=masuk" class="btn-pill primary">
                                <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                Absen Masuk Sekarang
                            </a>
                        <?php else: ?>
                            <span class="btn-pill primary disabled">
                                <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6 9 17l-5-5"/></svg>
                                Sudah Absen Masuk
                            </span>
                        <?php endif; ?>

                        <?php if ($sudah_masuk && !$sudah_pulang): ?>
                            <a href="proses_absensi.php?action=pulang" class="btn-pill ghost">
                                <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
                                Lapor Absen Pulang
                            </a>
                        <?php else: ?>
                            <span class="btn-pill ghost disabled">
                                <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
                                Lapor Absen Pulang
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="banner-illustration">
                    <?php if ($foto_dashboard !== ''): ?>
                        <img src="<?= htmlspecialchars($foto_dashboard) ?>" alt="Foto Profil <?= htmlspecialchars($nama) ?>">
                    <?php else: ?>
                        <svg viewBox="0 0 24 24" fill="none"><circle cx="12" cy="8" r="4" fill="#fde68a"/><path d="M2 19c1.5-4 5-6 10-6s8.5 2 10 6" fill="#86efac"/></svg>
                    <?php endif; ?>
                </div>
            </div>

            <div class="stat-grid">
                <div class="stat-card">
                    <h4>Status Hari ini</h4>
                    <div class="big-num"><span id="current-time">--:--:--</span> (WIB)</div>
                    <div class="stat-btn-row">
                        <a href="<?= $sudah_masuk ? '#' : 'proses_absensi.php?action=masuk' ?>" class="stat-btn <?= $sudah_masuk ? 'off' : 'on' ?>">Absen Masuk</a>
                        <a href="<?= ($sudah_masuk && !$sudah_pulang) ? 'proses_absensi.php?action=pulang' : '#' ?>" class="stat-btn <?= ($sudah_masuk && !$sudah_pulang) ? 'on' : 'off' ?>">Absen Keluar</a>
                    </div>
                </div>

                <div class="stat-card<?= $selesai_kehadiran ? ' complete' : '' ?>">
                    <h4>Total kehadiran bulan ini</h4>
                    <div class="big-num<?= $selesai_kehadiran ? ' success' : '' ?>"><?= $total_hadir ?>/22</div>
                    <div class="progress-track"><div class="progress-fill<?= $selesai_kehadiran ? ' complete' : '' ?>" style="width:<?= $persen_progress ?>%"></div></div>
                </div>

                <div class="stat-card">
                    <h4>Catatan Kehadiran</h4>
                    <input type="text" class="note-input" placeholder="Tulis catatan..." readonly>
                    <div style="margin-top:14px;">
                        <h4 style="margin-bottom:6px;">Keterlambatan / Izin</h4>
                        <div class="big-num" style="margin-bottom:0;"><?= $total_telat_izin ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function updateCurrentTime() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('current-time').textContent = `${hours}:${minutes}:${seconds}`;
        }

        updateCurrentTime();
        setInterval(updateCurrentTime, 1000);
    </script>
</body>
</html>
