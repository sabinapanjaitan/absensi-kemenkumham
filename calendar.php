<?php
session_start();
include 'koneksi.php';

if (!isset($_SESSION['login'])) {
    header("Location: index.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$bulan_nama_id = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
$hari_singkat_id = ['MON','TUE','WED','THU','FRI','SAT','SUN'];
$hari_id_full = ['Sunday'=>'Minggu','Monday'=>'Senin','Tuesday'=>'Selasa','Wednesday'=>'Rabu','Thursday'=>'Kamis','Friday'=>'Jumat','Saturday'=>'Sabtu'];

$year  = isset($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
$month = isset($_GET['month']) ? (int)$_GET['month'] : (int)date('n');
if ($month < 1) { $month = 12; $year--; }
if ($month > 12) { $month = 1; $year++; }

$prev_month = $month - 1; $prev_year = $year;
if ($prev_month < 1) { $prev_month = 12; $prev_year--; }
$next_month = $month + 1; $next_year = $year;
if ($next_month > 12) { $next_month = 1; $next_year++; }

$first_ts   = mktime(0,0,0,$month,1,$year);
$days_in_month = (int)date('t', $first_ts);
$first_weekday = (int)date('N', $first_ts); // 1 (Mon) - 7 (Sun)

$today = date('Y-m-d');
$is_current_month = ($year == date('Y') && $month == date('n'));

// Ambil status kehadiran sebulan ini
$status_map = [];
$ym = sprintf('%04d-%02d', $year, $month);
$res = mysqli_query($conn, "SELECT tanggal, status_kehadiran FROM absensi WHERE user_id = '".mysqli_real_escape_string($conn,$user_id)."' AND DATE_FORMAT(tanggal,'%Y-%m') = '$ym'");
while ($r = mysqli_fetch_assoc($res)) {
    $status_map[(int)date('j', strtotime($r['tanggal']))] = $r['status_kehadiran'];
}

function dot_class($status) {
    switch ($status) {
        case 'Hadir':
        case 'Telat':
            return 'dot-hadir';
        case 'Izin':
            return 'dot-izin';
        case 'Absen':
            return 'dot-absen';
        default:
            return '';
    }
}

$nama = $_SESSION['nama'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar - SIAP Kemenkumham</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="app-body" data-server-date="<?= $today ?>">
    <div class="app-shell">
        <?php include 'sidebar.php'; ?>

        <div class="main-content">
            <div class="content-header">
                <h1>Calendar</h1>
                <div class="calendar-header-pill">
                    <div class="nav-row">
                        <a href="?month=<?= $prev_month ?>&year=<?= $prev_year ?>">&lt;</a>
                        <span><?= $bulan_nama_id[$month] ?> <?= $year ?></span>
                        <a href="?month=<?= $next_month ?>&year=<?= $next_year ?>">&gt;</a>
                    </div>
                    <div class="sub-date" id="current-date"><?= $hari_id_full[date('l')] ?>, <?= date('d M Y') ?></div>
                </div>
            </div>

            <div class="panel-card" style="padding:22px 26px;">
                <div class="calendar-month-title"><?= $bulan_nama_id[$month] ?> <?= $year ?></div>

                <table class="calendar-grid">
                    <thead>
                        <tr>
                            <?php foreach ($hari_singkat_id as $h): ?>
                                <th><?= $h ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                    $day = 1;
                    $cell_index = 1;
                    // Baris pertama: isi sel kosong sebelum tanggal 1
                    echo "<tr>";
                    for ($i = 1; $i < $first_weekday; $i++) {
                        echo "<td class='empty'></td>";
                        $cell_index++;
                    }
                    while ($day <= $days_in_month) {
                        $weekday = (($cell_index - 1) % 7) + 1; // 1=Mon..7=Sun
                        $is_weekend = ($weekday == 6 || $weekday == 7);
                        $cell_date = sprintf('%04d-%02d-%02d', $year, $month, $day);
                        $is_today = ($is_current_month && $cell_date == $today);
                        $status = $status_map[$day] ?? null;

                        $classes = [];
                        if ($is_weekend) $classes[] = 'weekend';
                        if ($is_today) $classes[] = 'today';

                        echo "<td class='".implode(' ', $classes)."'>";
                        echo "<span class='day-num'>$day</span>";
                        if ($status) {
                            echo "<span class='dot ".dot_class($status)."'></span>";
                        }
                        echo "</td>";

                        if ($cell_index % 7 == 0 && $day != $days_in_month) {
                            echo "</tr><tr>";
                        }
                        $day++;
                        $cell_index++;
                    }
                    // Tutup baris terakhir jika belum genap 7 kolom
                    while (($cell_index - 1) % 7 != 0) {
                        echo "<td class='empty'></td>";
                        $cell_index++;
                    }
                    echo "</tr>";
                    ?>
                    </tbody>
                </table>

                <div class="calendar-legend">
                    <span><i style="background:var(--green)"></i> Hadir</span>
                    <span><i style="background:var(--orange)"></i> Izin</span>
                    <span><i style="background:var(--red)"></i> Absen</span>
                </div>
            </div>
        </div>
        </div>
        <script>
            (function(){
                function updateDateText(){
                    try{
                        const el = document.getElementById('current-date');
                        if(!el) return;
                        const now = new Date();
                        const opt = { weekday: 'long', day: '2-digit', month: 'short', year: 'numeric' };
                        const text = now.toLocaleDateString('id-ID', opt);
                        el.textContent = text.charAt(0).toUpperCase() + text.slice(1);
                    }catch(e){console.error(e)}
                }

                function syncTodayClass(){
                    try{
                        const now = new Date();
                        const localY = now.getFullYear();
                        const localM = now.getMonth() + 1;
                        const localD = now.getDate();

                        // Prefer the explicit calendar month title inside the panel
                        const titleEl = document.querySelector('.calendar-month-title');
                        if(!titleEl) return;
                        const headerText = titleEl.textContent.trim();
                        const months = { 'Januari':1,'Februari':2,'Maret':3,'April':4,'Mei':5,'Juni':6,'Juli':7,'Agustus':8,'September':9,'Oktober':10,'November':11,'Desember':12 };
                        const parts = headerText.split(' ');
                        const dispMonthName = parts[0] || '';
                        const dispYear = parseInt(parts[1] || '0', 10);
                        const dispMonth = months[dispMonthName] || 0;

                        if(dispYear === localY && dispMonth === localM){
                            // remove all existing .today
                            const prevAll = document.querySelectorAll('.calendar-grid td.today');
                            prevAll.forEach(function(n){ n.classList.remove('today'); });

                            const dayNodes = document.querySelectorAll('.calendar-grid .day-num');
                            for(const dn of dayNodes){
                                if(parseInt(dn.textContent.trim(),10) === localD){
                                    const td = dn.closest('td');
                                    if(td) td.classList.add('today');
                                    break;
                                }
                            }
                        }
                    }catch(e){console.error(e)}
                }

                function msUntilNext(h,m){
                    const now = new Date();
                    const target = new Date(now.getFullYear(), now.getMonth(), now.getDate()+1, h, m, 0, 0);
                    return target - now;
                }

                updateDateText();
                syncTodayClass();

                setInterval(function(){ updateDateText(); syncTodayClass(); }, 60 * 1000);

                try{
                    const serverDate = document.body.dataset.serverDate;
                    if(serverDate){
                        const now = new Date();
                        const localY = now.getFullYear();
                        const localM = String(now.getMonth()+1).padStart(2,'0');
                        const localD = String(now.getDate()).padStart(2,'0');
                        const localStr = `${localY}-${localM}-${localD}`;
                        if(localStr !== serverDate){
                            try{
                                if(localStr > serverDate){
                                    const markerKey = 'calendar_reloaded_for_' + localStr;
                                    if(!sessionStorage.getItem(markerKey)){
                                        sessionStorage.setItem(markerKey, Date.now().toString());
                                        try{ location.reload(); }catch(e){ location.reload(); }
                                        return;
                                    }
                                }
                            }catch(e){ }
                        }
                    }
                }catch(e){ }

                const delay = msUntilNext(0,1);
                if (delay > 0 && delay < 7*24*60*60*1000) {
                    setTimeout(function(){ try{ location.reload(true); }catch(e){ location.reload(); } }, delay);
                }
            })();
        </script>
