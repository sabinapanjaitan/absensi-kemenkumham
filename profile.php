<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = mysqli_prepare($conn, "SELECT * FROM user WHERE user_id = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $user_id);
mysqli_stmt_execute($stmt);
$u = mysqli_stmt_get_result($stmt)->fetch_assoc();
mysqli_stmt_close($stmt);

$u = $u ?: [];
$inisial = strtoupper(substr($u['nama'] ?? 'U', 0, 1));
$tanggal_lahir = $u['tanggal_lahir'] ?? '';
$ttl = trim($u['tempat_lahir'] ?? '');
if ($tanggal_lahir !== '') {
    $ttl .= ($ttl !== '' ? ', ' : '') . date('d M Y', strtotime($tanggal_lahir));
}
$ttl = $ttl !== '' ? $ttl : '-';

// ID tampilan (ID: NP-202401) memakai user_id apa adanya
$id_display = $u['user_id'] ?? '-';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - SIAP Kemenkumham</title>
    <link rel="stylesheet" href="style.css?v=5">
</head>
<body class="app-body">
    <div class="app-shell">
        <?php include 'sidebar.php'; ?>

        <div class="main-content">
            <div class="content-header">
                <h1>Profil Saya</h1>
                <div class="id-badge">ID: <?= htmlspecialchars($id_display) ?></div>
            </div>

            <?php if (isset($_GET['pesan']) && $_GET['pesan'] === 'sukses'): ?>
                <div class="alert-box alert-success">Data berhasil diperbarui.</div>
            <?php elseif (isset($_GET['pesan']) && $_GET['pesan'] === 'pw_sukses'): ?>
                <div class="alert-box alert-success">Password berhasil diubah.</div>
            <?php elseif (isset($_GET['pesan']) && $_GET['pesan'] === 'pw_gagal'): ?>
                <div class="alert-box alert-error">Password lama tidak sesuai.</div>
            <?php endif; ?>

            <div class="profile-layout">
                <div class="profile-photo-card">
                    <?php if (!empty($u['foto'])): ?>
                        <img src="<?= htmlspecialchars($u['foto']) ?>" alt="Foto Profil">
                    <?php else: ?>
                        <div class="avatar-placeholder"><?= $inisial ?></div>
                    <?php endif; ?>
                    <h3><?= htmlspecialchars($u['nama'] ?? '-') ?></h3>
                    <p><?= htmlspecialchars($u['jabatan'] ?? $u['departemen'] ?? '-') ?></p>
                </div>

                <div class="profile-details">
                    <div class="info-card">
                        <h4>Data Detail Karyawan</h4>
                        <div class="info-row"><span class="label">NIP</span><span class="value"><?= htmlspecialchars($u['nip'] ?? '-') ?></span></div>
                        <div class="info-row"><span class="label">Jenis Kelamin</span><span class="value c-green"><?= htmlspecialchars($u['jenis_kelamin'] ?? '-') ?></span></div>
                        <div class="info-row"><span class="label">Tempat, Tanggal Lahir</span><span class="value c-blue"><?= htmlspecialchars($ttl) ?></span></div>
                        <div class="info-row"><span class="label">NIK</span><span class="value"><?= htmlspecialchars($u['nik'] ?? '-') ?></span></div>
                        <div class="info-row"><span class="label">Departemen</span><span class="value"><?= htmlspecialchars($u['departemen'] ?? '-') ?></span></div>
                        <div class="info-row"><span class="label">Status Karyawan</span><span class="value"><?= htmlspecialchars($u['status_karyawan'] ?? '-') ?></span></div>
                    </div>

                    <div class="info-card">
                        <h4>Informasi Kontak</h4>
                        <div class="info-row"><span class="label">Email</span><span class="value"><?= htmlspecialchars($u['email'] ?? '-') ?></span></div>
                        <div class="info-row"><span class="label">Nomor Telepon</span><span class="value"><?= htmlspecialchars($u['telepon'] ?? '-') ?></span></div>
                        <div class="info-row"><span class="label">Alamat</span><span class="value"><?= htmlspecialchars($u['alamat'] ?? '-') ?></span></div>
                    </div>
                </div>

                <div class="profile-actions">
                    <a href="edit_profile.php" class="btn-solid">Ubah Data</a>
                    <a href="ganti_password.php" class="btn-outline">Ganti Password</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
