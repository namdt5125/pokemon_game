<?php
session_start();

define('TILE_GRASS', 0);
define('TILE_PATH', 1);
define('TILE_WATER', 2); 
define('TILE_TREE', 3);  
define('TILE_HOUSE_WALL', 4); 
define('TILE_HOUSE_ROOF_RED', 5); 
define('TILE_LAB_WALL', 6); 
define('TILE_LAB_ROOF', 7); 
define('TILE_FENCE', 8); 
define('TILE_SIGN', 9); 

define('TILE_BOSS_MAP2', 100);
define('TILE_BOSS_MAP3', 101);
define('TILE_BOSS_MAP4', 102);

$tile_properties = [
    TILE_GRASS => ['walkable' => true, 'image' => 'wildani.png', 'encounter_chance' => 15],
    TILE_PATH => ['walkable' => true, 'image' => 'path.png', 'encounter_chance' => 0],
    TILE_WATER => ['walkable' => false, 'image' => 'water.png', 'encounter_chance' => 0],
    TILE_TREE => ['walkable' => false, 'image' => 'tree.png', 'encounter_chance' => 0],
    TILE_HOUSE_WALL => ['walkable' => false, 'image' => 'house_wall.png', 'encounter_chance' => 0],
    TILE_HOUSE_ROOF_RED => ['walkable' => false, 'image' => 'house_roof_red.png', 'encounter_chance' => 0],
    TILE_LAB_WALL => ['walkable' => false, 'image' => 'lab_wall.png', 'encounter_chance' => 0],
    TILE_LAB_ROOF => ['walkable' => false, 'image' => 'lab_roof.png', 'encounter_chance' => 0],
    TILE_FENCE => ['walkable' => false, 'image' => 'fence.png', 'encounter_chance' => 0],
    TILE_SIGN => ['walkable' => false, 'image' => 'sign.png', 'encounter_chance' => 0],

    TILE_BOSS_MAP2 => ['walkable' => true, 'image' => 'path.png', 'encounter_chance' => 0, 'is_boss' => true, 'boss_id' => 'boss_map2', 'boss_sprite' => 'boss1.png'],
    TILE_BOSS_MAP3 => ['walkable' => true, 'image' => 'path.png', 'encounter_chance' => 0, 'is_boss' => true, 'boss_id' => 'boss_map3', 'boss_sprite' => 'boss2.png'],
    TILE_BOSS_MAP4 => ['walkable' => true, 'image' => 'path.png', 'encounter_chance' => 0, 'is_boss' => true, 'boss_id' => 'boss_map4', 'boss_sprite' => 'boss4.png'],
];

