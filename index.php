<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Absensi Kemenkumham</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="login-body">

    <div class="login-container">
        <img src="logo.png" alt="Logo Kemenkumham" class="login-logo">
        
        <h2>Selamat Datang</h2>
        
        <form action="login.php" method="POST">
            <div class="input-group">
                <label>User ID</label>
                <input type="text" name="user_id" placeholder="Enter your User ID" required>
            </div>
            
            <div class="input-group">
                <label>Password</label>
                <input type="password" name="password" placeholder="Enter your Password" required>
            </div>

            <button type="submit" class="login-btn">Login</button>
        </form>
        
        <p class="forgot-password">
            <a href="#">Forgot Password?</a>
        </p>

        <p class="copyright">
            &copy; 2024 Kementerian Hukum dan Hak Asasi Manusia Republik Indonesia
        </p>
    </div>

</body>
</html>