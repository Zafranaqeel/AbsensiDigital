<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'connection.php';

// ============ PROSES ABSENSI ============
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $latitude = isset($_POST['latitude']) ? $_POST['latitude'] : null;
    $longitude = isset($_POST['longitude']) ? $_POST['longitude'] : null;
    $jarak = isset($_POST['jarak']) ? (int)$_POST['jarak'] : null;
    
    $user_id = $_SESSION['user_id'];
    $tanggal = date('Y-m-d');
    $jam_sekarang = date('H:i:s');
    
    // Validasi lokasi
    if ($latitude === null || $longitude === null || $jarak === null) {
        $error = "Gagal mendapatkan lokasi! Silakan izinkan akses lokasi di browser Anda.";
    } else {
        // Koordinat sekolah (GANTI DENGAN KOORDINAT ASLI SEKOLAH!)
        $school_lat = -6.7145;  // GANTI dengan latitude asli sekolah
        $school_lng = 108.6432; // GANTI dengan longitude asli sekolah
        $max_distance = 100; // Meter
        
        // Fungsi hitung jarak
        function calculateDistance($lat1, $lon1, $lat2, $lon2) {
            $R = 6371000;
            $dLat = ($lat2 - $lat1) * M_PI / 180;
            $dLon = ($lon2 - $lon1) * M_PI / 180;
            $a = sin($dLat/2) * sin($dLat/2) +
                 cos($lat1 * M_PI / 180) * cos($lat2 * M_PI / 180) *
                 sin($dLon/2) * sin($dLon/2);
            $c = 2 * atan2(sqrt($a), sqrt(1-$a));
            return $R * $c;
        }
        
        $jarak_server = round(calculateDistance($latitude, $longitude, $school_lat, $school_lng));
        
        // Cek jarak
        if ($jarak_server > $max_distance) {
            $error = "Absensi Gagal! Anda berada $jarak_server meter dari sekolah. Maksimal jarak absensi adalah $max_distance meter.";
        } else {
            // Cek apakah sudah absen hari ini
            $check_query = "SELECT * FROM absensi WHERE user_id = $user_id AND tanggal = '$tanggal'";
            $check_result = mysqli_query($conn, $check_query);
            
            if (mysqli_num_rows($check_result) > 0) {
                $error = "Anda sudah melakukan absensi hari ini!";
            } else {
                // Tentukan status berdasarkan jam
                if ($jam_sekarang < '07:00:00') {
                    $status = 'Terlalu awal';
                } elseif ($jam_sekarang <= '08:00:00') {
                    $status = 'Hadir';
                } elseif ($jam_sekarang <= '10:00:00') {
                    $status = 'Terlambat';
                } else {
                    $status = 'Alpha';
                }
                
                // Simpan absensi
                $query = "INSERT INTO absensi (user_id, tanggal, jam_masuk, status, latitude, longitude, jarak) 
                          VALUES ($user_id, '$tanggal', '$jam_sekarang', '$status', '$latitude', '$longitude', $jarak_server)";
                
                if (mysqli_query($conn, $query)) {
                    $success = "✅ Absensi berhasil! Status: $status";
                } else {
                    $error = "Gagal menyimpan absensi: " . mysqli_error($conn);
                }
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Absensi - SMA Negeri 1 Lemahabang</title>
    <link rel="stylesheet" href="Style/dashboard_style.css">
    <link rel="icon" href="Assets/smansalaLogo.ico">
    <style>
        .container {
            max-width: 600px;
            margin: 50px auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .location-status {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            text-align: center;
        }
        .status-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .status-loading {
            background: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
        .status-info {
            background: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }
        .btn-absen {
            background: #3498db;
            color: white;
            padding: 15px 30px;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            cursor: pointer;
            width: 100%;
            margin-top: 20px;
        }
        .btn-absen:hover {
            background: #2980b9;
        }
        .btn-absen:disabled {
            background: #95a5a6;
            cursor: not-allowed;
        }
        .btn-back {
            display: inline-block;
            margin-top: 15px;
            text-align: center;
            width: 100%;
            text-decoration: none;
            color: #666;
            padding: 10px;
        }
        .btn-back:hover {
            color: #333;
        }
        .info-lokasi {
            font-family: monospace;
            font-size: 12px;
            margin-top: 10px;
            word-break: break-all;
        }
        .jarak {
            font-weight: bold;
            font-size: 16px;
            margin-top: 10px;
        }
        .jarak-valid {
            color: #27ae60;
        }
        .jarak-invalid {
            color: #e74c3c;
        }
        h2 {
            text-align: center;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        .subtitle {
            text-align: center;
            color: #666;
            margin-bottom: 20px;
        }
        .alert {
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: center;
        }
        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>📝 Form Absensi</h2>
    <div class="subtitle">SMA Negeri 1 Lemahabang Kabupaten Cirebon</div>
    
    <!-- Tampilkan pesan sukses/error -->
    <?php if ($success): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($success); ?>
        </div>
        <a href="dashboard.php" class="btn-back">← Kembali ke Dashboard</a>
    <?php elseif ($error): ?>
        <div class="alert alert-error">
            ❌ <?php echo htmlspecialchars($error); ?>
        </div>
        <a href="absen_process.php" class="btn-back">🔄 Coba Lagi</a>
        <a href="dashboard.php" class="btn-back">← Kembali ke Dashboard</a>
    <?php else: ?>
    
    <div id="locationStatus" class="location-status status-loading">
        ⏳ Mengambil lokasi Anda...
    </div>
    
    <form id="absenForm" method="POST">
        <input type="hidden" name="latitude" id="latitude">
        <input type="hidden" name="longitude" id="longitude">
        <input type="hidden" name="jarak" id="jarak">
        
        <div class="info-lokasi" id="infoLokasi"></div>
        <div class="jarak" id="jarakDisplay"></div>
        
        <button type="submit" id="btnAbsen" class="btn-absen" disabled>📍 Mendapatkan Lokasi...</button>
    </form>
    
    <a href="dashboard.php" class="btn-back">← Kembali ke Dashboard</a>
    
    <?php endif; ?>
</div>

<script>
// Koordinat sekolah SMA Negeri 1 Lemahabang
const SCHOOL_LAT = -6.844710;  // -6.830275
const SCHOOL_LNG = 108.620505; // 108.621136
const MAX_DISTANCE = 100; // Meter (batas maksimal absensi)

// Fungsi menghitung jarak antara dua koordinat (Haversine formula)
function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371000; // Radius bumi dalam meter
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}

// Fungsi untuk mendapatkan lokasi
function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            showPosition, 
            showError,
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    } else {
        document.getElementById('locationStatus').innerHTML = '❌ Browser Anda tidak mendukung Geolocation!';
        document.getElementById('locationStatus').className = 'location-status status-error';
        document.getElementById('btnAbsen').disabled = true;
    }
}

// Fungsi jika berhasil mendapatkan lokasi
function showPosition(position) {
    const userLat = position.coords.latitude;
    const userLng = position.coords.longitude;
    const accuracy = position.coords.accuracy; // Akurasi dalam meter
    
    // Hitung jarak ke sekolah
    const distance = calculateDistance(userLat, userLng, SCHOOL_LAT, SCHOOL_LNG);
    const distanceRounded = Math.round(distance);
    
    // Isi form
    document.getElementById('latitude').value = userLat;
    document.getElementById('longitude').value = userLng;
    document.getElementById('jarak').value = distanceRounded;
    
    // Tampilkan info lokasi
    document.getElementById('infoLokasi').innerHTML = `
        📍 Lokasi Anda: ${userLat.toFixed(6)}, ${userLng.toFixed(6)}<br>
        🎯 Akurasi GPS: ±${Math.round(accuracy)} meter
    `;
    
    // Tampilkan jarak
    const jarakDisplay = document.getElementById('jarakDisplay');
    if (distanceRounded <= MAX_DISTANCE) {
        jarakDisplay.innerHTML = `📏 Jarak ke sekolah: ${distanceRounded} meter`;
        jarakDisplay.className = 'jarak jarak-valid';
        document.getElementById('locationStatus').innerHTML = '✅ Lokasi berhasil didapatkan! Anda berada dalam jangkauan absensi.';
        document.getElementById('locationStatus').className = 'location-status status-success';
        document.getElementById('btnAbsen').disabled = false;
        document.getElementById('btnAbsen').innerHTML = '✅ Absen Sekarang';
    } else {
        jarakDisplay.innerHTML = `❌ Jarak ke sekolah: ${distanceRounded} meter (Terlalu jauh! Maksimal ${MAX_DISTANCE} meter)`;
        jarakDisplay.className = 'jarak jarak-invalid';
        document.getElementById('locationStatus').innerHTML = `⚠️ Anda berada ${distanceRounded} meter dari sekolah, melebihi batas maksimal ${MAX_DISTANCE} meter!`;
        document.getElementById('locationStatus').className = 'location-status status-error';
        document.getElementById('btnAbsen').disabled = true;
        document.getElementById('btnAbsen').innerHTML = '❌ Di luar jangkauan absensi';
    }
}

// Fungsi jika gagal mendapatkan lokasi
function showError(error) {
    let errorMessage = '';
    switch(error.code) {
        case error.PERMISSION_DENIED:
            errorMessage = 'Anda menolak izin akses lokasi. Silakan izinkan akses lokasi di browser Anda.';
            break;
        case error.POSITION_UNAVAILABLE:
            errorMessage = 'Informasi lokasi tidak tersedia. Pastikan GPS perangkat Anda aktif.';
            break;
        case error.TIMEOUT:
            errorMessage = 'Waktu permintaan lokasi habis. Silakan coba lagi.';
            break;
        default:
            errorMessage = 'Terjadi kesalahan saat mengambil lokasi.';
    }
    
    document.getElementById('locationStatus').innerHTML = `❌ ${errorMessage}`;
    document.getElementById('locationStatus').className = 'location-status status-error';
    document.getElementById('btnAbsen').disabled = true;
    document.getElementById('btnAbsen').innerHTML = '❌ Gagal Mendapatkan Lokasi';
    document.getElementById('infoLokasi').innerHTML = 'Silakan refresh halaman dan izinkan akses lokasi.';
}

// Cek apakah sudah absen hari ini (dari server)
function checkTodayAbsence() {
    fetch('check_absensi.php')
        .then(response => response.json())
        .then(data => {
            if (data.absent_today) {
                document.getElementById('locationStatus').innerHTML = '⚠️ Anda sudah melakukan absensi hari ini!';
                document.getElementById('locationStatus').className = 'location-status status-info';
                document.getElementById('btnAbsen').disabled = true;
                document.getElementById('btnAbsen').innerHTML = '✅ Sudah Absen Hari Ini';
            } else {
                getLocation();
            }
        })
        .catch(() => {
            getLocation();
        });
}

// Jalankan pengecekan saat halaman load
window.onload = checkTodayAbsence;
</script>

</body>
</html>