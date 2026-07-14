<?php
// Komponen sidebar - digunakan di semua halaman setelah login
// Membutuhkan session sudah di-start dan $_SESSION['nama'] tersedia
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar">
    <div class="sidebar-logo">
        <div class="logo-mark">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2 4 7v2h16V7l-8-5Z"/><path d="M6 10v9M10 10v9M14 10v9M18 10v9"/><path d="M3 21h18"/></svg>
        </div>
        <h3>SIAP</h3>
    </div>

    <nav class="sidebar-nav">
        <a href="dashboard.php" class="<?= ($current_page == 'dashboard.php') ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9.5 12 3l9 6.5"/><path d="M5 9.5V21h14V9.5"/><path d="M9 21v-6h6v6"/></svg>
            Dashboard
        </a>
        <a href="riwayat.php" class="<?= ($current_page == 'riwayat.php') ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg>
            Riwayat Absensi
        </a>
        <a href="calendar.php" class="<?= ($current_page == 'calendar.php') ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M3 9h18M8 2v4M16 2v4"/></svg>
            Calendar
        </a>
        <a href="kegiatan.php" class="<?= ($current_page == 'kegiatan.php') ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4Z"/></svg>
            Kegiatan Harian
        </a>
        <a href="profile.php" class="<?= ($current_page == 'profile.php') ? 'active' : '' ?>">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4.4 3.6-8 8-8s8 3.6 8 8"/></svg>
            Profile
        </a>
    </nav>

    <div class="sidebar-footer">
        <a href="logout.php" onclick="return confirm('Yakin ingin keluar?');">
            <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
            Log Out
        </a>
    </div>
</div>
