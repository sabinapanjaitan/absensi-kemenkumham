<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login'])) {
    header('Location: index.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = mysqli_prepare($conn, 'SELECT * FROM user WHERE user_id = ? LIMIT 1');
mysqli_stmt_bind_param($stmt, 's', $user_id);
mysqli_stmt_execute($stmt);
$u = mysqli_stmt_get_result($stmt)->fetch_assoc();
mysqli_stmt_close($stmt);

if (!$u) {
    http_response_code(404);
    exit('Data pengguna tidak ditemukan.');
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $telepon = trim($_POST['telepon'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $nip = trim($_POST['nip'] ?? '');
    $nik = trim($_POST['nik'] ?? '');
    $departemen = trim($_POST['departemen'] ?? '');
    $status_karyawan = $_POST['status_karyawan'] ?? '';
    $tempat_lahir = trim($_POST['tempat_lahir'] ?? '');
    $tanggal_lahir = trim($_POST['tanggal_lahir'] ?? '');
    $jenis_kelamin = $_POST['jenis_kelamin'] ?? '';
    $foto_lama = $u['foto'] ?? '';
    $foto = $foto_lama;
    $foto_baru_path = '';

    if ($nama === '') {
        $error = 'Nama lengkap wajib diisi.';
    } elseif ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Format email belum benar.';
    } elseif (!in_array($jenis_kelamin, ['Laki-laki', 'Perempuan'], true)) {
        $error = 'Jenis kelamin tidak valid.';
    } elseif (!in_array($status_karyawan, ['Tetap', 'Kontrak', 'Magang'], true)) {
        $error = 'Status karyawan tidak valid.';
    } elseif ($tanggal_lahir !== '' && !DateTime::createFromFormat('Y-m-d', $tanggal_lahir)) {
        $error = 'Tanggal lahir tidak valid.';
    }

    $upload_error = $_FILES['foto']['error'] ?? UPLOAD_ERR_NO_FILE;
    if ($error === '' && $upload_error !== UPLOAD_ERR_NO_FILE) {
        if ($upload_error !== UPLOAD_ERR_OK) {
            $error = 'Foto gagal diunggah. Silakan coba lagi.';
        } elseif ($_FILES['foto']['size'] > 2 * 1024 * 1024) {
            $error = 'Ukuran foto maksimal 2 MB.';
        } else {
            $mime = (new finfo(FILEINFO_MIME_TYPE))->file($_FILES['foto']['tmp_name']);
            $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
            if (!isset($extensions[$mime]) || @getimagesize($_FILES['foto']['tmp_name']) === false) {
                $error = 'Format foto harus JPG, PNG, atau WebP.';
            } else {
                $folder = __DIR__ . '/uploads/profile';
                if (!is_dir($folder) && !mkdir($folder, 0755, true)) {
                    $error = 'Folder penyimpanan foto tidak dapat dibuat.';
                } else {
                    $nama_file = 'profile_' . preg_replace('/[^A-Za-z0-9_-]/', '', $user_id) . '_' . bin2hex(random_bytes(8)) . '.' . $extensions[$mime];
                    $foto_baru_path = $folder . '/' . $nama_file;
                    if (move_uploaded_file($_FILES['foto']['tmp_name'], $foto_baru_path)) {
                        $foto = 'uploads/profile/' . $nama_file;
                    } else {
                        $error = 'Foto tidak dapat dipindahkan ke folder penyimpanan.';
                    }
                }
            }
        }
    }

    if ($error === '') {
        $tanggal_lahir = $tanggal_lahir !== '' ? $tanggal_lahir : null;
        $stmt = mysqli_prepare($conn, 'UPDATE user SET nama=?, nip=?, nik=?, departemen=?, status_karyawan=?, email=?, telepon=?, alamat=?, tempat_lahir=?, tanggal_lahir=?, jenis_kelamin=?, foto=? WHERE user_id=?');
        if (!$stmt) {
            $error = 'Query penyimpanan gagal dibuat: ' . mysqli_error($conn);
        } else {
            mysqli_stmt_bind_param($stmt, 'sssssssssssss', $nama, $nip, $nik, $departemen, $status_karyawan, $email, $telepon, $alamat, $tempat_lahir, $tanggal_lahir, $jenis_kelamin, $foto, $user_id);
            if (mysqli_stmt_execute($stmt)) {
                mysqli_stmt_close($stmt);
                if ($foto_baru_path !== '' && strpos($foto_lama, 'uploads/profile/') === 0 && is_file(__DIR__ . '/' . $foto_lama)) {
                    unlink(__DIR__ . '/' . $foto_lama);
                }
                $_SESSION['nama'] = $nama;
                header('Location: profile.php?pesan=sukses');
                exit;
            }
            $error = 'Data belum dapat disimpan: ' . mysqli_stmt_error($stmt);
            mysqli_stmt_close($stmt);
            if ($foto_baru_path !== '' && is_file($foto_baru_path)) {
                unlink($foto_baru_path);
            }
        }
    }

    // Tetap tampilkan data yang baru diketik ketika validasi atau simpan gagal.
    $u = array_merge($u, [
        'nama' => $nama, 'email' => $email, 'telepon' => $telepon, 'alamat' => $alamat,
        'tempat_lahir' => $tempat_lahir, 'tanggal_lahir' => $tanggal_lahir,
        'jenis_kelamin' => $jenis_kelamin, 'nip' => $nip, 'nik' => $nik, 'departemen' => $departemen,
        'status_karyawan' => $status_karyawan, 'foto' => $foto_lama,
    ]);
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubah Data - SIAP Kemenkumham</title>
    <link rel="stylesheet" href="style.css?v=5">
</head>
<body class="app-body">
    <div class="app-shell">
        <?php include 'sidebar.php'; ?>
        <main class="main-content">
            <div class="content-header">
                <div>
                    <h1>Ubah Data Diri</h1>
                    <p class="page-subtitle">Perbarui data pribadi dan informasi kontak Anda.</p>
                </div>
                <a href="profile.php" class="link-btn">&larr; Kembali ke Profil</a>
            </div>

            <?php if ($error !== ''): ?>
                <div class="alert-box alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <section class="edit-profile-card">
                <form method="POST" class="edit-profile-form" enctype="multipart/form-data">
                    <div class="form-section">
                        <h2>Data Pribadi</h2>
                        <div class="form-grid">
                            <div class="form-field form-field-full">
                                <label for="nama">Nama Lengkap</label>
                                <input id="nama" type="text" name="nama" value="<?= htmlspecialchars($u['nama'] ?? '') ?>" required>
                            </div>
                            <div class="form-field form-field-full">
                                <label for="foto">Foto Profil</label>
                                <input id="foto" type="file" name="foto" accept="image/jpeg,image/png,image/webp">
                                <small class="field-help">Opsional. JPG, PNG, atau WebP; maksimal 2 MB.</small>
                            </div>
                            <div class="form-field">
                                <label for="nip">NIP</label>
                                <input id="nip" type="text" name="nip" inputmode="numeric" value="<?= htmlspecialchars($u['nip'] ?? '') ?>">
                            </div>
                            <div class="form-field">
                                <label for="jenis_kelamin">Jenis Kelamin</label>
                                <select id="jenis_kelamin" name="jenis_kelamin" required>
                                    <option value="Laki-laki" <?= ($u['jenis_kelamin'] ?? '') === 'Laki-laki' ? 'selected' : '' ?>>Laki-laki</option>
                                    <option value="Perempuan" <?= ($u['jenis_kelamin'] ?? '') === 'Perempuan' ? 'selected' : '' ?>>Perempuan</option>
                                </select>
                            </div>
                            <div class="form-field">
                                <label for="tanggal_lahir">Tanggal Lahir</label>
                                <input id="tanggal_lahir" type="date" name="tanggal_lahir" value="<?= htmlspecialchars($u['tanggal_lahir'] ?? '') ?>">
                            </div>
                            <div class="form-field form-field-full">
                                <label for="tempat_lahir">Tempat Lahir</label>
                                <input id="tempat_lahir" type="text" name="tempat_lahir" value="<?= htmlspecialchars($u['tempat_lahir'] ?? '') ?>">
                            </div>
                            <div class="form-field">
                                <label for="nik">NIK</label>
                                <input id="nik" type="text" name="nik" inputmode="numeric" value="<?= htmlspecialchars($u['nik'] ?? '') ?>">
                            </div>
                            <div class="form-field">
                                <label for="departemen">Departemen</label>
                                <input id="departemen" type="text" name="departemen" value="<?= htmlspecialchars($u['departemen'] ?? '') ?>">
                            </div>
                            <div class="form-field">
                                <label for="status_karyawan">Status Karyawan</label>
                                <select id="status_karyawan" name="status_karyawan" required>
                                    <option value="Tetap" <?= ($u['status_karyawan'] ?? 'Tetap') === 'Tetap' ? 'selected' : '' ?>>Tetap</option>
                                    <option value="Kontrak" <?= ($u['status_karyawan'] ?? '') === 'Kontrak' ? 'selected' : '' ?>>Kontrak</option>
                                    <option value="Magang" <?= ($u['status_karyawan'] ?? '') === 'Magang' ? 'selected' : '' ?>>Magang</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <h2>Informasi Kontak</h2>
                        <div class="form-grid">
                            <div class="form-field">
                                <label for="email">Email</label>
                                <input id="email" type="email" name="email" value="<?= htmlspecialchars($u['email'] ?? '') ?>">
                            </div>
                            <div class="form-field">
                                <label for="telepon">Nomor Telepon</label>
                                <input id="telepon" type="tel" name="telepon" value="<?= htmlspecialchars($u['telepon'] ?? '') ?>">
                            </div>
                            <div class="form-field form-field-full">
                                <label for="alamat">Alamat</label>
                                <textarea id="alamat" name="alamat" rows="4"><?= htmlspecialchars($u['alamat'] ?? '') ?></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="modal-actions edit-actions">
                        <a href="profile.php" class="btn-cancel">Batal</a>
                        <button type="submit" class="btn-solid">Simpan Perubahan</button>
                    </div>
                </form>
            </section>
        </main>
    </div>
</body>
</html>
