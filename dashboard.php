<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

include 'connection.php';

// Ambil role dari session
$role = $_SESSION['role'];
$nama = $_SESSION['nama'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard | Absensi Digital</title>
    <link rel="icon" href="Assets/smansalaLogo.png">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
    <style>
        /* RESET & BASE */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f0f2f5;
            min-height: 100vh;
        }

        /* ========== DESKTOP VIEW (Default) ========== */
        
        /* Style untuk Real Time Clock */
        .clock {
            font-family: 'Courier New', monospace;
            font-size: 20px;
            font-weight: bold;
            background: rgba(0, 0, 0, 0.15);
            padding: 10px 20px;
            border-radius: 12px;
            letter-spacing: 1px;
            text-align: center;
            min-width: 200px;
        }

        .date {
            font-size: 12px;
            opacity: 0.9;
            margin-top: 5px;
        }

        /* Style welcome card dengan flex agar jam bisa di kanan */
        .welcome-card {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 25px 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .welcome-text {
            flex: 1;
        }

        .welcome-text h2 {
            font-size: 22px;
            margin-bottom: 8px;
        }

        .welcome-text p {
            opacity: 0.9;
            font-size: 14px;
        }

        .welcome-clock {
            margin-left: 20px;
        }

        /* Navbar */
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            background: #2c3e50;
            color: white;
            padding: 15px 30px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .navbar h1 {
            font-size: 18px;
            margin: 0;
        }

        .right-section {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .role-badge {
            background: #3498db;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .logout-btn {
            background: #e74c3c;
            color: white;
            padding: 8px 15px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            transition: background 0.3s;
        }

        .logout-btn:hover {
            background: #c0392b;
        }

        /* Container */
        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        /* Admin Panel */
        .admin-panel {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            overflow-x: auto;
        }

        .admin-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f2f5;
            flex-wrap: wrap;
            gap: 15px;
        }

        .admin-header h2 {
            color: #2c3e50;
        }

        .btn-primary {
            background: #3498db;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: background 0.3s;
            display: inline-block;
        }

        .btn-primary:hover {
            background: #2980b9;
        }

        /* Tabel User */
        .user-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .user-table th,
        .user-table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .user-table th {
            background: #f8f9fa;
            font-weight: bold;
            color: #555;
        }

        .user-table tr:hover {
            background: #f8f9fa;
        }

        .role-badge-small {
            padding: 3px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            display: inline-block;
        }

        .role-admin {
            background: #e74c3c;
            color: white;
        }

        .role-manager {
            background: #3498db;
            color: white;
        }

        .role-user {
            background: #27ae60;
            color: white;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .edit-btn {
            background: #f39c12;
            color: white;
            padding: 5px 10px;
            text-decoration: none;
            border-radius: 3px;
            font-size: 12px;
        }

        .delete-btn {
            background: #e74c3c;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            font-size: 12px;
            cursor: pointer;
        }

        /* Menu Grid */
        .menu-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .menu-card {
            background: white;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            text-decoration: none;
            color: #333;
            transition: transform 0.3s, box-shadow 0.3s;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: block;
        }

        .menu-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .menu-card .icon {
            font-size: 48px;
            margin-bottom: 15px;
        }

        .menu-card h3 {
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .menu-card p {
            font-size: 14px;
            color: #666;
        }

        footer {
            text-align: center;
            margin-top: 50px;
            padding: 20px;
            color: #666;
            font-size: 14px;
        }

        /* ========== MOBILE VIEW (max-width: 768px) ========== */
        @media (max-width: 768px) {
            /* Navbar Mobile */
            .navbar {
                flex-direction: column;
                text-align: center;
                padding: 12px 15px;
                gap: 10px;
            }

            .navbar h1 {
                font-size: 14px;
            }

            .right-section {
                width: 100%;
                justify-content: center;
            }

            .user-info {
                gap: 10px;
                flex-wrap: wrap;
                justify-content: center;
            }

            .user-info span {
                font-size: 12px;
            }

            .logout-btn {
                padding: 6px 12px;
                font-size: 12px;
            }

            /* Welcome Card Mobile */
            .welcome-card {
                flex-direction: column;
                text-align: center;
                padding: 20px 15px;
                margin-bottom: 20px;
            }

            .welcome-text h2 {
                font-size: 16px;
            }

            .welcome-text p {
                font-size: 12px;
            }

            .welcome-clock {
                margin-left: 0;
                margin-top: 15px;
            }

            .clock {
                font-size: 16px;
                min-width: 160px;
                padding: 8px 15px;
            }

            .date {
                font-size: 10px;
            }

            /* Container Mobile */
            .container {
                margin: 15px auto;
                padding: 0 15px;
            }

            /* Admin Panel Mobile */
            .admin-panel {
                padding: 15px;
                border-radius: 8px;
            }

            .admin-header {
                flex-direction: column;
                text-align: center;
            }

            .admin-header h2 {
                font-size: 18px;
            }

            .btn-primary {
                padding: 8px 16px;
                font-size: 12px;
                width: 100%;
                text-align: center;
            }

            .admin-panel h3 {
                font-size: 16px;
                margin: 15px 0 10px;
            }

            /* Tabel Mobile - Horizontal Scroll */
            .admin-panel {
                overflow-x: auto;
            }

            .user-table {
                min-width: 500px;
            }

            .user-table th,
            .user-table td {
                padding: 8px 10px;
                font-size: 12px;
            }

            .action-buttons {
                flex-direction: column;
                gap: 5px;
            }

            .edit-btn, .delete-btn {
                text-align: center;
            }

            /* Menu Grid Mobile */
            .menu-grid {
                grid-template-columns: 1fr;
                gap: 12px;
            }

            .menu-card {
                padding: 20px;
                display: flex;
                align-items: center;
                text-align: left;
                gap: 15px;
            }

            .menu-card .icon {
                font-size: 32px;
                margin-bottom: 0;
            }

            .menu-card h3 {
                font-size: 14px;
                margin-bottom: 5px;
            }

            .menu-card p {
                font-size: 11px;
            }

            /* Info tambahan mobile */
            .info-tambahan {
                font-size: 12px;
                padding: 15px !important;
            }

            footer {
                font-size: 10px;
                margin-top: 30px;
                padding: 15px;
            }
        }

        /* ========== TABLET VIEW (768px - 1024px) ========== */
        @media (min-width: 769px) and (max-width: 1024px) {
            .navbar h1 {
                font-size: 14px;
            }

            .welcome-text h2 {
                font-size: 18px;
            }

            .menu-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        /* ========== SMALL MOBILE (max-width: 480px) ========== */
        @media (max-width: 480px) {
            .navbar h1 {
                font-size: 12px;
            }

            .user-info span {
                font-size: 11px;
            }

            .role-badge {
                font-size: 10px;
                padding: 4px 10px;
            }

            .logout-btn {
                padding: 5px 10px;
                font-size: 11px;
            }

            .welcome-text h2 {
                font-size: 14px;
            }

            .clock {
                font-size: 14px;
                min-width: 140px;
            }

            .menu-card {
                padding: 15px;
            }

            .menu-card .icon {
                font-size: 28px;
            }

            .menu-card h3 {
                font-size: 13px;
            }
        }
    </style>
</head>
<body>

<!-- Navbar -->
<div class="navbar">
    <h1>📚 Absensi Digital SMA N 1 Lemahabang</h1>
    <div class="right-section">
        <div class="user-info">
            <span>👋 <?php echo htmlspecialchars($nama); ?></span>
            <span class="role-badge">
                <?php 
                    if ($role == 'admin') echo "👑 Admin";
                    elseif ($role == 'manager') echo "📊 Manager";
                    else echo "👤 Pengguna";
                ?>
            </span>
            <a href="logout.php" class="logout-btn">🚪 Logout</a>
        </div>
    </div>
</div>

<div class="container">
    <!-- Welcome Card dengan Jam di Kanan -->
    <div class="welcome-card">
        <div class="welcome-text">
            <h2>Selamat Datang di Dashboard Absensi Digital</h2>
            <p>SMA Negeri 1 Lemahabang Kabupaten Cirebon</p>
        </div>
        <div class="welcome-clock">
            <div class="clock" id="realTimeClock">
                <div id="time">--:--:--</div>
                <div id="date" class="date">---, -- --- ----</div>
            </div>
        </div>
    </div>

    <?php if ($role == 'admin'): ?>
        
        <!-- ============ TAMPILAN ADMIN ============ -->
        <div class="admin-panel">
            <div class="admin-header">
                <h2>👨‍💼 Panel Admin</h2>
                <a href="addUser.php" class="btn-primary">+ Tambah User Baru</a>
            </div>
            
            <h3>📋 Daftar Seluruh Pengguna</h3>
            <?php
            $query = "SELECT * FROM users ORDER BY id DESC";
            $result = mysqli_query($conn, $query);
            
            if (mysqli_num_rows($result) > 0):
            ?>
            <div style="overflow-x: auto;">
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($user = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['nama']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <span class="role-badge-small role-<?php echo $user['role']; ?>">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </td>
                            <td class="action-buttons">
                                <a href="editUser.php?id=<?php echo $user['id']; ?>" class="edit-btn">Edit</a>
                                <button onclick="deleteUser(<?php echo $user['id']; ?>)" class="delete-btn">Hapus</button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <p>Belum ada pengguna terdaftar.</p>
            <?php endif; ?>
        </div>
        
        <div class="menu-grid">
            <a href="rekapAbsensi.php" class="menu-card">
                <div class="icon">📊</div>
                <div>
                    <h3>Rekap Absensi</h3>
                    <p>Lihat laporan kehadiran</p>
                </div>
            </a>
            <a href="laporan.php" class="menu-card">
                <div class="icon">📈</div>
                <div>
                    <h3>Laporan</h3>
                    <p>Export laporan ke Excel/PDF</p>
                </div>
            </a>
            <a href="pengaturan.php" class="menu-card">
                <div class="icon">⚙️</div>
                <div>
                    <h3>Pengaturan</h3>
                    <p>Konfigurasi sistem</p>
                </div>
            </a>
        </div>
        
    <?php elseif ($role == 'manager'): ?>
        
        <div class="admin-panel">
            <div class="admin-header">
                <h2>📊 Panel Manager</h2>
                <a href="addUser.php" class="btn-primary">+ Tambah User</a>
            </div>
            
            <h3>📋 Daftar User (Non-Admin)</h3>
            <?php
            $query = "SELECT * FROM users WHERE role != 'admin' ORDER BY id DESC";
            $result = mysqli_query($conn, $query);
            
            if (mysqli_num_rows($result) > 0):
            ?>
            <div style="overflow-x: auto;">
                <table class="user-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Role</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($user = mysqli_fetch_assoc($result)): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['nama']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td>
                                <span class="role-badge-small role-<?php echo $user['role']; ?>">
                                    <?php echo ucfirst($user['role']); ?>
                                </span>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
                <p>Belum ada user terdaftar selain admin.</p>
            <?php endif; ?>
        </div>
        
        <div class="menu-grid">
            <a href="rekapAbsensi.php" class="menu-card">
                <div class="icon">📊</div>
                <div>
                    <h3>Rekap Absensi</h3>
                    <p>Lihat laporan kehadiran</p>
                </div>
            </a>
            <a href="laporan.php" class="menu-card">
                <div class="icon">📈</div>
                <div>
                    <h3>Laporan</h3>
                    <p>Export laporan ke Excel/PDF</p>
                </div>
            </a>
        </div>
        
    <?php else: ?>
        
        <div class="menu-grid">
            <a href="absen_process.php" class="menu-card">
                <div class="icon">📝</div>
                <div>
                    <h3>Absensi</h3>
                    <p>Lakukan absensi masuk/pulang</p>
                </div>
            </a>
            <a href="riwayatAbsensi.php" class="menu-card">
                <div class="icon">📜</div>
                <div>
                    <h3>Riwayat Absensi</h3>
                    <p>Lihat riwayat kehadiran Anda</p>
                </div>
            </a>
            <a href="profil.php" class="menu-card">
                <div class="icon">👤</div>
                <div>
                    <h3>Profil Saya</h3>
                    <p>Update data diri</p>
                </div>
            </a>
        </div>
        
        <div class="info-tambahan" style="background: #e3f2fd; padding: 20px; border-radius: 10px; margin-top: 30px; text-align: center;">
            <p style="color: #1976d2;">✅ Anda login sebagai <strong>Pengguna</strong>. Anda hanya bisa melakukan absensi dan melihat riwayat sendiri.</p>
        </div>
        
    <?php endif; ?>
</div>

<footer>
    <p>&copy; <?php echo date('Y'); ?> Absensi Digital SMA Negeri 1 Lemahabang | Bismillah Gaada Error</p>
</footer>

<script>
function updateClock() {
    const now = new Date();
    
    let hours = now.getHours().toString().padStart(2, '0');
    let minutes = now.getMinutes().toString().padStart(2, '0');
    let seconds = now.getSeconds().toString().padStart(2, '0');
    const timeString = `${hours}:${minutes}:${seconds}`;
    
    const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
    const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    
    const dayName = days[now.getDay()];
    const day = now.getDate();
    const month = months[now.getMonth()];
    const year = now.getFullYear();
    
    const dateString = `${dayName}, ${day} ${month} ${year}`;
    
    document.getElementById('time').textContent = timeString;
    document.getElementById('date').textContent = dateString;
}

setInterval(updateClock, 1000);
updateClock();

function deleteUser(id) {
    if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
        window.location.href = 'deleteUser.php?id=' + id;
    }
}
</script>

</body>
</html>