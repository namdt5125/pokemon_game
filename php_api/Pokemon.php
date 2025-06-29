<?php
include_once(__DIR__ . "/game_config.php");
class Pokemon {
    public $name;
    public $type;
    public $health;
    public $damage;

    public function __construct($name, $type, $health, $damage){
        $this->name = $name;
        $this->type = $type;
        $this->health = $health;
        $this->damage = $damage;
    }
    public function levelUp(){
        $this->damage = $this->damage + GlobalConfig::DMG_INCREASE;
        $this->health = $this->health + GlobalConfig::HEALTH_INCREASE;
    }

    public function __toString(){
        return json_encode($this);
    }
}
?>