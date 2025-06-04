<?php
include_once(__DIR__ . "/game_config.php");
include_once(__DIR__ . "/Pokemon.php");
include_once(__DIR__ . "/Trainer.php");

session_start();

header('Content-Type: application/json');

error_log("game_actions.php accessed. Session ID: " . session_id() . ", Action: " . ($_REQUEST['action'] ?? 'N/A'));
if(isset($_SESSION['trainer'])){
    error_log("game_actions.php (entry) - _SESSION['trainer'] type: " . (is_object($_SESSION['trainer']) ? get_class($_SESSION['trainer']) : gettype($_SESSION['trainer'])));
} else {
    error_log("game_actions.php (entry) - _SESSION['trainer'] is NOT set.");
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';
$response = ['success' => false, 'message' => 'Hành động không hợp lệ.']; 
$trainer = $_SESSION['trainer'] ?? null;
$enemy = $_SESSION['enemy'] ?? null;
$current_map_from_session = $_SESSION['current_map'] ?? 'map1';

if ($action === 'get_trainer_info') {
    if ($trainer && $trainer instanceof Trainer && isset($trainer->pokemon) && $trainer->pokemon instanceof Pokemon) {
        $response = ['success' => true, 'trainer' => $trainer];
    } else {
        $log_message = "get_trainer_info: Failed. ";
        if (!$trainer) $log_message .= "Trainer not set in session.";
        elseif (!($trainer instanceof Trainer)) $log_message .= "Trainer in session is not a valid Trainer instance. Actual type: " . (is_object($trainer) ? get_class($trainer) : gettype($trainer));
        elseif (!$trainer->pokemon) $log_message .= "Trainer's pokemon property is not set.";
        else $log_message .= "Trainer's pokemon is not a valid Pokemon instance. Actual type: " . (is_object($trainer->pokemon) ? get_class($trainer->pokemon) : gettype($trainer->pokemon));
        $log_message .= " Username: " . ($_SESSION['username'] ?? 'N/A');
        error_log($log_message);
        $response = ['success' => false, 'message' => 'Không tìm thấy thông tin Trainer hợp lệ. Hãy thử chọn lại Pokémon khởi đầu.'];
    }
    echo json_encode($response);
    exit;
}
elseif ($action === 'get_current_battle_data') {
    $valid_trainer_pokemon = ($trainer && $trainer instanceof Trainer && isset($trainer->pokemon) && $trainer->pokemon instanceof Pokemon);
    $valid_enemy = ($enemy && $enemy instanceof Pokemon);

    if ($valid_trainer_pokemon && $valid_enemy) {
        $response = [
            'success' => true,
            'player_pokemon' => $trainer->pokemon,
            'enemy_pokemon' => $enemy,
            'message' => "Dữ liệu trận đấu đã sẵn sàng!",
            'trainer_name' => $trainer->name,
            'current_map' => $current_map_from_session
        ];
    } else {
        $error_msg_detail = "Không thể tải dữ liệu trận đấu. ";
        if (!$valid_trainer_pokemon) $error_msg_detail .= "Trainer hoặc Pokémon của Trainer không hợp lệ. ";
        if (!$valid_enemy) $error_msg_detail .= "Đối thủ (enemy) không hợp lệ. ";
        error_log("get_current_battle_data: " . $error_msg_detail . "User: " . ($_SESSION['username'] ?? 'N/A'));
        $response = ['success' => false, 'message' => trim($error_msg_detail)];
    }
    echo json_encode($response);
    exit;
}
elseif ($action === 'update_current_map') {
    $map_name_to_update = $_POST['map_name'] ?? null;
    if ($map_name_to_update) {
        $_SESSION['current_map'] = $map_name_to_update;
        error_log("Updated current_map in session to: " . $map_name_to_update . " for user: " . ($_SESSION['username'] ?? 'N/A'));
        $response = ['success' => true, 'message' => "Current map updated to {$map_name_to_update}."];
    } else {
        $response = ['success' => false, 'message' => 'Map name not provided for update.'];
    }
    echo json_encode($response);
    exit;
}

if (in_array($action, ['trigger_wild_encounter', 'trigger_boss_battle', 'fight_action', 'run_action'])) {
    if (!$trainer) { error_log("Battle Action ($action): Trainer not found. User: " . ($_SESSION['username'] ?? 'N/A')); echo json_encode(['success' => false, 'message' => 'Trainer not found.']); exit; }
    if (!($trainer instanceof Trainer)) { error_log("Battle Action ($action): Trainer invalid. User: " . ($_SESSION['username'] ?? 'N/A') . " Type: " . (is_object($trainer) ? get_class($trainer) : gettype($trainer))); echo json_encode(['success' => false, 'message' => 'Trainer data corrupted.']); exit; }
    if (!isset($trainer->pokemon) || !($trainer->pokemon instanceof Pokemon)){ error_log("Battle Action ($action): Trainer's Pokemon invalid. User: " . ($_SESSION['username'] ?? 'N/A') . " Pokemon: " . print_r($trainer->pokemon, true) ); echo json_encode(['success' => false, 'message' => "Trainer's Pokemon data corrupted."]); exit; }
}


if ($action === 'trigger_wild_encounter') {
    $map_level_context = $current_map_from_session;
    if ($trainer->pokemon->health <= 0) { $response = ['success' => false, 'message' => 'Pokémon của bạn đã kiệt sức!']; }
    else {
        $enemy_name = "Wild Pokemon ({$map_level_context})";
        $pokemon_types = ["bulbasaur", "charmander", "pikachu", "squirtle"];
        $enemy_type = $pokemon_types[array_rand($pokemon_types)];
        $enemy_health = rand(intval(GlobalConfig::BASE_HEALTH_MIN * 0.3), intval(GlobalConfig::BASE_HEALTH_MAX * 0.5));
        $enemy_damage = rand(intval(GlobalConfig::BASE_DMG_MIN * 0.3), intval(GlobalConfig::BASE_DMG_MAX * 0.5));
        $_SESSION['enemy'] = new Pokemon($enemy_name, $enemy_type, $enemy_health, $enemy_damage);
        $response = [
            'success' => true, 'type' => 'wild_encounter',
            'message' => "Một {$enemy_name} ({$enemy_type}) xuất hiện!",
            'player_pokemon' => $trainer->pokemon, 'enemy_pokemon' => $_SESSION['enemy']
        ];
    }
}
elseif ($action === 'trigger_boss_battle') {
    $boss_id = $_POST['boss_id'] ?? '';
    if ($trainer->pokemon->health <= 0) { $response = ['success' => false, 'message' => 'Pokémon của bạn đã kiệt sức! Không thể chiến đấu với boss.']; }
    elseif (empty($boss_id)) { $response = ['success' => false, 'message' => 'Boss ID not provided.']; }
    else {
        $boss_name_display = "Unknown Boss"; $boss_name_internal = "UnknownBossInternal";
        $boss_type = "unknown_boss_type"; $boss_health = 100; $boss_damage = 20;
        $special_effect_msg = null; $boss_sprite_filename = null;

        if ($boss_id === 'boss_map2') {
            $boss_name_display = "Charizard chúa (Boss Map 2)"; $boss_name_internal = "Map2_Charizard";
            $boss_type = "golem_rock"; $boss_health = rand(300, 600); $boss_damage = rand(150, 200);
            $boss_sprite_filename = "boss1.png";
        } elseif ($boss_id === 'boss_map3') {
            $boss_name_display = "Tralalero Tralala (Boss Map 3)"; $boss_name_internal = "Map3_Tralala";
            $boss_type = "ancient_golem_grass"; $boss_health = rand(500000, 1000000); $boss_damage = rand(50000, 100000);
            $boss_sprite_filename = "boss2.png";
        } elseif ($boss_id === 'boss_map4') {
            $boss_name_display = "Sigma Pika Pika (Boss Map 4)"; $boss_name_internal = "Map4_sigmaPika";
            $boss_type = "fire_flying_dark"; $boss_health = rand(1000, 50000); $boss_damage = rand(5000, 10000);
            $special_effect_msg = "Chỉ số Pokémon của bạn cảm thấy bị áp chế bởi {$boss_name_display}!";
            $boss_sprite_filename = "boss4.png";
        }

        if ($boss_name_internal !== "UnknownBossInternal" && $boss_sprite_filename !== null) {
            $_SESSION['enemy'] = new Pokemon($boss_name_internal, $boss_type, $boss_health, $boss_damage);
            if(isset($_SESSION['enemy'])) {
                $_SESSION['enemy']->name_display = $boss_name_display;
                $_SESSION['enemy']->is_boss = true;
                $_SESSION['enemy']->boss_sprite_filename = $boss_sprite_filename;
            }
            $response = [
                'success' => true, 'type' => 'boss_battle',
                'message' => "Bạn chạm trán Boss: {$boss_name_display}!",
                'player_pokemon' => $trainer->pokemon, 'enemy_pokemon' => $_SESSION['enemy']
            ];
            if ($special_effect_msg) { $response['special_effect'] = $special_effect_msg; }
        } else {
            $response = ['success' => false, 'message' => "Không tìm thấy thông tin hoặc ảnh cho Boss ID: {$boss_id}."];
            error_log("Trigger Boss Battle: Could not find definition or sprite for boss_id = {$boss_id}");
        }
    }
}
elseif ($action === 'fight_action') {
     if (!isset($enemy) || !($enemy instanceof Pokemon)) { $response = ['success' => false, 'message' => 'Không có đối thủ hợp lệ để chiến đấu.']; }
     elseif ($trainer->pokemon->health <= 0) { $response = ['success' => false, 'message' => 'Pokémon của bạn đã kiệt sức!']; }
     elseif ($enemy->health <= 0) { $response = ['success' => false, 'message' => 'Đối thủ đã bị hạ gục.']; }
     else {
        $enemy_is_drainer = (isset($enemy->name) && $enemy->name === "Map4_sigmaPika");
        $original_stats_player_display = null; $current_response_extras = [];
        if ($enemy_is_drainer && !isset($_SESSION['drainer_effect_applied_this_battle'])) {
            $original_stats_player_display = [ 'health' => $trainer->pokemon->health, 'damage' => $trainer->pokemon->damage ];
            $trainer->pokemon->health = 5; $trainer->pokemon->damage = 5;
            $_SESSION['drainer_effect_applied_this_battle'] = true;
            $current_response_extras['special_effect_applied_log'] = "Kỹ năng đặc biệt của Charizard Hắc Ám: Chỉ số Pokémon của bạn bị hút cạn!";
        }
        $fight_log = $trainer->fight($enemy); 
        $_SESSION['enemy'] = $enemy; 
        $outcome_message = ""; $battle_over = false;
        $enemy_name_display = $enemy->name_display ?? $enemy->name;
        if ($trainer->pokemon->health <= 0) { $outcome_message = "THUA CUỘC: Pokémon của bạn đã bị {$enemy_name_display} đánh bại!"; if ($enemy_is_drainer) $outcome_message = "THUA CUỘC: Dù bị suy yếu, Pokémon của bạn đã chiến đấu dũng cảm nhưng không qua khỏi trước {$enemy_name_display}!"; $battle_over = true; }
        elseif ($enemy->health <= 0) { $outcome_message = "CHIẾN THẮNG! {$enemy_name_display} bị hạ gục. Pokémon của bạn đã lên cấp và hồi máu."; if ($enemy_is_drainer) $outcome_message = "CHIẾN THẮNG KINH NGẠC: Bất chấp bị hút cạn năng lượng, bạn đã đánh bại {$enemy_name_display}!"; $battle_over = true; }
        if ($battle_over && isset($_SESSION['drainer_effect_applied_this_battle'])) { unset($_SESSION['drainer_effect_applied_this_battle']);  }
        $response = array_merge($current_response_extras, [
            'success' => true, 'type' => 'fight_result',
            'fight_log' => $fight_log,
            'player_pokemon_after_fight' => $trainer->pokemon,
            'enemy_pokemon_after_fight' => $enemy, 
            'outcome_message' => $outcome_message, 'battle_over' => $battle_over
        ]);
        if ($original_stats_player_display) { $response['original_stats_player_if_drained'] = $original_stats_player_display; }
    }
}
elseif ($action === 'run_action') {
     if (!isset($enemy) || !($enemy instanceof Pokemon)) { $response = ['success' => false, 'message' => 'Không có đối thủ hợp lệ để bỏ chạy.']; }
     elseif ($trainer->pokemon->health <= 0) { $response = ['success' => false, 'message' => 'Pokémon đã kiệt sức, không thể bỏ chạy.']; }
     elseif ($enemy->health <= 0) { $response = ['success' => true, 'type' => 'run_result', 'ran_away' => true, 'message' => 'Đối thủ đã bị hạ, không cần chạy.', 'battle_over' => true]; unset($_SESSION['enemy']); if (isset($_SESSION['drainer_effect_applied_this_battle'])) unset($_SESSION['drainer_effect_applied_this_battle']); }
     else {
        $initial_hp = $trainer->pokemon->health;
        if ($trainer->run()) {
            unset($_SESSION['enemy']); if (isset($_SESSION['drainer_effect_applied_this_battle'])) unset($_SESSION['drainer_effect_applied_this_battle']);
            $response = ['success' => true, 'type' => 'run_result', 'ran_away' => true, 'message' => 'Bạn đã bỏ chạy thành công!', 'battle_over' => true];
        }
        else { $response = [ 'success' => true, 'type' => 'run_result', 'ran_away' => false, 'message' => "Bỏ chạy thất bại! Pokémon mất HP (từ {$initial_hp} còn {$trainer->pokemon->health}).", 'player_pokemon_after_run_fail' => $trainer->pokemon, 'battle_over' => false ]; }
    }
}
elseif ($action === 'end_battle') {
    unset($_SESSION['enemy']);
    if(isset($_SESSION['drainer_effect_applied_this_battle'])) { unset($_SESSION['drainer_effect_applied_this_battle']); }
    $response = ['success' => true, 'message' => 'Trận đấu đã kết thúc và dọn dẹp session phía server.'];
}
else if (empty($action) && $response['message'] === 'Hành động không hợp lệ hoặc Trainer chưa được khởi tạo.') {
     $response = ['success' => false, 'message' => 'Không có hành động nào được chỉ định cho game_actions.php.'];
}

if (isset($trainer) && $trainer instanceof Trainer && ($action === 'fight_action' || $action === 'run_action')) {
    $_SESSION['trainer'] = $trainer; 
}

echo json_encode($response);
exit;
?>