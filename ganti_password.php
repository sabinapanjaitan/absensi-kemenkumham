<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password_lama  = $_POST['password_lama'];
    $password_baru  = $_POST['password_baru'];
    $password_ulang = $_POST['password_ulang'];

    $stmt = mysqli_prepare($conn, "SELECT password FROM user WHERE user_id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $user_id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_stmt_get_result($stmt)->fetch_assoc();
    mysqli_stmt_close($stmt);

    $cocok = $row && (password_verify($password_lama, $row['password']) || $password_lama === $row['password']);

    if (!$cocok) {
        header("Location: profile.php?pesan=pw_gagal");
        exit;
    } elseif ($password_baru !== $password_ulang) {
        $error = 'Konfirmasi password baru tidak cocok.';
    } elseif (strlen($password_baru) < 6) {
        $error = 'Password baru minimal 6 karakter.';
    } else {
        $hash = password_hash($password_baru, PASSWORD_DEFAULT);
        $stmt = mysqli_prepare($conn, "UPDATE user SET password = ? WHERE user_id = ?");
        mysqli_stmt_bind_param($stmt, "ss", $hash, $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        header("Location: profile.php?pesan=pw_sukses");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ganti Password - SIAP Kemenkumham</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="app-body">
    <div class="app-shell">
        <?php include 'sidebar.php'; ?>

        <div class="main-content">
            <div class="content-header">
                <h1>Ganti Password</h1>
                <a href="profile.php" class="link-btn">&larr; Kembali ke Profil</a>
            </div>

            <?php if ($error): ?>
                <div class="alert-box alert-error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <div class="panel-card" style="padding:26px; max-width:420px;">
                <form method="POST">
                    <div class="form-field">
                        <label>Password Lama</label>
                        <input type="password" name="password_lama" required>
                    </div>
                    <div class="form-field">
                        <label>Password Baru</label>
                        <input type="password" name="password_baru" required minlength="6">
                    </div>
                    <div class="form-field">
                        <label>Ulangi Password Baru</label>
                        <input type="password" name="password_ulang" required minlength="6">
                    </div>
                    <div class="modal-actions" style="margin-top:6px;">
                        <a href="profile.php" class="btn-cancel">Batal</a>
                        <button type="submit" class="btn-solid" style="border:none;">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
