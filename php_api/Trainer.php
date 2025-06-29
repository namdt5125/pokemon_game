<?php
include_once(__DIR__ . "/game_config.php");
include_once(__DIR__ . "/Pokemon.php");

class Trainer {
    public $name;
    public $pokemon;

    public function __construct($name, $pokemon_name, $pokemon_type){
        $this->name = $name;
        $health = rand(GlobalConfig::BASE_HEALTH_MIN, GlobalConfig::BASE_HEALTH_MAX);
        $damage = rand(GlobalConfig::BASE_DMG_MIN, GlobalConfig::BASE_DMG_MAX);
        $this->pokemon = new Pokemon($pokemon_name, $pokemon_type, $health, $damage);
    }

    public function fight(Pokemon $wild_pokemon){
        $result = array();
        while ($this->pokemon->health > 0 && $wild_pokemon->health > 0) {
            $this->pokemon->health -= $wild_pokemon->damage;

            if ($this->pokemon->health > 0) {
                $wild_pokemon->health -= $this->pokemon->damage;
            }

            array_push($result, [
                'player_hp' => max(0, $this->pokemon->health),
                'player_dmg' => $this->pokemon->damage,
                'enemy_hp' => max(0, $wild_pokemon->health),
                'enemy_dmg' => $wild_pokemon->damage
            ]);

            if ($this->pokemon->health <= 0 || $wild_pokemon->health <= 0) {
                break;
            }
        }

        if ($wild_pokemon->health < 0) {
            $wild_pokemon->health = 0;
        }
        if ($this->pokemon->health < 0) {
            $this->pokemon->health = 0;
        }

        if ($this->pokemon->health > 0 && $wild_pokemon->health <= 0) {
            $this->pokemon->levelUp();
        }


        return $result; 
    }

    public function checkAlive(){
        if ($this->pokemon->health <= 0) return false;
        return true;
    }

    public function run(){
        $rate = rand(0, 100);
        if ($rate > GlobalConfig::RUN_CHANCE) return true;
        $this->pokemon->health = $this->pokemon->health - intval($this->pokemon->health / 10);
        if ($this->pokemon->health < 0) $this->pokemon->health = 0;
        return false;
    }

    public function __toString() {
        return json_encode($this);
    }
}

class godMode extends Trainer{
    public function fight(Pokemon $wild_pokemon) {
        $wild_pokemon->health = 0;
        $original_hp_before_levelup = $this->pokemon->health; 
        $this->pokemon->levelUp();

        return [[
            'player_hp' => $this->pokemon->health,
            'player_dmg' => $this->pokemon->damage, 
            'enemy_hp' => 0,
            'enemy_dmg' => $wild_pokemon->damage 
        ]];
    }
}
?>