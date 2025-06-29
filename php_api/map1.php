<?php

if ($action == "new_battle") {
    $name = "wild pokemon";
    $pokemon_types = ["bulbasaur", "charmander", "pikachu", "squirtle"];
    $type = $pokemon_types[array_rand($pokemon_types)];
    $health = rand(20, 40); 
    $damage = rand(10, 15); 
    $wild_pokemon = new Pokemon($name, $type, $health, $damage);
    $_SESSION["enemy"] = $wild_pokemon;
    if (isset($_SESSION["trainer"]) && $_SESSION["trainer"] instanceof Trainer) {
        echo json_encode([$_SESSION["trainer"]->pokemon, $wild_pokemon]);
    } else {
        echo json_encode(["error" => "Trainer not properly initialized."]);
    }
    die();
} 
else if ($action == "fight") {
    if (!isset($_SESSION["enemy"])) {
        echo json_encode([]); 
        die();
    }
    if (!isset($_SESSION["trainer"]) || !($_SESSION["trainer"] instanceof Trainer)) {
        echo json_encode(["error" => "Trainer not properly initialized."]);
        die();
    }
    if (!($_SESSION["enemy"] instanceof Pokemon)) {
        echo json_encode(["error" => "Enemy is not a valid Pokemon object."]);
        $_SESSION["enemy"] = null; 
        die();
    }

    $result = $_SESSION["trainer"]->fight($_SESSION["enemy"]);
     echo json_encode($result);
    die();
} 
else if ($action == "run") {
    if (!isset($_SESSION["trainer"]) || !($_SESSION["trainer"] instanceof Trainer)) {
        echo json_encode(["error" => "Trainer not properly initialized."]);
        die();
    }
     if (!isset($_SESSION["enemy"])) {
        echo "You can't run, there is no enemy."; 
        die();
    }

    $result = $_SESSION["trainer"]->run();
    if ($result == true) { 
        echo "You escaped";
        $_SESSION["enemy"] = null; 
    } else {
        echo "You failed to escape, and lost a little HP";
    }
    die();
}

?>