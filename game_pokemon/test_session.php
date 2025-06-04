<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once(__DIR__ . "/php_api/Pokemon.php");
include_once(__DIR__ . "/php_api/Trainer.php");

session_start();

header('Content-Type: text/plain');
echo "SESSION DATA:\n";
print_r($_SESSION); 

if (isset($_SESSION['trainer'])) {
    if ($_SESSION['trainer'] instanceof Trainer) {
        echo "\n\n_SESSION['trainer'] is a valid Trainer object.\n";
        echo "Trainer name: " . $_SESSION['trainer']->name . "\n";
        if (isset($_SESSION['trainer']->pokemon) && $_SESSION['trainer']->pokemon instanceof Pokemon) {
            echo "Trainer Pokemon: " . $_SESSION['trainer']->pokemon->name . "\n";
        } else {
            echo "Trainer's Pokemon is NOT a valid Pokemon object or not set.\n";
            if(isset($_SESSION['trainer']->pokemon)) {
                echo "Type of trainer's pokemon: " . (is_object($_SESSION['trainer']->pokemon) ? get_class($_SESSION['trainer']->pokemon) : gettype($_SESSION['trainer']->pokemon)) . "\n";
            }
        }
    } else {
        echo "\n\n_SESSION['trainer'] is set, but NOT a valid Trainer object. Type: " . (is_object($_SESSION['trainer']) ? get_class($_SESSION['trainer']) : gettype($_SESSION['trainer'])) . "\n";
    }
} else {
    echo "\n\n_SESSION['trainer'] is NOT SET.\n";
}
?>