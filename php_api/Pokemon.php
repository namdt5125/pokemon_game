<?php
include_once(__DIR__ . "/game_config.php");
class Pokemon {
    public $name;
    public $type;
    public $health;
    public $damage;
    public $skills = [];

    public function __construct($name, $type, $health, $damage){
        $this->name = $name;
        $this->type = $type;
        $this->health = min($health, GlobalConfig::MAX_VAL);
        $this->damage = min($damage, GlobalConfig::MAX_VAL);
        $this->initSkills();
    }
// ...


    public function initSkills() {
        if (!empty($this->skills)) return;

        $basic_skills = [
            'bulbasaur' => [
                ['name' => 'Tackle', 'type' => 'damage', 'value' => 10, 'scale' => 1.1, 'desc' => 'Gây sát thương vật lý'],
                ['name' => 'Vine Whip', 'type' => 'damage', 'value' => 15, 'scale' => 1.3, 'desc' => 'Dùng dây leo quất mạnh'],
                ['name' => 'Synthesis', 'type' => 'heal', 'value' => 30, 'scale' => 0.5, 'desc' => 'Hồi phục HP'],
                ['name' => 'Growl', 'type' => 'buff', 'value' => 5, 'scale' => 0.0, 'desc' => 'Tăng sức tấn công']
            ],
            'charmander' => [
                ['name' => 'Scratch', 'type' => 'damage', 'value' => 10, 'scale' => 1.1, 'desc' => 'Cào cấu đối phương'],
                ['name' => 'Ember', 'type' => 'damage', 'value' => 20, 'scale' => 1.4, 'desc' => 'Phun lửa nhỏ'],
                ['name' => 'Heal', 'type' => 'heal', 'value' => 20, 'scale' => 0.5, 'desc' => 'Hồi phục HP'],
                ['name' => 'Dragon Rage', 'type' => 'buff', 'value' => 8, 'scale' => 0.0, 'desc' => 'Tăng sức tấn công mạnh']
            ],
            'squirtle' => [
                ['name' => 'Tackle', 'type' => 'damage', 'value' => 10, 'scale' => 1.1, 'desc' => 'Gây sát thương vật lý'],
                ['name' => 'Water Gun', 'type' => 'damage', 'value' => 18, 'scale' => 1.3, 'desc' => 'Bắn súng nước'],
                ['name' => 'Withdraw', 'type' => 'heal', 'value' => 40, 'scale' => 0.6, 'desc' => 'Hồi phục HP'],
                ['name' => 'Tail Whip', 'type' => 'buff', 'value' => 5, 'scale' => 0.0, 'desc' => 'Tăng sức tấn công']
            ],
            'pikachu' => [
                ['name' => 'Quick Attack', 'type' => 'damage', 'value' => 15, 'scale' => 1.2, 'desc' => 'Tấn công nhanh'],
                ['name' => 'Thunderbolt', 'type' => 'damage', 'value' => 25, 'scale' => 1.5, 'desc' => 'Giật sét đối phương'],
                ['name' => 'Potion', 'type' => 'heal', 'value' => 25, 'scale' => 0.5, 'desc' => 'Hồi phục HP'],
                ['name' => 'Agility', 'type' => 'buff', 'value' => 8, 'scale' => 0.0, 'desc' => 'Tăng sức tấn công']
            ]
        ];

        // Default/Fallback skills
        $default_skills = [
             ['name' => 'Hit', 'type' => 'damage', 'value' => 10, 'scale' => 1.0, 'desc' => 'Đánh thường'],
             ['name' => 'Kick', 'type' => 'damage', 'value' => 15, 'scale' => 1.1, 'desc' => 'Đá mạnh'],
             ['name' => 'Bandage', 'type' => 'heal', 'value' => 20, 'scale' => 0.5, 'desc' => 'Băng bó vết thương'],
             ['name' => 'Focus', 'type' => 'buff', 'value' => 5, 'scale' => 0.0, 'desc' => 'Tập trung sức mạnh']
        ];

        $key = strtolower($this->type);
        if (isset($basic_skills[$key])) {
            $this->skills = $basic_skills[$key];
        } else {
             $this->skills = $default_skills;
        }
    }
    public function levelUp(){
        $this->damage = min($this->damage + GlobalConfig::DMG_INCREASE, GlobalConfig::MAX_VAL);
        $this->health = min($this->health + GlobalConfig::HEALTH_INCREASE, GlobalConfig::MAX_VAL);
    }

    public function __toString(){
        return json_encode($this);
    }
}
?>