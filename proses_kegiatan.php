<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

// Handle deletion via GET ?delete_id=... (used by link from kegiatan.php)

// Handle deletion via GET ?delete_id=... (used by link from kegiatan.php)
if (isset($_GET['delete_id'])) {
    if (!isset($_SESSION['login'])) {
        header("Location: index.php"); exit;
    }
    $user_id = $_SESSION['user_id'];
    $del_id = (int)$_GET['delete_id'];
    if ($del_id <= 0) {
        // Try fallback deletion by matching fields if delete_id not provided
        $w = $_GET['w'] ?? null;
        $n = $_GET['n'] ?? null;
        $t = $_GET['t'] ?? null;
        if ($w !== null && $n !== null && $t !== null) {
            $del_stmt2 = mysqli_prepare($conn, 'DELETE FROM kegiatan WHERE user_id = ? AND tanggal = ? AND waktu_kegiatan = ? AND nama_kegiatan = ? LIMIT 1');
            if ($del_stmt2) {
                mysqli_stmt_bind_param($del_stmt2, 'ssss', $user_id, $t, $w, $n);
                mysqli_stmt_execute($del_stmt2);
                mysqli_stmt_close($del_stmt2);
                header('Location: kegiatan.php?tanggal=' . urlencode($t) . '&pesan=deleted');
                exit;
            } else {
                header("Location: kegiatan.php?pesan=error"); exit;
            }
        }
        header("Location: kegiatan.php?pesan=error"); exit;
    }
    $del_stmt = mysqli_prepare($conn, 'DELETE FROM kegiatan WHERE id = ? AND user_id = ?');
    if ($del_stmt) {
        mysqli_stmt_bind_param($del_stmt, 'is', $del_id, $user_id);
        mysqli_stmt_execute($del_stmt);
        mysqli_stmt_close($del_stmt);
        // preserve tanggal in redirect if provided
        $redir = 'kegiatan.php?pesan=deleted';
        if (!empty($_GET['tanggal'])) $redir = 'kegiatan.php?tanggal=' . urlencode($_GET['tanggal']) . '&pesan=deleted';
        header("Location: $redir");
        exit;
    } else {
        // If delete by id failed or id is 0, try delete by matching fields (waktu_kegiatan + nama_kegiatan + tanggal)
        $w = $_GET['w'] ?? null;
        $n = $_GET['n'] ?? null;
        $t = $_GET['t'] ?? null;
        if ($w !== null && $n !== null && $t !== null) {
            $del_stmt2 = mysqli_prepare($conn, 'DELETE FROM kegiatan WHERE user_id = ? AND tanggal = ? AND waktu_kegiatan = ? AND nama_kegiatan = ? LIMIT 1');
            if ($del_stmt2) {
                mysqli_stmt_bind_param($del_stmt2, 'ssss', $user_id, $t, $w, $n);
                mysqli_stmt_execute($del_stmt2);
                mysqli_stmt_close($del_stmt2);
                header('Location: kegiatan.php?tanggal=' . urlencode($t) . '&pesan=deleted');
                exit;
            } else {
                echo "Gagal menghapus (fallback): " . htmlspecialchars(mysqli_error($conn)); exit;
            }
        }
        echo "Gagal menghapus: " . htmlspecialchars(mysqli_error($conn)); exit;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle deletion requests first
    if (isset($_POST['action']) && $_POST['action'] === 'delete') {
        $user_id = $_SESSION['user_id'];
        $del_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($del_id <= 0) {
            header("Location: kegiatan.php?pesan=error");
            exit;
        }
        $del_stmt = mysqli_prepare($conn, 'DELETE FROM kegiatan WHERE id = ? AND user_id = ?');
        if ($del_stmt) {
            mysqli_stmt_bind_param($del_stmt, 'is', $del_id, $user_id);
            mysqli_stmt_execute($del_stmt);
            mysqli_stmt_close($del_stmt);
            header("Location: kegiatan.php?pesan=deleted");
            exit;
        } else {
            echo "Gagal menghapus: " . htmlspecialchars(mysqli_error($conn));
            exit;
        }
    }

    $user_id = $_SESSION['user_id'];
    $tanggal = $_POST['tanggal'] !== '' ? $_POST['tanggal'] : date('Y-m-d');

    // Support both new separate hour/minute selects and legacy single time inputs
    if (!empty($_POST['waktu_mulai_hour']) && !empty($_POST['waktu_mulai_minute']) && !empty($_POST['waktu_selesai_hour']) && !empty($_POST['waktu_selesai_minute'])) {
        $wm_h = preg_replace('/[^0-9]/','', $_POST['waktu_mulai_hour']);
        $wm_m = preg_replace('/[^0-9]/','', $_POST['waktu_mulai_minute']);
        $ws_h = preg_replace('/[^0-9]/','', $_POST['waktu_selesai_hour']);
        $ws_m = preg_replace('/[^0-9]/','', $_POST['waktu_selesai_minute']);
        $waktu_mulai = str_pad($wm_h,2,'0',STR_PAD_LEFT) . ':' . str_pad($wm_m,2,'0',STR_PAD_LEFT);
        $waktu_selesai = str_pad($ws_h,2,'0',STR_PAD_LEFT) . ':' . str_pad($ws_m,2,'0',STR_PAD_LEFT);
    } else {
        $waktu_mulai   = $_POST['waktu_mulai'] ?? '';
        $waktu_selesai = $_POST['waktu_selesai'] ?? '';
    }
    $waktu = str_replace(':', '.', $waktu_mulai) . ' - ' . str_replace(':', '.', $waktu_selesai);

    $nama_kegiatan = trim($_POST['nama_kegiatan']);
    $departemen    = trim($_POST['departemen']);
    $status        = ($_POST['status_kegiatan'] === 'Selesai') ? 'Selesai' : 'On Progress';

    // Pastikan kolom 'waktu_kegiatan' ada pada tabel (jika tidak, tambahkan)
    $col_check = mysqli_query($conn, "SHOW COLUMNS FROM kegiatan LIKE 'waktu_kegiatan'");
    if ($col_check === false) {
        echo "Error checking table columns: " . htmlspecialchars(mysqli_error($conn));
        exit;
    }
    if (mysqli_num_rows($col_check) == 0) {
        $alter_sql = "ALTER TABLE kegiatan ADD COLUMN waktu_kegiatan VARCHAR(30) NOT NULL DEFAULT '' AFTER tanggal";
        if (!mysqli_query($conn, $alter_sql)) {
            echo "Gagal menambahkan kolom 'waktu_kegiatan': " . htmlspecialchars(mysqli_error($conn));
            exit;
        }
    }

    $sql = "INSERT INTO kegiatan (user_id, tanggal, waktu_kegiatan, nama_kegiatan, departemen, status_kegiatan) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    if ($stmt === false) {
        // Prepare gagal — tunjukkan error dan coba jalankan query langsung sebagai fallback
        $db_err = mysqli_error($conn);
        // Fallback: escape values and run plain query
        $u = mysqli_real_escape_string($conn, $user_id);
        $t = mysqli_real_escape_string($conn, $tanggal);
        $w = mysqli_real_escape_string($conn, $waktu);
        $n = mysqli_real_escape_string($conn, $nama_kegiatan);
        $d = mysqli_real_escape_string($conn, $departemen);
        $s = mysqli_real_escape_string($conn, $status);
        $fallback_sql = "INSERT INTO kegiatan (user_id, tanggal, waktu_kegiatan, nama_kegiatan, departemen, status_kegiatan) VALUES ('$u','$t','$w','$n','$d','$s')";
        if (mysqli_query($conn, $fallback_sql)) {
            header("Location: kegiatan.php?tanggal=".urlencode($tanggal)."&pesan=sukses");
            exit;
        } else {
            echo "DB prepare error: " . htmlspecialchars($db_err) . " — fallback failed: " . htmlspecialchars(mysqli_error($conn));
            exit;
        }
    }

    mysqli_stmt_bind_param($stmt, "ssssss", $user_id, $tanggal, $waktu, $nama_kegiatan, $departemen, $status);
    if (mysqli_stmt_execute($stmt)) {
        mysqli_stmt_close($stmt);
        header("Location: kegiatan.php?tanggal=".urlencode($tanggal)."&pesan=sukses");
        exit;
    } else {
        echo "Execute error: " . htmlspecialchars(mysqli_error($conn));
        exit;
    }
    exit;
}

header("Location: kegiatan.php");
exit;
