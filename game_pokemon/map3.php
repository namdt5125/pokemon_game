<?php

if (!isset($_SESSION["trainer"]) || !($_SESSION["trainer"] instanceof Trainer)) {
    echo json_encode(["error" => "Người huấn luyện chưa được khởi tạo đúng cách. Vui lòng bắt đầu trò chơi mới."]);
    http_response_code(403);
    die();
}

if (!isset($_SESSION["trainer"]->pokemon) || !($_SESSION["trainer"]->pokemon instanceof Pokemon)) {
    echo json_encode(["error" => "Không tìm thấy Pokémon của người huấn luyện."]);
    http_response_code(500);
    die();
}

if ($_SESSION["trainer"]->pokemon->health <= 0) {
    echo json_encode(["error" => "Pokémon của bạn đã kiệt sức. Không thể chiến đấu với Boss."]);
    http_response_code(400);
    die();
}

if (isset($action) && $action == "boss_krakon") { 
    
    $boss_name = "Krakon Chúa Tể Vực Thẳm";
    $boss_type = "abyssal_leviathan";
    $boss_initial_health = rand(500000, 1000000);   
    $boss_initial_damage = rand(100000, 500000);     

    $boss_pokemon = new Pokemon($boss_name, $boss_type, $boss_initial_health, $boss_initial_damage);
    $_SESSION["enemy"] = $boss_pokemon;

    $fight_log = $_SESSION["trainer"]->fight($_SESSION["enemy"]); 

    $battle_outcome_message = "";
    if ($_SESSION["trainer"]->pokemon->health <= 0) {
        $battle_outcome_message = "Pokémon của bạn đã bị " . $boss_pokemon->name . " đánh bại.";
    } elseif (isset($_SESSION["enemy"]->health) && $_SESSION["enemy"]->health <= 0) {
        $battle_outcome_message = "Bạn đã xuất sắc đánh bại " . $boss_pokemon->name . "!";
        $_SESSION['map3_boss_krakon_defeated'] = true; 
    } else {
        $battle_outcome_message = "Trận chiến với " . $boss_pokemon->name . " đã kết thúc mà không rõ người chiến thắng.";
    }
    
    $response = [
        "message" => "Trận chiến với Boss Map 3: " . $boss_pokemon->name . " đã diễn ra.",
        "boss_details" => [
            "name" => $boss_pokemon->name,
            "type" => $boss_pokemon->type,
            "initial_health" => $boss_initial_health,
            "initial_damage" => $boss_initial_damage
        ],
        "fight_log" => $fight_log,
        "trainer_pokemon_final_status" => $_SESSION["trainer"]->pokemon,
        "boss_final_status" => $_SESSION["enemy"],
        "battle_outcome_message" => $battle_outcome_message
    ];

    echo json_encode($response);
    die();

} else {
    echo json_encode(["error" => "Hành động '{$action}' không được nhận diện hoặc không được chỉ định cho map3."]);
    http_response_code(400);
    die();
}
?>