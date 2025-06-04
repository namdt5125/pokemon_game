<?php
include_once(__DIR__ . "/php_api/GameConfig.php");
include_once(__DIR__ . "/php_api/Pokemon.php");
include_once(__DIR__ . "/php_api/Trainer.php");

session_start();

if (!isset($_SESSION["username"])) {
    header('Content-Type: application/json');
    echo json_encode(["success" => false, "message" => "Bạn chưa đăng nhập."]);
    exit;
}

if (isset($_GET["action"])) {
    if ($_GET["action"] == "save") {
        if (!isset($_SESSION["trainer"]) || !($_SESSION['trainer'] instanceof Trainer) ) {
            $message = urlencode("Chưa có dữ liệu game để lưu.");
            header("Location: player_profile.php?save_status=error&message={$message}");
            exit;
        }

        if (!isset($_SESSION['trainer']->pokemon) || !($_SESSION['trainer']->pokemon instanceof Pokemon)) {
            $message = urlencode("Dữ liệu Pokémon của Trainer bị lỗi, không thể lưu.");
            header("Location: player_profile.php?save_status=error&message={$message}");
            exit;
        }
                
        $trainer_data = serialize($_SESSION["trainer"]);
        $filename = "pokemon_save_" . preg_replace('/[^A-Za-z0-9_\-]/', '_', $_SESSION['username']) . "_" . date('Ymd_His') . ".sav";

        header('Content-Type: application/octet-stream');
        header("Content-Disposition: attachment; filename=\"{$filename}\"");
        header("Content-Length: " . strlen($trainer_data));
        echo $trainer_data;
        exit;

    } else {
        $message = urlencode("Hành động không hợp lệ cho save-load.php.");
        header("Location: player_profile.php?save_status=error&message={$message}");
        exit;
    }
} else {
    $message = urlencode("Không có hành động nào được chỉ định cho save-load.php.");
    header("Location: player_profile.php?save_status=error&message={$message}");
    exit;
}
?>