$maps_data = [
    'map1' => [
        'name' => "Thị Trấn Lmao",
        'start_pos' => ['x' => 5, 'y' => 5],
        'layout' => [ 
            [TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE],
            [TILE_TREE,TILE_GRASS,TILE_GRASS,TILE_PATH,TILE_PATH,TILE_PATH,TILE_PATH,TILE_PATH,TILE_GRASS,TILE_GRASS,TILE_GRASS,TILE_TREE],
            [TILE_TREE,TILE_GRASS,TILE_GRASS,TILE_PATH,TILE_HOUSE_WALL,TILE_HOUSE_WALL,TILE_HOUSE_WALL,TILE_PATH,TILE_GRASS,TILE_GRASS,TILE_GRASS,TILE_TREE],
            [TILE_TREE,TILE_GRASS,TILE_GRASS,TILE_PATH,TILE_HOUSE_WALL,TILE_HOUSE_ROOF_RED,TILE_HOUSE_WALL,TILE_PATH,TILE_GRASS,TILE_GRASS,TILE_GRASS,TILE_TREE],
            [TILE_TREE,TILE_PATH,TILE_PATH,TILE_PATH,TILE_PATH,TILE_PATH,TILE_PATH,TILE_PATH,TILE_PATH,TILE_PATH,TILE_PATH,TILE_TREE],
            [TILE_TREE,TILE_PATH,TILE_GRASS,TILE_GRASS,TILE_PATH,TILE_LAB_WALL,TILE_LAB_WALL,TILE_PATH,TILE_GRASS,TILE_GRASS,TILE_PATH,TILE_TREE],
            [TILE_TREE,TILE_PATH,TILE_GRASS,TILE_GRASS,TILE_PATH,TILE_LAB_WALL,TILE_LAB_ROOF,TILE_PATH,TILE_GRASS,TILE_GRASS,TILE_PATH,TILE_TREE],
            [TILE_TREE,TILE_PATH,TILE_PATH,TILE_PATH,TILE_PATH,TILE_PATH,TILE_PATH,TILE_PATH,TILE_PATH,TILE_PATH,TILE_PATH,TILE_TREE],
            [TILE_TREE,TILE_GRASS,TILE_GRASS,TILE_WATER,TILE_WATER,TILE_WATER,TILE_WATER,TILE_GRASS,TILE_GRASS,TILE_FENCE,TILE_FENCE,TILE_TREE],
            [TILE_TREE,TILE_GRASS,TILE_GRASS,TILE_WATER,TILE_WATER,TILE_WATER,TILE_WATER,TILE_GRASS,TILE_GRASS,TILE_PATH,TILE_SIGN,TILE_TREE],
            [TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE],
        ],
        'music' => '../assets/audio/background_music.mp3'
    ],
    'map2' => [
        'name' => "Hang Động Bruh",
        'start_pos' => ['x' => 1, 'y' => 1],
        'layout' => [ 
            [TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE],
            [TILE_TREE,TILE_PATH,TILE_PATH,TILE_PATH,TILE_PATH,TILE_PATH,TILE_PATH,TILE_PATH,TILE_PATH,TILE_TREE],
            [TILE_TREE,TILE_PATH,TILE_GRASS,TILE_GRASS,TILE_GRASS,TILE_PATH,TILE_GRASS,TILE_GRASS,TILE_PATH,TILE_TREE],
            [TILE_TREE,TILE_PATH,TILE_GRASS,TILE_PATH,TILE_PATH,TILE_PATH,TILE_PATH,TILE_GRASS,TILE_PATH,TILE_TREE],
            [TILE_TREE,TILE_PATH,TILE_GRASS,TILE_PATH,TILE_BOSS_MAP2,TILE_PATH,TILE_PATH,TILE_GRASS,TILE_PATH,TILE_TREE],
            [TILE_TREE,TILE_PATH,TILE_GRASS,TILE_PATH,TILE_PATH,TILE_PATH,TILE_PATH,TILE_GRASS,TILE_PATH,TILE_TREE],
            [TILE_TREE,TILE_PATH,TILE_GRASS,TILE_GRASS,TILE_GRASS,TILE_GRASS,TILE_GRASS,TILE_GRASS,TILE_PATH,TILE_TREE],
            [TILE_TREE,TILE_PATH,TILE_PATH,TILE_PATH,TILE_PATH,TILE_PATH,TILE_PATH,TILE_PATH,TILE_PATH,TILE_TREE],
            [TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE],
        ],
        'music' => '../assets/audio/background_music.mp3'
    ],
     'map3' => [
        'name' => "Khu Rừng Brainrot",
        'start_pos' => ['x' => 1, 'y' => 1],
        'layout' => [
            [TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE],
            [TILE_TREE,TILE_GRASS,TILE_GRASS,TILE_PATH,TILE_PATH,TILE_GRASS,TILE_GRASS,TILE_TREE],
            [TILE_TREE,TILE_GRASS,TILE_PATH,TILE_PATH,TILE_BOSS_MAP3,TILE_PATH,TILE_GRASS,TILE_TREE],
            [TILE_TREE,TILE_GRASS,TILE_GRASS,TILE_PATH,TILE_PATH,TILE_GRASS,TILE_GRASS,TILE_TREE],
            [TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE],
        ],
        'music' => '../assets/audio/background_music.mp3'
    ],
    'map4' => [
        'name' => "Sân Khấu Sigma",
        'start_pos' => ['x' => 1, 'y' => 1],
        'layout' => [
            [TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE], 
            [TILE_TREE,TILE_PATH,TILE_PATH,TILE_PATH,TILE_PATH,TILE_PATH,TILE_PATH,TILE_TREE],
            [TILE_TREE,TILE_PATH,TILE_PATH,TILE_BOSS_MAP4,TILE_PATH,TILE_PATH,TILE_PATH,TILE_TREE],
            [TILE_TREE,TILE_PATH,TILE_PATH,TILE_PATH,TILE_PATH,TILE_PATH,TILE_PATH,TILE_TREE],
            [TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE,TILE_TREE],
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