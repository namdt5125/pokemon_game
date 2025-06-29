<?php


if (!isset($_SESSION["trainer"]) || !($_SESSION["trainer"] instanceof Trainer)) {
    echo json_encode(["error" => "Người huấn luyện chưa được khởi tạo đúng cách."]);
    http_response_code(403);
    die();
}

if (!isset($_SESSION["trainer"]->pokemon) || !($_SESSION["trainer"]->pokemon instanceof Pokemon)) {
    echo json_encode(["error" => "Không tìm thấy Pokémon của người huấn luyện."]);
    http_response_code(500);
    die();
}

if (isset($action) && $action == "boss_drainer") { 

    if ($_SESSION["trainer"]->pokemon->health <= 0) {
    }
    
    $boss_name = "Kael'thas Kẻ Hút Năng Lượng";
    $boss_type = "void_sorcerer";            
    $boss_initial_health = rand(300, 600);
    $boss_initial_damage = rand(150, 200);

    $boss_pokemon = new Pokemon($boss_name, $boss_type, $boss_initial_health, $boss_initial_damage);
    $_SESSION["enemy"] = $boss_pokemon;

    $trainer_pokemon = $_SESSION["trainer"]->pokemon;
    
    $original_health_before_drain = $trainer_pokemon->health;
    $original_damage_before_drain = $trainer_pokemon->damage;

    $trainer_pokemon->health = 5;
    $trainer_pokemon->damage = 5;

    $stat_drain_message = "Hào quang của {$boss_name} hút cạn sinh lực Pokémon của bạn! Máu và Sát thương bị giảm xuống còn 5 cho trận đấu này.";

    $fight_log = $_SESSION["trainer"]->fight($_SESSION["enemy"]); 

    $battle_outcome_message = "";
    if ($_SESSION["trainer"]->pokemon->health <= 0) {
        $battle_outcome_message = "Pokémon của bạn đã bị " . $boss_pokemon->name . " đánh bại sau khi bị suy yếu nặng nề.";
    } elseif (isset($_SESSION["enemy"]->health) && $_SESSION["enemy"]->health <= 0) {
        $battle_outcome_message = "Thật phi thường! Dù bị suy yếu, bạn đã kiên cường chiến đấu và hạ gục " . $boss_pokemon->name . "!";
    } else {
        $battle_outcome_message = "Trận chiến dữ dội với " . $boss_pokemon->name . " đã kết thúc.";
    }
    
    $response = [
        "message" => "Trận chiến định mệnh với Boss đặc biệt " . $boss_pokemon->name . " bắt đầu!",
        "special_skill_effect" => $stat_drain_message,
        "trainer_pokemon_stats_before_drain" => [
            "health" => $original_health_before_drain,
            "damage" => $original_damage_before_drain
        ],
        "trainer_pokemon_stats_after_drain_before_fight" => [ 
            "health" => 5,
            "damage" => 5
        ],
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
    echo json_encode(["error" => "Hành động '{$action}' không được nhận diện hoặc không được chỉ định cho map4."]);
    http_response_code(400);
    die();
}
?>