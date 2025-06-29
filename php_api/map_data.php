<?php
session_start();

define('TILE_GRASS', 0);          
define('TILE_PATH', 1);       
define('TILE_WATER', 2);        
define('TILE_TALL_GRASS', 3);    

define('TILE_TREE', 4);      
define('TILE_ROCK', 5);     
define('TILE_STAKES', 6);      

define('TILE_STUMP', 7);          
define('TILE_GRASS_PATCH', 8);    
define('TILE_DEBRIS', 9);       

define('TILE_BOSS_MAP2', 100);
define('TILE_BOSS_MAP3', 101);
define('TILE_BOSS_MAP4', 102);

$tile_properties = [

    TILE_GRASS => ['walkable' => true, 'image' => '003.png', 'encounter_chance' => 5],
    TILE_PATH => ['walkable' => true, 'image' => '018.png', 'encounter_chance' => 0],
    TILE_WATER => ['walkable' => false, 'image' => '019.png', 'encounter_chance' => 0],
    TILE_TALL_GRASS => ['walkable' => true, 'image' => '275.png', 'encounter_chance' => 80],

    TILE_TREE => ['walkable' => false, 'image' => '280.png', 'encounter_chance' => 0],
    TILE_ROCK => ['walkable' => false, 'image' => '279.png', 'encounter_chance' => 0],
    TILE_STAKES => ['walkable' => false, 'image' => '277.png', 'encounter_chance' => 0],

    TILE_STUMP => ['walkable' => false, 'image' => '248.png', 'encounter_chance' => 0],
    TILE_GRASS_PATCH => ['walkable' => true, 'image' => '189.png', 'encounter_chance' => 20],
    TILE_DEBRIS => ['walkable' => true, 'image' => '228.png', 'encounter_chance' => 0],

    TILE_BOSS_MAP2 => ['walkable' => true, 'image' => '018.png', 'encounter_chance' => 0, 'is_boss' => true, 'boss_id' => 'boss_map2', 'boss_sprite' => 'boss1.png'],
    TILE_BOSS_MAP3 => ['walkable' => true, 'image' => '018.png', 'encounter_chance' => 0, 'is_boss' => true, 'boss_id' => 'boss_map3', 'boss_sprite' => 'boss2.png'],
    TILE_BOSS_MAP4 => ['walkable' => true, 'image' => '018.png', 'encounter_chance' => 0, 'is_boss' => true, 'boss_id' => 'boss_map4', 'boss_sprite' => 'boss4.png'],
];

