<?php
session_start();

// --- ĐỊNH NGHĨA CÁC LOẠI TILE MỚI DỰA TRÊN HÌNH ẢNH CUNG CẤP ---

// Tile cơ bản
define('TILE_GRASS', 0);          // Cỏ nền
define('TILE_PATH', 1);           // Đường đi
define('TILE_WATER', 2);          // Nước
define('TILE_TALL_GRASS', 3);     // Cỏ cao (gặp quái)

// Tile vật cản (không thể đi qua)
define('TILE_TREE', 4);           // Cây rậm
define('TILE_ROCK', 5);           // Đá
define('TILE_STAKES', 6);         // Cọc gỗ (hàng rào)

// Tile trang trí (có thể đi qua)
define('TILE_STUMP', 7);          // Gốc cây
define('TILE_GRASS_PATCH', 8);    // Mảng cỏ
define('TILE_DEBRIS', 9);         // Đất đá trên đường

// Tile đặc biệt cho Boss
define('TILE_BOSS_MAP2', 100);
define('TILE_BOSS_MAP3', 101);
define('TILE_BOSS_MAP4', 102);

// --- THUỘC TÍNH CỦA TỪNG LOẠI TILE ---
$tile_properties = [
    // Tile cơ bản
    TILE_GRASS => ['walkable' => true, 'image' => '003.png', 'encounter_chance' => 5],
    TILE_PATH => ['walkable' => true, 'image' => '018.png', 'encounter_chance' => 0],
    TILE_WATER => ['walkable' => false, 'image' => '019.png', 'encounter_chance' => 0],
    TILE_TALL_GRASS => ['walkable' => true, 'image' => '275.png', 'encounter_chance' => 80], // Tỉ lệ gặp quái cao

    // Tile vật cản
    TILE_TREE => ['walkable' => false, 'image' => '280.png', 'encounter_chance' => 0],
    TILE_ROCK => ['walkable' => false, 'image' => '279.png', 'encounter_chance' => 0],
    TILE_STAKES => ['walkable' => false, 'image' => '277.png', 'encounter_chance' => 0],

    // Tile trang trí
    TILE_STUMP => ['walkable' => false, 'image' => '248.png', 'encounter_chance' => 0], // Gốc cây không thể đi qua
    TILE_GRASS_PATCH => ['walkable' => true, 'image' => '189.png', 'encounter_chance' => 20],
    TILE_DEBRIS => ['walkable' => true, 'image' => '228.png', 'encounter_chance' => 0],

    // Tile Boss
    TILE_BOSS_MAP2 => ['walkable' => true, 'image' => '018.png', 'encounter_chance' => 0, 'is_boss' => true, 'boss_id' => 'boss_map2', 'boss_sprite' => 'boss1.png'],
    TILE_BOSS_MAP3 => ['walkable' => true, 'image' => '018.png', 'encounter_chance' => 0, 'is_boss' => true, 'boss_id' => 'boss_map3', 'boss_sprite' => 'boss2.png'],
    TILE_BOSS_MAP4 => ['walkable' => true, 'image' => '018.png', 'encounter_chance' => 0, 'is_boss' => true, 'boss_id' => 'boss_map4', 'boss_sprite' => 'boss4.png'],
];

// --- DỮ LIỆU CÁC MAP ĐÃ ĐƯỢC THIẾT KẾ LẠI ---
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
            // Thiết kế map 3: một khu vực nhỏ, hoang tàn với nhiều gốc cây và đá, boss ở trung tâm.
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
            // Thiết kế map 4: một đấu trường hình tròn được bao quanh bởi cọc gỗ, boss ở giữa.
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

// --- LOGIC XỬ LÝ REQUEST ---
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