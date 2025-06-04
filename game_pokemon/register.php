<?php
session_start();

$users_file = 'users.json';
$users = [];

if (file_exists($users_file)) {
    $users = json_decode(file_get_contents($users_file), true);
    if ($users === null) $users = []; 
}

$registration_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($username) || empty($password) || empty($confirm_password)) {
        $registration_message = "Vui lòng điền đầy đủ thông tin.";
    } elseif ($password !== $confirm_password) {
        $registration_message = "Mật khẩu xác nhận không khớp.";
    } else {
        $user_exists = false;
        foreach ($users as $user) {
            if ($user['username'] === $username) {
                $user_exists = true;
                break;
            }
        }

        if ($user_exists) {
            $registration_message = "Tên đăng nhập đã tồn tại.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $users[] = ['username' => $username, 'password' => $hashed_password];
            if (file_put_contents($users_file, json_encode($users, JSON_PRETTY_PRINT))) {
                $registration_message = "Đăng ký thành công! Bạn có thể đăng nhập ngay bây giờ.";
            } else {
                $registration_message = "Lỗi khi lưu thông tin. Vui lòng thử lại.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký - Pokemon Game</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #f0f0f0; margin: 0; }
        .container { background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 300px; }
        h2 { text-align: center; color: #333; }
        label { display: block; margin-bottom: 5px; color: #555; }
        input[type="text"], input[type="password"] { width: calc(100% - 20px); padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; }
        button { width: 100%; padding: 10px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #0056b3; }
        .message { margin-bottom: 15px; padding: 10px; border-radius: 4px; text-align: center; }
        .message.error { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .message.success { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .link { text-align: center; margin-top: 15px; }
        .link a { color: #007bff; text-decoration: none; }
        .link a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Đăng Ký Tài Khoản</h2>
        <?php if (!empty($registration_message)): ?>
            <div class="message <?php echo (strpos($registration_message, 'thành công') !== false) ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($registration_message); ?>
            </div>
        <?php endif; ?>
        <form action="register.php" method="post">
            <div>
                <label for="username">Tên đăng nhập:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div>
                <label for="password">Mật khẩu:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div>
                <label for="confirm_password">Xác nhận mật khẩu:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit">Đăng Ký</button>
        </form>
        <div class="link">
            Đã có tài khoản? <a href="login.php">Đăng nhập tại đây</a>
        </div>
    </div>
</body>
</html>