<?php
session_start();
if (!isset($_SESSION["username"])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trận Đấu Pokémon!</title>
    <link href="https://fonts.googleapis.com/css2?family=Press+Start+2P&display=swap" rel="stylesheet"> <link rel="stylesheet" href="css/battle_style.css">
</head>
    <div id="battle-screen-container">
        <h1 id="battle-title">Battle</h1>

        <div class="pokemon-area" id="enemy-pokemon-area">
            <div class="pokemon-sprite">
                <img id="enemy-sprite" src="" alt="Đối Thủ">
            </div>
            <div class="pokemon-stats-box">
                <p class="pokemon-name"><span id="enemy-name">Wild Pokemon</span></p>
                <p>⭐Strength: <span id="enemy-dmg">--</span></p>
                <p>❤️HP: <span id="enemy-hp">--</span></p>
            </div>
        </div>

        <div class="pokemon-area" id="player-pokemon-area">
            <div class="pokemon-sprite">
                <img id="player-sprite" src="" alt="Pokémon Của Bạn">
            </div>
            <div class="pokemon-stats-box">
                <p class="pokemon-name"><span id="player-pkm-name">User Pokemon</span></p>
                <p>⭐Strength: <span id="player-pkm-dmg">--</span></p>
                <p>❤️HP: <span id="player-pkm-hp">--</span></p>
            </div>
        </div>

        <div id="battle-actions" style="flex-direction: column; height: auto;">
            <div id="skill-container" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 10px; width: 100%;">
                <button class="skill-btn" id="skill-0">Skill 1</button>
                <button class="skill-btn" id="skill-1">Skill 2</button>
                <button class="skill-btn" id="skill-2">Skill 3</button>
                <button class="skill-btn" id="skill-3">Skill 4</button>
            </div>
            <button id="run-button" style="width: 100%;">Run</button>
        </div>

        <div id="battle-log-container">
            <p id="battle-message">Trận đấu bắt đầu!</p>
            <div id="battle-log">
                </div>
        </div>
    </div>

    <audio id="battle-music" loop src="./assets/audio/battle_music.mp3"></audio> <audio id="sfx-hit"></audio> <script src="js/battle_ui.js"></script> </body>
</html>