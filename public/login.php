<?php 
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/security.php';
require_once __DIR__ . '/../src/db.php';
require_once __DIR__ . '/../src/auth.php';

$pageTitle = 'Login - Perpustakaan Digital';
$activePage = 'login';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?= asset('css/styles.css') ?>">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 100px 2rem 4rem;
        }
        
        .login-container {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(20px);
            padding: 3rem;
            border-radius: 24px;
            box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 480px;
            animation: fadeInUp 0.6s ease-out;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 2.5rem;
        }
        
        .login-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #6366F1, #818CF8);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1.5rem;
            box-shadow: 0 10px 30px rgba(99, 102, 241, 0.4);
        }
        
        .login-icon i {
            font-size: 2.5rem;
            color: white;
        }
        
        .login-header h2 {
            color: #0F172A;
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .login-header p {
            color: #64748B;
            font-size: 0.95rem;
        }
        
        .alert {
            padding: 1rem 1.25rem;
            background: #FEE2E2;
            border-left: 4px solid #EF4444;
            border-radius: 12px;
            margin-bottom: 2rem;
            color: #991B1B;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: shake 0.5s ease;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        .alert i {
            font-size: 1.25rem;
        }
        
        .form-group {
            margin-bottom: 1.75rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.75rem;
            font-weight: 600;
            color: #1E293B;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .input-wrapper {
            position: relative;
        }
        
        .input-icon {
            position: absolute;
            left: 1.25rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94A3B8;
            font-size: 1.1rem;
            transition: all 0.3s ease;
        }
        
        .form-group input {
            width: 100%;
            padding: 1.1rem 1.25rem 1.1rem 3.25rem;
            border: 2px solid #E2E8F0;
            border-radius: 12px;
            font-size: 1rem;
            font-family: 'Inter', sans-serif;
            transition: all 0.3s ease;
            background: #F8FAFC;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #6366F1;
            background: white;
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
        }
        
        .form-group input:focus + .input-icon {
            color: #6366F1;
        }
        
        .btn-submit {
            width: 100%;
            padding: 1.25rem;
            background: linear-gradient(135deg, #6366F1, #818CF8);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.05rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 0.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            box-shadow: 0 4px 16px rgba(99, 102, 241, 0.3);
        }
        
        .btn-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(99, 102, 241, 0.4);
        }
        
        .btn-submit:active {
            transform: translateY(0);
        }
        
        .login-footer {
            text-align: center;
            margin-top: 2rem;
            padding-top: 2rem;
            border-top: 1px solid #E2E8F0;
        }
        
        .login-footer a {
            color: #6366F1;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .login-footer a:hover {
            color: #4F46E5;
            gap: 0.75rem;
        }
        
        @media (max-width: 640px) {
            .login-wrapper {
                padding: 80px 1rem 2rem;
            }
            
            .login-container {
                padding: 2rem 1.5rem;
            }
            
            .login-header h2 {
                font-size: 1.75rem;
            }
            
            .login-icon {
                width: 70px;
                height: 70px;
            }
            
            .login-icon i {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <!-- Include Header -->
    <?php include __DIR__ . '/../templates/header.php'; ?>
    
    <div class="login-wrapper">
        <div class="login-container">
            <div class="login-header">
                <div class="login-icon">
                    <i class="fas fa-book-reader"></i>
                </div>
                <h2>Selamat Datang</h2>
                <p>Masuk ke akun admin perpustakaan digital</p>
            </div>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert">
                    <i class="fas fa-exclamation-circle"></i>
                    <span>
                        <?php
                            switch($_GET['error']) {
                                case 'missing_fields': echo 'Semua field wajib diisi!'; break;
                                case 'invalid_token': echo 'Token keamanan tidak valid!'; break;
                                case 'empty_fields': echo 'Username dan password tidak boleh kosong!'; break;
                                case 'invalid_credentials': echo 'Username atau password salah!'; break;
                                case 'system_error': echo 'Terjadi kesalahan sistem!'; break;
                                default: echo 'Login gagal!';
                            }
                        ?>
                    </span>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="../admin/proses_login.php">
                <input type="hidden" name="csrf_token" value="<?= Security::generateCSRFToken() ?>">
                
                <div class="form-group">
                    <label for="username">
                        <i class="fas fa-user"></i> Username
                    </label>
                    <div class="input-wrapper">
                        <input 
                            type="text" 
                            id="username" 
                            name="user" 
                            required 
                            placeholder="Masukkan username Anda"
                            autocomplete="username"
                        >
                        <i class="fas fa-user input-icon"></i>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock"></i> Password
                    </label>
                    <div class="input-wrapper">
                        <input 
                            type="password" 
                            id="password" 
                            name="pwd" 
                            required 
                            placeholder="Masukkan password Anda"
                            autocomplete="current-password"
                        >
                        <i class="fas fa-lock input-icon"></i>
                    </div>
                </div>
                
                <button type="submit" name="login" class="btn-submit">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Masuk</span>
                </button>
            </form>
            
            <div class="login-footer">
                <a href="index.php">
                    <i class="fas fa-arrow-left"></i>
                    <span>Kembali ke Beranda</span>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Include Footer -->
    <?php include __DIR__ . '/../templates/footer.php'; ?>
</body>
</html>


