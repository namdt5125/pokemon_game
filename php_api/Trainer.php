<?php
include_once(__DIR__ . "/game_config.php");
include_once(__DIR__ . "/Pokemon.php");

class Trainer {
    public $name;
    public $pokemon;
    public $defeated_bosses = [];

    public function __construct($name, $pokemon_name, $pokemon_type){
        $this->name = $name;
        $health = rand(GlobalConfig::BASE_HEALTH_MIN, GlobalConfig::BASE_HEALTH_MAX);
        $damage = rand(GlobalConfig::BASE_DMG_MIN, GlobalConfig::BASE_DMG_MAX);
        $this->pokemon = new Pokemon($pokemon_name, $pokemon_type, $health, $damage);
    }

    public function fightTurn(Pokemon $enemy, $skillIndex) {
        // Sanitize stats to prevent overflow from hacked/legacy saves
        if ($this->pokemon->damage > GlobalConfig::MAX_VAL) $this->pokemon->damage = GlobalConfig::MAX_VAL;
        if ($this->pokemon->health > GlobalConfig::MAX_VAL) $this->pokemon->health = GlobalConfig::MAX_VAL;
        
        if ($enemy->damage > GlobalConfig::MAX_VAL) $enemy->damage = GlobalConfig::MAX_VAL;
        if ($enemy->health > GlobalConfig::MAX_VAL) $enemy->health = GlobalConfig::MAX_VAL;

        $log = [];
        $player_pkm = $this->pokemon;

        // --- Player Turn ---
        if (!isset($player_pkm->skills[$skillIndex])) {
            $skillIndex = 0; // Fallback
        }
        $skill = $player_pkm->skills[$skillIndex];
        $log[] = "Bạn dùng kỹ năng {$skill['name']}!";

        switch ($skill['type']) {
            case 'damage':
                $scale = $skill['scale'] ?? 1.0;
                $dmg = $skill['value'] + intval($player_pkm->damage * $scale); 
                $dmg = min($dmg, GlobalConfig::MAX_VAL); // Cap damage
                $enemy->health -= $dmg;
                $log[] = "{$skill['name']} gây {$dmg} sát thương lên {$enemy->name}!";
                break;
            case 'heal':
                $heal = $skill['value'] + intval($player_pkm->damage * ($skill['scale'] ?? 0));
                $player_pkm->health += $heal;
                $log[] = "Bạn hồi phục {$heal} HP!";
                break;
            case 'buff':
                $buff = $skill['value'];
                $player_pkm->damage += $buff;
                $log[] = "Sức tấn công tăng thêm {$buff}!";
                break;
        }

        // Check if enemy defeated
        if ($enemy->health <= 0) {
            $enemy->health = 0;
            $this->pokemon->levelUp(); // Level up on win
            return [
                'player_hp' => $this->pokemon->health,
                'player_dmg' => $this->pokemon->damage,
                'enemy_hp' => $enemy->health,
                'enemy_dmg' => $enemy->damage,
                'log' => $log,
                'is_over' => true,
                'winner' => 'player'
            ];
        }

        // --- Enemy Turn ---
        // Ensure enemy has skills (for bosses/wild created before update)
        if (method_exists($enemy, 'initSkills')) {
            $enemy->initSkills();
        }
        if (empty($enemy->skills)) {
             // Fallback if initSkills didn't work or not present
             $enemy_skill = ['name' => 'Attack', 'type' => 'damage', 'value' => $enemy->damage, 'desc' => 'Normal Attack'];
        } else {
            $enemy_skill = $enemy->skills[array_rand($enemy->skills)];
        }

        $log[] = "Đối thủ dùng {$enemy_skill['name']}!";

        switch ($enemy_skill['type']) {
             case 'damage':
                $scale = $enemy_skill['scale'] ?? 1.0;
                $dmg = $enemy_skill['value'] + intval($enemy->damage * $scale);
                $dmg = min($dmg, GlobalConfig::MAX_VAL); // Cap damage
                $player_pkm->health -= $dmg;
                $log[] = "Nó gây {$dmg} sát thương lên bạn!";
                break;
            case 'heal':
                $heal = $enemy_skill['value'] + intval($enemy->damage * ($enemy_skill['scale'] ?? 0));
                $enemy->health += $heal;
                $log[] = "Đối thủ hồi phục {$heal} HP!";
                break;
            case 'buff':
                $buff = $enemy_skill['value'];
                $enemy->damage += $buff;
                $log[] = "Đối thủ tăng sức tấn công thêm {$buff}!";
                break;
        }

        if ($player_pkm->health <= 0) {
            $player_pkm->health = 0;
            return [
                'player_hp' => $this->pokemon->health,
                'player_dmg' => $this->pokemon->damage,
                'enemy_hp' => $enemy->health,
                'enemy_dmg' => $enemy->damage,
                'log' => $log,
                'is_over' => true,
                'winner' => 'enemy'
            ];
        }

        return [
            'player_hp' => $this->pokemon->health,
            'player_dmg' => $this->pokemon->damage,
            'enemy_hp' => $enemy->health,
            'enemy_dmg' => $enemy->damage,
            'log' => $log,
            'is_over' => false
        ];
    }

    public function fight(Pokemon $wild_pokemon){
        // Legacy fight method - kept for potential backward compatibility or simple resolution
        // But for this task, we rely on fightTurn.
        // We can leave this as is or modify it. The instructions say "Preserve Logic" for Boss/Save. 
        // Logic for BATTLE is "Enhanced".
        // I'll keep this strictly as a fallback or for the loop-based resolution if ever needed.
        return $this->fightTurn($wild_pokemon, 0); // Redirect to turn 0 if called? No, legacy was full loop.
        // Let's just leave the original fight() body here but unused by new frontend.
        $result = array();
        while ($this->pokemon->health > 0 && $wild_pokemon->health > 0) {
             $this->pokemon->health -= $wild_pokemon->damage;
             if ($this->pokemon->health > 0) $wild_pokemon->health -= $this->pokemon->damage;
             array_push($result, ['player_hp' => $this->pokemon->health, 'enemy_hp' => $wild_pokemon->health]); // Simplified log
             if ($this->pokemon->health <= 0 || $wild_pokemon->health <= 0) break;
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
    public function fightTurn(Pokemon $enemy, $skillIndex) {
        $enemy->health = 0;
        $this->pokemon->levelUp();
        
        return [
            'player_hp' => $this->pokemon->health,
            'player_dmg' => $this->pokemon->damage, 
            'enemy_hp' => 0,
            'enemy_dmg' => $enemy->damage,
            'log' => ["God Mode Activated: Kẻ địch bị tiêu diệt ngay lập tức!"],
            'is_over' => true,
            'winner' => 'player'
        ];
    }

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