<?php

include_once(__DIR__ . "/php_api/GameConfig.php");
include_once(__DIR__ . "/php_api/Pokemon.php");
include_once(__DIR__ . "/php_api/Trainer.php");

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

$trainer = null; 
$load_message = ''; 
$action_message = ''; 

if (isset($_GET['action']) && $_GET['action'] === 'reset_game') {
    unset($_SESSION['trainer']);
    unset($_SESSION['enemy']);
    unset($_SESSION['current_map']);
    error_log("Player_profile: User " . $_SESSION['username'] . " chose to reset game. Redirecting to choose_starter.php");
    header("Location: choose_starter.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["load_game_file"])) {
    if (isset($_FILES["load_game_file"]["error"]) && $_FILES["load_game_file"]["error"] == UPLOAD_ERR_OK) {
        $tmp_name = $_FILES["load_game_file"]["tmp_name"];
        $data = file_get_contents($tmp_name);

        $loaded_trainer = @unserialize($data);

        if ($loaded_trainer instanceof Trainer) {
            $_SESSION['trainer'] = $loaded_trainer;
            $_SESSION['current_map'] = (isset($loaded_trainer->current_map) && !empty($loaded_trainer->current_map)) ? $loaded_trainer->current_map : 'map1';
            unset($_SESSION['enemy']); 
            $load_message = "Tải game thành công!";
            error_log("User " . $_SESSION['username'] . " loaded game successfully. Current map set to: " . $_SESSION['current_map']);
        } else {
            $unserialize_error = error_get_last();
            $error_details = $unserialize_error ? " (PHP Error: " . $unserialize_error['message'] . ")" : "";
            error_log("Unserialize failed for uploaded save file. User: " . $_SESSION['username'] . $error_details . ". Data (first 200 chars): " . substr(bin2hex($data), 0, 400)); // Log data dạng hex
            if (empty($data)) {
                $load_message = "Lỗi: File save trống.";
            } elseif (strlen($data) < 50) { 
                $load_message = "Lỗi: File save có vẻ không hợp lệ (quá ngắn).";
            } else {
                $load_message = "Lỗi: File save không hợp lệ hoặc bị hỏng. Không thể khôi phục Trainer.";
            }
        }
    } elseif (isset($_FILES["load_game_file"]["error"]) && $_FILES["load_game_file"]["error"] != UPLOAD_ERR_NO_FILE) {
        $load_message = "Lỗi khi tải file lên. Mã lỗi: " . $_FILES["load_game_file"]["error"];
    }
}


if (isset($_SESSION['trainer']) && $_SESSION['trainer'] instanceof Trainer) {
    $trainer = $_SESSION['trainer'];
} else {
    error_log("Player_profile (final check): Trainer not found or invalid for user " . ($_SESSION['username'] ?? 'Unknown') . ". Redirecting to choose_starter.php. Current trainer type: " . (isset($_SESSION['trainer']) && is_object($_SESSION['trainer']) ? get_class($_SESSION['trainer']) : gettype($_SESSION['trainer'] ?? null)));
    header("Location: choose_starter.php");
    exit;
}


if (isset($_GET['save_status'])) {
    if ($_GET['save_status'] === 'success') {
        $action_message = "Game đã được lưu thành công!";
    } elseif ($_GET['save_status'] === 'error') {
        $action_message = "Lỗi: Không thể lưu game. " . htmlspecialchars($_GET['message'] ?? '');
    }
}

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thông Tin Người Chơi - Pokemon Game</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; color: #333; margin: 0; padding: 20px; }
        .container { max-width: 800px; margin: 20px auto; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h1, h2 { color: #007bff; }
        h1 { text-align: center; }
        .info-section { margin-bottom: 20px; padding: 15px; border: 1px solid #ddd; border-radius: 4px; background-color: #f9f9f9;}
        .info-section h2 { margin-top: 0; }
        p { line-height: 1.6; }
        strong { color: #555; }
        .actions a, .actions button, .actions form button { 
            display: inline-block;
            margin: 5px;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            border: 1px solid transparent; 
        }
        .actions .play-button { background-color: #28a745; color: white; border-color: #28a745; }
        .actions .play-button:hover { background-color: #218838; }

        .actions .save-button { background-color: #ffc107; color: #212529; border-color: #ffc107;}
        .actions .save-button:hover { background-color: #e0a800; }

        .actions form button.load-submit-button {
            background-color: #007bff; color:white; border-color:#007bff;
        }
        .actions form button.load-submit-button:hover {
            background-color: #0056b3;
        }


        .actions .logout-button { background-color: #dc3545; color: white; border-color: #dc3545;}
        .actions .logout-button:hover { background-color: #c82333; }
        .actions .newgame-button { background-color: #17a2b8; color: white; border-color: #17a2b8;}
        .actions .newgame-button:hover { background-color: #138496; }

        .form-group { margin-bottom: 15px; display: inline-block; }
        .form-group label { display: inline-block; margin-right: 5px; font-weight:normal; }
        .form-group input[type="file"] { padding: 8px; border: 1px solid #ccc; border-radius: 4px; font-size:14px; }

        .message { padding: 10px; margin-bottom: 15px; border-radius: 4px; text-align: center; }
        .message.success { background-color: #d4edda; color: #155724; }
        .message.error { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="container">
        <h1>Chào mừng, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>

        <?php if (!empty($load_message)):  ?>
            <div class="message <?php echo (strpos($load_message, 'thành công') !== false) ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($load_message); ?>
            </div>
        <?php elseif (!empty($action_message)): ?>
            <div class="message <?php echo (strpos($action_message, 'thành công') !== false) ? 'success' : 'error'; ?>">
                <?php echo htmlspecialchars($action_message); ?>
            </div>
        <?php endif; ?>

        <div class="info-section">
            <h2>Thông Tin Trainer và Pokémon</h2>
            <?php 
            if ($trainer && $trainer instanceof Trainer): ?>
                <p><strong>Tên Trainer:</strong> <?php echo htmlspecialchars($trainer->name); ?></p>
                <?php if (isset($trainer->pokemon) && $trainer->pokemon instanceof Pokemon): ?>
                    <h3>Thông Tin Pokémon</h3>
                    <p><strong>Tên Pokémon:</strong> <?php echo htmlspecialchars($trainer->pokemon->name); ?></p>
                    <p><strong>Loại:</strong> <?php echo htmlspecialchars($trainer->pokemon->type); ?></p>
                    <p><strong>Máu (HP):</strong> <?php echo htmlspecialchars($trainer->pokemon->health); ?></p>
                    <p><strong>Sát thương (Damage):</strong> <?php echo htmlspecialchars($trainer->pokemon->damage); ?></p>
                <?php else: ?>
                    <p style="color:red;">Dữ liệu Pokémon của Trainer không hợp lệ! Hãy thử <a href="player_profile.php?action=reset_game">Chơi Mới Hoàn Toàn</a>.</p>
                <?php endif; ?>
            <?php else: ?>
                 <p style="color:red;">Lỗi: Không thể tải thông tin Trainer. Vui lòng <a href="choose_starter.php">chọn lại Pokémon khởi đầu</a> hoặc liên hệ quản trị viên.</p>
            <?php endif; ?>
        </div>

        <div class="info-section actions">
            <h2>Hành Động</h2>
            <?php if ($trainer && $trainer instanceof Trainer && isset($trainer->pokemon) && $trainer->pokemon instanceof Pokemon): ?>
                <a href="index.html" class="play-button">Chơi Game (Map: <?php echo htmlspecialchars($_SESSION['current_map'] ?? 'map1'); ?>)</a>
                <a href="save-load.php?action=save" class="save-button">Lưu Game Hiện Tại</a>
            <?php endif; ?>

            <form action="player_profile.php" method="post" enctype="multipart/form-data" style="margin-top: 10px;">
                <div class="form-group">
                     <label for="load_game_file">Tải Game Đã Lưu (.sav):</label>
                    <input type="file" name="load_game_file" id="load_game_file" accept=".sav">
                    <button type="submit" class="load-submit-button">Tải Lên</button>
                </div>
            </form>
            <br>
            <a href="player_profile.php?action=reset_game" class="newgame-button" style="margin-top:5px;">Chơi Mới Hoàn Toàn</a>
            <a href="logout.php" class.logout-button">Đăng Xuất</a>
        </div>
    </div>
</body>
</html>