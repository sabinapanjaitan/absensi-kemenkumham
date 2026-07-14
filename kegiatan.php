<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$tanggal_filter = trim($_GET['tanggal'] ?? date('Y-m-d'));
$search = trim($_GET['search'] ?? '');

$where = "WHERE user_id = '".mysqli_real_escape_string($conn, $user_id)."' AND tanggal = '".mysqli_real_escape_string($conn, $tanggal_filter)."'";
if ($search !== '') {
    $esc = mysqli_real_escape_string($conn, $search);
    $where .= " AND (nama_kegiatan LIKE '%$esc%' OR departemen LIKE '%$esc%')";
}

// Pastikan kolom untuk ORDER BY tersedia di DB, fallback ke `id` jika tidak ada
$order_by = 'waktu_kegiatan';
$col_check = mysqli_query($conn, "SHOW COLUMNS FROM kegiatan LIKE 'waktu_kegiatan'");
if ($col_check === false || mysqli_num_rows($col_check) == 0) {
    $order_by = 'id';
}

$sql = "SELECT * FROM kegiatan WHERE user_id = ? AND tanggal = ? ORDER BY $order_by ASC";
$stmt = mysqli_prepare($conn, $sql);
if ($stmt) {
    mysqli_stmt_bind_param($stmt, 'ss', $user_id, $tanggal_filter);
    mysqli_stmt_execute($stmt);
    $data = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
} else {
    $data = false; // mark as failed
}

