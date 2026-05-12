<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Portfolio</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Outfit', sans-serif; background-color: #f8fafc; display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0; color: #0f172a; padding: 20px; box-sizing: border-box; }
        .login-card { background: white; padding: 40px; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); width: 100%; max-width: 400px; text-align: center; box-sizing: border-box; }
        @media (max-width: 480px) {
            .login-card { padding: 25px; }
        }
        .login-card h2 { margin-top: 0; color: #4f46e5; margin-bottom: 24px; }
        .form-group { margin-bottom: 20px; text-align: left; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 600; color: #475569; }
        .form-control { width: 100%; padding: 12px 16px; border: 2px solid #cbd5e1; border-radius: 8px; font-size: 1rem; box-sizing: border-box; transition: border-color 0.3s; }
        .form-control:focus { outline: none; border-color: #4f46e5; }
        .btn { width: 100%; padding: 14px; background-color: #4f46e5; color: white; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 600; cursor: pointer; transition: background-color 0.3s; }
        .btn:hover { background-color: #4338ca; }
        .error { color: #ef4444; margin-bottom: 20px; font-size: 0.9rem; font-weight: 600; }
    </style>
</head>
<body>
    <div class="login-card">
        <h2>Local Portfolio Admin</h2>
        <?php if (isset($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="password">Admin Password</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Enter password" required autofocus>
            </div>
            <button type="submit" class="btn">Login</button>
        </form>
    </div>
</body>
</html>
