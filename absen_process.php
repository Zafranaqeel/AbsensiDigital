<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'connection.php';

// ================== PROSES ABSENSI ==================
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    date_default_timezone_set('Asia/Jakarta');

    $user_id = $_SESSION['user_id'];
    $tanggal = date('Y-m-d');
    $jam_sekarang = date('H:i:s');

    // Ambil status absensi
    $status_input = $_POST['status'] ?? 'hadir';

    // Default status
    $status = '';

    // Lokasi
    $latitude = $_POST['latitude'] ?? null;
    $longitude = $_POST['longitude'] ?? null;

    // Upload surat
    $surat_name = null;

    // ================== VALIDASI SURAT ==================
    if ($status_input == 'izin' || $status_input == 'sakit') {

        if (!isset($_FILES['surat']) || $_FILES['surat']['error'] != 0) {

            $error = "Surat wajib diupload untuk izin/sakit!";

        } else {

            $target_dir = "uploads/surat/";

            // Buat folder jika belum ada
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }

            $file_name = time() . "_" . basename($_FILES["surat"]["name"]);
            $target_file = $target_dir . $file_name;

            if (move_uploaded_file($_FILES["surat"]["tmp_name"], $target_file)) {

                $surat_name = $file_name;

            } else {

                $error = "Gagal upload surat!";
            }
        }
    }

    // ================== CEK SUDAH ABSEN ==================
    if (empty($error)) {

        $check_query = "SELECT * FROM absensi 
                        WHERE user_id = '$user_id' 
                        AND tanggal = '$tanggal'";

        $check_result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($check_result) > 0) {

            $error = "Anda sudah melakukan absensi hari ini!";
        }
    }

    // ================== ABSENSI HADIR ==================
    if (empty($error) && $status_input == 'hadir') {

        // Validasi lokasi
        if ($latitude === null || $longitude === null) {

            $error = "Gagal mendapatkan lokasi!";

        } else {

            // Koordinat sekolah -6.830273, 108.621136
            $school_lat = -6.830273;
            $school_lng = 108.621136;

            $max_distance = 100;

            // Fungsi hitung jarak
            function calculateDistance($lat1, $lon1, $lat2, $lon2)
            {
                $R = 6371000;

                $dLat = ($lat2 - $lat1) * M_PI / 180;
                $dLon = ($lon2 - $lon1) * M_PI / 180;

                $a =
                    sin($dLat / 2) * sin($dLat / 2) +
                    cos($lat1 * M_PI / 180) *
                    cos($lat2 * M_PI / 180) *
                    sin($dLon / 2) *
                    sin($dLon / 2);

                $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

                return $R * $c;
            }

            $jarak_server = round(
                calculateDistance(
                    $latitude,
                    $longitude,
                    $school_lat,
                    $school_lng
                )
            );

            // Validasi jarak
            if ($jarak_server > $max_distance) {

                $error = "Anda berada $jarak_server meter dari sekolah!";

            } else {

                // Validasi jam
                if ($jam_sekarang < '05:00:00') {

                    $error = "Absensi belum dibuka!";

                } elseif ($jam_sekarang <= '06:15:00') {

                    $status = 'hadir';

                } else {

                    $status = 'terlambat';
                }
            }
        }

    } elseif (empty($error) && $status_input == 'izin') {

        $status = 'izin';
        $jarak_server = 0;

    } elseif (empty($error) && $status_input == 'sakit') {

        $status = 'sakit';
        $jarak_server = 0;
    }

    // ================== SIMPAN DATABASE ==================
    if (empty($error)) {

        $query = "INSERT INTO absensi 
        (
            user_id,
            tanggal,
            jam_masuk,
            latitude,
            longitude,
            jarak,
            `status`,
            surat
        )
        VALUES
        (
            '$user_id',
            '$tanggal',
            '$jam_sekarang',
            '$latitude',
            '$longitude',
            '$jarak_server',
            '$status',
            '$surat_name'
        )";

        if (mysqli_query($conn, $query)) {

            $success = "✅ Absensi berhasil! Status: " . strtoupper($status);

        } else {

            $error = "Gagal menyimpan absensi: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Absensi Digital</title>
    <link rel="stylesheet" href="Style/absenStyle.css">
</head>

<body>

<div class="container">

    <h2>Form Absensi</h2>

    <?php if ($success): ?>

        <div class="alert-success">
            <?= $success ?>
        </div>

    <?php endif; ?>

    <?php if ($error): ?>

        <div class="alert-error">
            <?= $error ?>
        </div>

    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">

        <input type="hidden" name="latitude" id="latitude">
        <input type="hidden" name="longitude" id="longitude">

        <h4>Status Absensi</h4>

        <label>
            <input type="radio"
                   name="status"
                   value="hadir"
                   checked
                   onchange="toggleSurat()">
            Hadir
        </label>

        <br><br>

        <label>
            <input type="radio"
                   name="status"
                   value="izin"
                   onchange="toggleSurat()">
            Izin
        </label>

        <br><br>

        <label>
            <input type="radio"
                   name="status"
                   value="sakit"
                   onchange="toggleSurat()">
            Sakit
        </label>

        <div id="uploadSurat"
             class="upload-box"
             style="display:none;">

            <label>Upload Surat:</label><br><br>

            <input type="file"
                   name="surat"
                   accept=".jpg,.jpeg,.png,.pdf">
        </div>

        <br>

        <button type="submit"
                id="btnAbsen"
                class="btn">
            Absen Sekarang
        </button>

    </form>

</div>

<script>

function toggleSurat() {

    const status =
        document.querySelector(
            'input[name="status"]:checked'
        ).value;

    const uploadDiv =
        document.getElementById('uploadSurat');

    if (status === 'izin' || status === 'sakit') {

        uploadDiv.style.display = 'block';

    } else {

        uploadDiv.style.display = 'none';
    }
}

// ================== GPS ==================

const SCHOOL_LAT = -6.830273;
const SCHOOL_LNG = 108.621136;

function calculateDistance(lat1, lon1, lat2, lon2) {

    const R = 6371000;

    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;

    const a =
        Math.sin(dLat / 2) * Math.sin(dLat / 2) +
        Math.cos(lat1 * Math.PI / 180) *
        Math.cos(lat2 * Math.PI / 180) *
        Math.sin(dLon / 2) *
        Math.sin(dLon / 2);

    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));

    return R * c;
}

function getLocation() {

    if (navigator.geolocation) {

        navigator.geolocation.getCurrentPosition(function(position) {

            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;

        });

    } else {

        alert("Browser tidak mendukung GPS");
    }
}

window.onload = getLocation;

</script>

</body>
</html>