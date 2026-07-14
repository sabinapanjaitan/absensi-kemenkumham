<?php
include 'koneksi.php';
session_start();

$query = mysqli_query($conn, "SELECT tanggal, status_kehadiran FROM absensi WHERE user_id = '".$_SESSION['user_id']."'");
$events = [];

while ($row = mysqli_fetch_assoc($query)) {
    // Tentukan warna berdasarkan status
    $color = ($row['status_kehadiran'] == 'Hadir') ? '#28a745' : '#dc3545';
    
    $events[] = [
        'title' => $row['status_kehadiran'],
        'start' => $row['tanggal'],
        'color' => $color
    ];
}

echo json_encode($events);
?>