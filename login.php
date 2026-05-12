<?php
session_start();
include 'connection.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['email']) || empty($_POST['password'])) {
        $error = "Email dan password harus diisi!";
    } else {
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $password = md5($_POST['password']);

        // Ambil semua data termasuk role
        $query = "SELECT * FROM users WHERE email='$email' AND password='$password'";
        $result = mysqli_query($conn, $query);
        
        if (!$result) {
            $error = "Error database: " . mysqli_error($conn);
        } else {
            $data = mysqli_fetch_assoc($result);
            
            if ($data) {
                $_SESSION['user_id'] = $data['id'];
                $_SESSION['nama'] = $data['nama'];
                $_SESSION['role'] = $data['role']; // <==== INI YANG PENTING!
                
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Login gagal! Email atau password salah.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login | Absensi Digital</title>
    <link rel="icon" href="Assets/smansalaLogo.png">
    <style>
        .error-message {
            color: #d32f2f;
            margin-bottom: 15px;
            padding: 10px;
            background: #ffebee;
            border-radius: 5px;
            border-left: 4px solid #d32f2f;
            text-align: left;
            font-size: 14px;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            padding: 20px;
        }

        .login-box {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            width: 350px;
            text-align: center;
        }

        .login-box h2 {
            margin-bottom: 30px;
            color: #333;
        }

        .login-box input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
        }

        .login-box input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 5px rgba(102,126,234,0.3);
        }

        .login-box button {
            width: 100%;
            padding: 12px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 10px;
        }

        .login-box button:hover {
            background: #5a67d8;
        }

        /* ========== MOBILE VIEW (max-width: 768px) ========== */
        @media (max-width: 768px) {
            body {
                padding: 15px;
                height: auto;
                min-height: 100vh;
            }
            
            .login-box {
                width: 100%;
                max-width: 350px;
                padding: 30px 25px;
                margin: 0 auto;
            }
            
            .login-box h2 {
                margin-bottom: 25px;
                font-size: 24px;
            }
            
            .login-box input {
                padding: 10px;
                font-size: 14px;
                margin: 8px 0;
            }
            
            .login-box button {
                padding: 10px;
                font-size: 15px;
                margin-top: 10px;
            }
            
            .error-message {
                font-size: 12px;
                padding: 8px;
                margin-bottom: 12px;
            }
        }

        /* ========== SMALL MOBILE (max-width: 480px) ========== */
        @media (max-width: 480px) {
            .login-box {
                padding: 25px 20px;
            }
            
            .login-box h2 {
                font-size: 20px;
                margin-bottom: 20px;
            }
            
            .login-box input {
                padding: 10px;
                font-size: 13px;
            }
            
            .login-box button {
                padding: 10px;
                font-size: 14px;
            }
            
            .error-message {
                font-size: 11px;
                padding: 8px;
            }
        }

        /* ========== LARGE PHONE (375px ke bawah) ========== */
        @media (max-width: 375px) {
            .login-box {
                padding: 20px 15px;
            }
            
            .login-box h2 {
                font-size: 18px;
                margin-bottom: 18px;
            }
            
            .login-box input {
                padding: 8px;
                font-size: 12px;
                margin: 6px 0;
            }
            
            .login-box button {
                padding: 8px;
                font-size: 13px;
            }
        }

        /* ========== TABLET VIEW (768px - 1024px) ========== */
        @media (min-width: 769px) and (max-width: 1024px) {
            .login-box {
                width: 380px;
                padding: 45px;
            }
            
            .login-box h2 {
                font-size: 26px;
            }
        }
    </style>
</head>
<body>

<div class="login-box">
    <h2>Login Absensi</h2>
    
    <?php if ($error): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    
    <form action="" method="POST">
        <input type="email" name="email" placeholder="Email" required autofocus>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>
</div>

</body>
</html>