$hari_id_full = ['Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];
$tgl_ts = strtotime($tanggal_filter);
$tgl_display = $hari_id_full[date('l', $tgl_ts)] . ', ' . date('d M Y', $tgl_ts);

$pesan = $_GET['pesan'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kegiatan Harian - SIAP Kemenkumham</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="app-body">
    <div class="app-shell">
        <?php include 'sidebar.php'; ?>

        <div class="main-content">
            <div class="content-header">
                <h1>Daftar Kegiatan Harian</h1>
                <button type="button" class="btn-search-action" onclick="document.getElementById('modalTambah').classList.add('show')">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v14M5 12h14"/></svg>
                    Tambah Kegiatan
                </button>
            </div>

            <?php if ($pesan === 'sukses'): ?>
                <div class="alert-box alert-success">Kegiatan berhasil ditambahkan.</div>
            <?php elseif ($pesan === 'deleted'): ?>
                <div class="alert-box alert-success">Kegiatan berhasil dihapus.</div>
            <?php elseif ($pesan === 'error'): ?>
                <div class="alert-box alert-error">Terjadi kesalahan. Coba lagi.</div>
            <?php endif; ?>

            <form method="GET" class="table-toolbar" style="justify-content:space-between;padding:0 0 16px 0;">
                <label class="date-pill" style="cursor:pointer;" title="<?= $tgl_display ?>">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="17" rx="2"/><path d="M3 9h18M8 2v4M16 2v4"/></svg>
                    <input type="date" name="tanggal" value="<?= htmlspecialchars($tanggal_filter) ?>" onchange="this.form.submit()" style="border:none;background:transparent;font-family:inherit;font-size:13px;font-weight:600;color:var(--text-dark);outline:none;">
                </label>
                <div class="search-box" style="min-width:200px;">
                    <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/></svg>
                    <input type="text" name="search" placeholder="Search" value="<?= htmlspecialchars($search) ?>" onkeyup="if(event.key==='Enter')this.form.submit()">
                </div>
            </form>

            <div class="panel-card">
                <div class="panel-inner">
                    <div class="table-card">
                        <table class="styled-table">
                    <thead>
                        <tr>
                            <th>WAKTU</th>
                            <th>NAMA KEGIATAN</th>
                            <th>DEPARTEMEN</th>
                            <th>STATUS</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if ($data === false): ?>
                        <tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:26px;">Terjadi kesalahan pada query: <?= htmlspecialchars(mysqli_error($conn)) ?></td></tr>
                    <?php elseif (mysqli_num_rows($data) == 0): ?>
                        <tr><td colspan="4" style="text-align:center;color:var(--text-muted);padding:26px;">Belum ada kegiatan pada tanggal ini.</td></tr>
                    <?php else: while ($k = mysqli_fetch_assoc($data)):
                        $badge = ($k['status_kegiatan'] == 'Selesai') ? 'badge-success' : 'badge-onprogress';
                    ?>
                        <tr>
                            <td><?= htmlspecialchars($k['waktu_kegiatan']) ?></td>
                            <td><?= htmlspecialchars($k['nama_kegiatan']) ?></td>
                            <td><?= htmlspecialchars($k['departemen']) ?></td>
                            <td>
                                <span class="badge <?= $badge ?>"><?= htmlspecialchars($k['status_kegiatan']) ?></span>
                                <?php
                                    $del_id = isset($k['id']) ? (int)$k['id'] : 0;
                                    $del_w = urlencode($k['waktu_kegiatan']);
                                    $del_n = urlencode($k['nama_kegiatan']);
                                    $del_t = urlencode($tanggal_filter);
                                ?>
                                <a href="proses_kegiatan.php?delete_id=<?= $del_id ?>&w=<?= $del_w ?>&n=<?= $del_n ?>&t=<?= $del_t ?>" onclick="return confirm('Yakin ingin menghapus kegiatan ini?');" class="icon-btn" style="display:inline-block;margin-left:8px;" title="Hapus kegiatan">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                                </a>
                            </td>
                        </tr>
                    <?php endwhile; endif; ?>
                    </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Tambah Kegiatan -->
    <div class="modal-overlay" id="modalTambah">
        <div class="modal-box">
            <h3>Tambah Kegiatan</h3>
            <form action="proses_kegiatan.php" method="POST">
                <input type="hidden" name="tanggal" value="<?= htmlspecialchars($tanggal_filter) ?>">
                <div class="form-field">
                    <label>Waktu Mulai</label>
                    <div style="display:flex;gap:8px;">
                        <select name="waktu_mulai_hour" required style="width:110px;padding:10px;border:1px solid #dcdfe6;border-radius:8px;">
                            <option value="">Jam</option>
                            <?php for ($h=0;$h<24;$h++): $hh = str_pad($h,2,'0',STR_PAD_LEFT); ?>
                                <option value="<?= $hh ?>"><?= $hh ?></option>
                            <?php endfor; ?>
                        </select>
                        <select name="waktu_mulai_minute" required style="width:110px;padding:10px;border:1px solid #dcdfe6;border-radius:8px;">
                            <option value="">Menit</option>
                            <?php for ($mm=0; $mm<60; $mm++): $m = str_pad($mm,2,'0',STR_PAD_LEFT); ?>
                                <option value="<?= $m ?>"><?= $m ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div class="form-field">
                    <label>Waktu Selesai</label>
                    <div style="display:flex;gap:8px;">
                        <select name="waktu_selesai_hour" required style="width:110px;padding:10px;border:1px solid #dcdfe6;border-radius:8px;">
                            <option value="">Jam</option>
                            <?php for ($h=0;$h<24;$h++): $hh = str_pad($h,2,'0',STR_PAD_LEFT); ?>
                                <option value="<?= $hh ?>"><?= $hh ?></option>
                            <?php endfor; ?>
                        </select>
                        <select name="waktu_selesai_minute" required style="width:110px;padding:10px;border:1px solid #dcdfe6;border-radius:8px;">
                            <option value="">Menit</option>
                            <?php for ($mm=0; $mm<60; $mm++): $m = str_pad($mm,2,'0',STR_PAD_LEFT); ?>
                                <option value="<?= $m ?>"><?= $m ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div class="form-field">
                    <label>Nama Kegiatan</label>
                    <input type="text" name="nama_kegiatan" placeholder="Contoh: Registrasi Surat Masuk" required>
                </div>
                <div class="form-field">
                    <label>Departemen</label>
                    <input type="text" name="departemen" placeholder="Contoh: Subbag Tata Usaha" required>
                </div>
                <div class="form-field">
                    <label>Status</label>
                    <select name="status_kegiatan">
                        <option value="On Progress">On Progress</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="document.getElementById('modalTambah').classList.remove('show')">Batal</button>
                    <button type="submit" class="btn-solid" style="border:none;">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
