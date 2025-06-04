<?php
include_once(__DIR__ . "/php_api/game_config.php");
include_once(__DIR__ . "/php_api/Pokemon.php");
include_once(__DIR__ . "/php_api/Trainer.php");

session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
$starter_pokemon_options = [
    "pikachu" => "Pikachu",
    "bulbasaur" => "Bulbasaur",
    "charmander" => "Charmander",
    "squirtle" => "Squirtle"
];
$starter_images = [ 
    "pikachu" => "pikachu.png",
    "bulbasaur" => "bulbasaur.png",
    "charmander" => "charmander.png",
    "squirtle" => "squirtle.png"
];

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pokemon_nickname = trim($_POST['pokemon_nickname'] ?? '');
    $selected_pokemon_type = $_POST['pokemon_type'] ?? '';

    if (empty($pokemon_nickname)) {
        $error_message = "Vui lòng đặt tên cho Pokémon của bạn.";
    } elseif (empty($selected_pokemon_type) || !array_key_exists($selected_pokemon_type, $starter_pokemon_options)) {
        $error_message = "Vui lòng chọn một Pokémon hợp lệ.";
    } else {
        $trainer_name = $_SESSION['username'];
        $_SESSION['trainer'] = new Trainer($trainer_name, $pokemon_nickname, $selected_pokemon_type);
        
        $_SESSION['current_map'] = 'map1'; 
        unset($_SESSION['enemy']); 

        error_log("Starter chosen: User " . $_SESSION['username'] . " chose " . $selected_pokemon_type . " named " . $pokemon_nickname . ". Redirecting to index.html");
        
        header("Location: index.html");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chọn Pokémon Khởi Đầu - Pokemon Game</title>
    <style>
        body { font-family: Arial, sans-serif; display: flex; justify-content: center; align-items: center; min-height: 100vh; background-color: #f0f0f0; margin: 0; padding: 20px; }
        .container { background-color: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 15px rgba(0,0,0,0.1); width: auto; max-width: 600px; text-align: center; }
        h2 { color: #333; margin-bottom: 25px; }
        label { display: block; margin-bottom: 8px; color: #555; font-weight: bold; }
        input[type="text"] { width: calc(100% - 22px); padding: 10px; margin-bottom: 20px; border: 1px solid #ccc; border-radius: 4px; }
        .starter-options { display: flex; justify-content: space-around; flex-wrap: wrap; margin-bottom: 25px; }
        .starter-option { margin: 10px; padding: 15px; border: 1px solid #ddd; border-radius: 8px; cursor: pointer; transition: transform 0.2s, box-shadow 0.2s; min-width: 120px; }
        .starter-option:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(0,0,0,0.1); }
        .starter-option img { width: 80px; height: 80px; display: block; margin: 0 auto 10px; }
        .starter-option input[type="radio"] { margin-right: 8px; vertical-align: middle; }
        .starter-option span { vertical-align: middle; }
        button { padding: 12px 25px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; transition: background-color 0.2s; }
        button:hover { background-color: #0056b3; }
        .message.error { margin-top: 15px; padding: 10px; border-radius: 4px; text-align: center; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb;}
    </style>
</head>
<body>
    <div class="container">
        <h2>Chào mừng, <?php echo htmlspecialchars($_SESSION['username']); ?>! Hãy chọn Pokémon đồng hành.</h2>
        
        <?php if (!empty($error_message)): ?>
            <div class="message error"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>

        <form action="choose_starter.php" method="post">
            <div>
                <label for="pokemon_nickname">Đặt tên cho Pokémon của bạn:</label>
                <input type="text" id="pokemon_nickname" name="pokemon_nickname" required value="<?php echo isset($_POST['pokemon_nickname']) ? htmlspecialchars($_POST['pokemon_nickname']) : ''; ?>">
            </div>

            <label>Chọn Pokémon Khởi Đầu:</label>
            <div class="starter-options">
                <?php foreach ($starter_pokemon_options as $type => $name): ?>
                    <label class="starter-option" for="type_<?php echo $type; ?>">
                        <img src="./assets/images/pokemons/<?php echo $starter_images[$type]; ?>" alt="<?php echo $name; ?>">
                        <input type="radio" id="type_<?php echo $type; ?>" name="pokemon_type" value="<?php echo $type; ?>" <?php if(empty($_POST['pokemon_type']) && $type === 'pikachu') echo 'checked'; elseif(isset($_POST['pokemon_type']) && $_POST['pokemon_type'] === $type) echo 'checked';?> required>
                        <span><?php echo $name; ?></span>
                    </label>
                <?php endforeach; ?>
            </div>
            
            <button type="submit">Bắt Đầu Cuộc Phiêu Lưu!</button>
        </form>
         <p style="margin-top: 20px;"><a href="logout.php">Đăng xuất</a></p>
    </div>
</body>
</html>