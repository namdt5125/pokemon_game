<?php
session_start();
if (isset($_SESSION['username'])) {
    if (isset($_SESSION['trainer']) && $_SESSION['trainer'] instanceof Trainer) {

        include_once(__DIR__ . "/php_api/Trainer.php");
        include_once(__DIR__ . "/php_api/Pokemon.php");
        include_once(__DIR__ . "/php_api/game_config.php");

        if ($_SESSION['trainer'] instanceof Trainer) { 
             header("Location: player_profile.php");
             exit;
        } else {
            unset($_SESSION['trainer']); 
            header("Location: choose_starter.php");
            exit;
        }
    } else {
        header("Location: choose_starter.php");
        exit;
    }
}


$users_file = 'users.json';
$login_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $login_message = "Vui lòng điền đầy đủ tên đăng nhập và mật khẩu.";
    } else {
        if (file_exists($users_file)) {
            $users = json_decode(file_get_contents($users_file), true);
            if ($users === null) $users = [];

            $user_found = null;
            foreach ($users as $user) {
                if ($user['username'] === $username) {
                    $user_found = $user;
                    break;
                }
            }

            if ($user_found && password_verify($password, $user_found['password'])) {
                $_SESSION['username'] = $user_found['username'];

                unset($_SESSION['trainer']);
                unset($_SESSION['enemy']);
                unset($_SESSION['current_map']);

                header("Location: choose_starter.php");
                exit;
            } else {
                $login_message = "Tên đăng nhập hoặc mật khẩu không đúng.";
            }
        } else {
            $login_message = "Chưa có người dùng nào được đăng ký.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập - Pokemon Game</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #f0f0f0; margin: 0; }
        .container { background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); width: 300px; }
        h2 { text-align: center; color: #333; }
        label { display: block; margin-bottom: 5px; color: #555; }
        input[type="text"], input[type="password"] { width: calc(100% - 20px); padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 4px; }
        button { width: 100%; padding: 10px; background-color: #28a745; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; }
        button:hover { background-color: #218838; }
        .message { margin-bottom: 15px; padding: 10px; border-radius: 4px; text-align: center; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;}
        .link { text-align: center; margin-top: 15px; }
        .link a { color: #007bff; text-decoration: none; }
        .link a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Đăng Nhập</h2>
        <?php if (!empty($login_message)): ?>
            <div class="message error"><?php echo htmlspecialchars($login_message); ?></div>
        <?php endif; ?>
        <form action="login.php" method="post">
            <div>
                <label for="username">Tên đăng nhập:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div>
                <label for="password">Mật khẩu:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit">Đăng Nhập</button>
        </form>
        <div class="link">
            Chưa có tài khoản? <a href="register.php">Đăng ký tại đây</a>
        </div>
    </div>
</body>
</html>