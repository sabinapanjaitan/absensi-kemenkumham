<?php
session_start();

// Jika sudah login, langsung ke dashboard sesuai role
if (isset($_SESSION['login'])) {
    header("Location: " . (($_SESSION['role'] ?? 'karyawan') === 'admin' ? 'admin_dashboard.php' : 'dashboard.php'));
    exit;
}

// Koneksi database
$conn = mysqli_connect("localhost", "root", "", "db_absensi");

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id  = trim($_POST['user_id']);
    $password = $_POST['password'];

    // Prepared statement - AMAN dari SQL Injection
    $stmt = mysqli_prepare($conn, "SELECT * FROM user WHERE user_id = ? LIMIT 1");
    mysqli_stmt_bind_param($stmt, "s", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $data   = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);

    // Cek password (support bcrypt DAN plain text lama)
    if ($data && (password_verify($password, $data['password']) || $password === $data['password'])) {
        $_SESSION['login']   = true;
        $_SESSION['user_id'] = $data['user_id'];
        $_SESSION['nama']    = $data['nama'];
        $_SESSION['role']    = $data['role'] ?? 'karyawan';

        if ($_SESSION['role'] === 'admin') {
            header("Location: admin_dashboard.php");
        } else {
            header("Location: dashboard.php");
        }
        exit;
    } else {
        $error = 'User ID atau Password salah.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Absensi Kemenkumham</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-body">

    <div class="login-box">
        <div class="login-logo">
            <img src="logo.png" alt="Logo Kemenkumham" style="height: 100px; margin-bottom:5px;">
        </div>

        <h2 style="margin:0 0 24px 0; font-size:25px; color:#6b7280; font-weight:750;">Selamat Datang</h2>

        <?php if ($error): ?>
            <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label>User ID</label>
                <input type="text" name="user_id" class="form-control" 
                       placeholder="Enter your User ID" required>
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" 
                       placeholder="Enter your Password" required>
            </div>

            <button type="submit" class="btn btn-primary">Login</button>
        </form>

        <p style="margin-top:12px; text-align:center;">
            <a href="#">Forgot Password?</a>
        </p>

        <p class="copyright" style="position:static; margin-top:10px; transform:none; bottom:auto; left:auto; width:auto; border-top:1px solid rgba(107, 114, 128, 0.3); padding-top:12px; color:#6b7280;">
            &copy; 2024 Kementerian Hukum dan Hak Asasi Manusia Republik Indonesia
        </p>
    </div>

</body>
</html>