$maps_data = [
    'map1' => [
        'name' => "Khu Rừng lmao",
        'start_pos' => ['x' => 10, 'y' => 12],
        'layout' => [
            [TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE],
            [TILE_TREE, TILE_GRASS, TILE_GRASS, TILE_TALL_GRASS, TILE_TALL_GRASS, TILE_GRASS, TILE_STUMP, TILE_GRASS, TILE_TREE, TILE_TALL_GRASS, TILE_TALL_GRASS, TILE_TALL_GRASS, TILE_GRASS, TILE_GRASS, TILE_TREE],
            [TILE_TREE, TILE_GRASS, TILE_GRASS_PATCH, TILE_GRASS, TILE_GRASS, TILE_GRASS, TILE_GRASS, TILE_GRASS, TILE_TREE, TILE_GRASS, TILE_GRASS, TILE_GRASS, TILE_STUMP, TILE_GRASS, TILE_TREE],
            [TILE_TREE, TILE_TALL_GRASS, TILE_GRASS, TILE_PATH, TILE_PATH, TILE_PATH, TILE_DEBRIS, TILE_PATH, TILE_PATH, TILE_PATH, TILE_PATH, TILE_GRASS, TILE_GRASS, TILE_GRASS, TILE_TREE],
            [TILE_TREE, TILE_TALL_GRASS, TILE_GRASS, TILE_PATH, TILE_STAKES, TILE_STAKES, TILE_STAKES, TILE_STAKES, TILE_STAKES, TILE_STAKES, TILE_PATH, TILE_GRASS, TILE_ROCK, TILE_GRASS, TILE_TREE],
            [TILE_TREE, TILE_GRASS, TILE_GRASS, TILE_PATH, TILE_STAKES, TILE_GRASS, TILE_GRASS, TILE_GRASS, TILE_GRASS, TILE_STAKES, TILE_PATH, TILE_GRASS, TILE_GRASS, TILE_GRASS, TILE_TREE],
            [TILE_TREE, TILE_GRASS, TILE_ROCK, TILE_PATH, TILE_STAKES, TILE_GRASS, TILE_STUMP, TILE_GRASS, TILE_GRASS, TILE_STAKES, TILE_PATH, TILE_DEBRIS, TILE_PATH, TILE_PATH, TILE_TREE],
            [TILE_TREE, TILE_GRASS, TILE_GRASS, TILE_PATH, TILE_STAKES, TILE_STAKES, TILE_STAKES, TILE_STAKES, TILE_STAKES, TILE_STAKES, TILE_PATH, TILE_GRASS, TILE_GRASS, TILE_GRASS, TILE_TREE],
            [TILE_TREE, TILE_GRASS, TILE_GRASS, TILE_PATH, TILE_PATH, TILE_PATH, TILE_DEBRIS, TILE_PATH, TILE_PATH, TILE_PATH, TILE_PATH, TILE_GRASS, TILE_TALL_GRASS, TILE_GRASS, TILE_TREE],
            [TILE_TREE, TILE_WATER, TILE_WATER, TILE_WATER, TILE_GRASS, TILE_GRASS, TILE_GRASS, TILE_GRASS, TILE_GRASS, TILE_GRASS, TILE_GRASS, TILE_GRASS, TILE_TALL_GRASS, TILE_GRASS, TILE_TREE],
            [TILE_TREE, TILE_WATER, TILE_WATER, TILE_WATER, TILE_WATER, TILE_WATER, TILE_GRASS, TILE_STUMP, TILE_TALL_GRASS, TILE_GRASS, TILE_GRASS, TILE_ROCK, TILE_GRASS, TILE_GRASS, TILE_TREE],
            [TILE_TREE, TILE_WATER, TILE_WATER, TILE_WATER, TILE_WATER, TILE_GRASS, TILE_GRASS, TILE_GRASS, TILE_GRASS, TILE_GRASS, TILE_PATH, TILE_PATH, TILE_PATH, TILE_PATH, TILE_TREE],
            [TILE_TREE, TILE_GRASS, TILE_GRASS, TILE_GRASS, TILE_GRASS, TILE_GRASS, TILE_GRASS, TILE_GRASS, TILE_GRASS, TILE_GRASS, TILE_PATH, TILE_STUMP, TILE_GRASS, TILE_GRASS, TILE_TREE],
            [TILE_TREE, TILE_GRASS, TILE_TALL_GRASS, TILE_TALL_GRASS, TILE_GRASS, TILE_GRASS, TILE_ROCK, TILE_GRASS, TILE_TALL_GRASS, TILE_GRASS, TILE_PATH, TILE_GRASS, TILE_GRASS, TILE_GRASS, TILE_TREE],
            [TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE],
        ],
        'music' => '../assets/audio/background_music.mp3'
    ],
    'map2' => [
        'name' => "Rừng Gì Đấy",
        'start_pos' => ['x' => 1, 'y' => 5],
        'layout' => [
            [TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE],
            [TILE_TREE, TILE_TALL_GRASS, TILE_TALL_GRASS, TILE_TALL_GRASS, TILE_ROCK, TILE_GRASS, TILE_GRASS, TILE_STUMP, TILE_GRASS, TILE_TALL_GRASS, TILE_TREE],
            [TILE_TREE, TILE_GRASS, TILE_GRASS, TILE_PATH, TILE_PATH, TILE_PATH, TILE_PATH, TILE_PATH, TILE_PATH, TILE_PATH, TILE_TREE],
            [TILE_TREE, TILE_STUMP, TILE_GRASS, TILE_PATH, TILE_ROCK, TILE_TALL_GRASS, TILE_TALL_GRASS, TILE_GRASS, TILE_GRASS, TILE_PATH, TILE_TREE],
            [TILE_TREE, TILE_GRASS, TILE_GRASS, TILE_PATH, TILE_GRASS, TILE_GRASS, TILE_BOSS_MAP2, TILE_GRASS, TILE_TALL_GRASS, TILE_PATH, TILE_TREE],
            [TILE_TREE, TILE_GRASS, TILE_ROCK, TILE_PATH, TILE_TALL_GRASS, TILE_TALL_GRASS, TILE_TALL_GRASS, TILE_ROCK, TILE_GRASS, TILE_PATH, TILE_TREE],
            [TILE_TREE, TILE_TALL_GRASS, TILE_GRASS, TILE_PATH, TILE_PATH, TILE_DEBRIS, TILE_PATH, TILE_PATH, TILE_PATH, TILE_PATH, TILE_TREE],
            [TILE_TREE, TILE_TALL_GRASS, TILE_TALL_GRASS, TILE_STUMP, TILE_GRASS, TILE_GRASS, TILE_ROCK, TILE_TALL_GRASS, TILE_TALL_GRASS, TILE_TALL_GRASS, TILE_TREE],
            [TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE],
        ],
        'music' => '../assets/audio/background_music.mp3'
    ],
    'map3' => [
        'name' => "Khu Rừng Hoang Tàn",
        'start_pos' => ['x' => 1, 'y' => 4],
        'layout' => [
            [TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE],
            [TILE_TREE, TILE_GRASS, TILE_GRASS, TILE_STUMP, TILE_PATH, TILE_ROCK, TILE_TALL_GRASS, TILE_GRASS, TILE_TREE],
            [TILE_TREE, TILE_GRASS, TILE_ROCK, TILE_PATH, TILE_PATH, TILE_PATH, TILE_STUMP, TILE_GRASS, TILE_TREE],
            [TILE_TREE, TILE_STUMP, TILE_PATH, TILE_PATH, TILE_BOSS_MAP3, TILE_PATH, TILE_PATH, TILE_ROCK, TILE_TREE],
            [TILE_TREE, TILE_GRASS, TILE_GRASS, TILE_PATH, TILE_PATH, TILE_PATH, TILE_GRASS, TILE_GRASS, TILE_TREE],
            [TILE_TREE, TILE_TALL_GRASS, TILE_STUMP, TILE_ROCK, TILE_PATH, TILE_STUMP, TILE_TALL_GRASS, TILE_GRASS, TILE_TREE],
            [TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE],
        ],
        'music' => '../assets/audio/background_music.mp3'
    ],
    'map4' => [
        'name' => "Đấu Trường Cổ Xưa",
        'start_pos' => ['x' => 4, 'y' => 6],
        'layout' => [
            [TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE],
            [TILE_TREE, TILE_GRASS, TILE_GRASS, TILE_STAKES, TILE_STAKES, TILE_STAKES, TILE_GRASS, TILE_GRASS, TILE_TREE],
            [TILE_TREE, TILE_GRASS, TILE_STAKES, TILE_PATH, TILE_DEBRIS, TILE_PATH, TILE_STAKES, TILE_GRASS, TILE_TREE],
            [TILE_TREE, TILE_STAKES, TILE_PATH, TILE_PATH, TILE_BOSS_MAP4, TILE_PATH, TILE_PATH, TILE_STAKES, TILE_TREE],
            [TILE_TREE, TILE_GRASS, TILE_STAKES, TILE_PATH, TILE_PATH, TILE_PATH, TILE_STAKES, TILE_GRASS, TILE_TREE],
            [TILE_TREE, TILE_GRASS, TILE_GRASS, TILE_STAKES, TILE_PATH, TILE_STAKES, TILE_GRASS, TILE_GRASS, TILE_TREE],
            [TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_PATH, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE],
            [TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE, TILE_PATH, TILE_TREE, TILE_TREE, TILE_TREE, TILE_TREE],
        ],
        'music' => '../assets/audio/background_music.mp3'
    ],
];

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';
$map_name = $_GET['map_name'] ?? 'map1';

if ($action === 'get_map_data') {
    if (isset($maps_data[$map_name])) {
        echo json_encode([
            'success' => true,
            'map_info' => $maps_data[$map_name],
            'tile_properties' => $tile_properties
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Map not found']);
    }
    exit;
}

echo json_encode(['success' => false, 'message' => 'Invalid action or no action specified for map_data.php']);
?>