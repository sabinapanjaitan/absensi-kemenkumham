<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$action  = $_GET['action'] ?? '';
$today   = date('Y-m-d');
$jam_now = date('H:i');
$batas_telat = '09:00';

// Cek apakah sudah ada record hari ini
$stmt = mysqli_prepare($conn, "SELECT * FROM absensi WHERE user_id = ? AND tanggal = ? LIMIT 1");
mysqli_stmt_bind_param($stmt, "ss", $user_id, $today);
mysqli_stmt_execute($stmt);
$row = mysqli_stmt_get_result($stmt)->fetch_assoc();
mysqli_stmt_close($stmt);

if ($action === 'masuk') {
    $status = ($jam_now > $batas_telat) ? 'Telat' : 'Hadir';

    if ($row) {
        $stmt = mysqli_prepare($conn, "UPDATE absensi SET jam_masuk = ?, status_kehadiran = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "ssi", $jam_now, $status, $row['id']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else {
        $stmt = mysqli_prepare($conn, "INSERT INTO absensi (user_id, tanggal, jam_masuk, status_kehadiran) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $user_id, $today, $jam_now, $status);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
} elseif ($action === 'pulang') {
    if ($row && !empty($row['jam_masuk']) && empty($row['jam_pulang'])) {
        $stmt = mysqli_prepare($conn, "UPDATE absensi SET jam_pulang = ? WHERE user_id = ? AND tanggal = ?");
        if (!$stmt) {
            error_log('Absensi pulang prepare failed: ' . mysqli_error($conn));
            header("Location: dashboard.php?error=absen_pulang");
            exit;
        }
        mysqli_stmt_bind_param($stmt, "sss", $jam_now, $user_id, $today);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
} elseif ($action === 'catatan') {
    $keterangan = trim($_POST['keterangan'] ?? '');
    $allowed = ['Sakit', 'Izin Urusan Keluarga', 'Dinas Luar', 'Lainnya'];
    if (!in_array($keterangan, $allowed, true)) {
        header("Location: dashboard.php?error=invalid_catatan");
        exit;
    }

    if ($row) {
        $stmt = mysqli_prepare($conn, "UPDATE absensi SET keterangan = ? WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "si", $keterangan, $row['id']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    } else {
        $status = 'Izin';
        $stmt = mysqli_prepare($conn, "INSERT INTO absensi (user_id, tanggal, status_kehadiran, keterangan) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "ssss", $user_id, $today, $status, $keterangan);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
}

header("Location: dashboard.php");
exit;
