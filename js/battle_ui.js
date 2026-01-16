// File: js/battle_ui.js

const battleUI = {
    ui: {
        playerName: document.getElementById('player-pkm-name'),
        playerHp: document.getElementById('player-pkm-hp'),
        playerDmg: document.getElementById('player-pkm-dmg'),
        playerSprite: document.getElementById('player-sprite'),
        enemyName: document.getElementById('enemy-name'),
        enemyHp: document.getElementById('enemy-hp'),
        enemyDmg: document.getElementById('enemy-dmg'),
        enemySprite: document.getElementById('enemy-sprite'),
        skillButtons: [
            document.getElementById('skill-0'),
            document.getElementById('skill-1'),
            document.getElementById('skill-2'),
            document.getElementById('skill-3')
        ],
        runButton: document.getElementById('run-button'),
        battleMessage: document.getElementById('battle-message'),
        battleLogContainer: document.getElementById('battle-log-container'),
        battleLog: document.getElementById('battle-log'),
        battleMusic: document.getElementById('battle-music'),
        sfxHit: document.getElementById('sfx-hit')
    },
    assetsBaseUrl: './assets/images/',
    playerPokemon: null,
    enemyPokemon: null,
    isBattleOver: false, // Thêm cờ để quản lý trạng thái kết thúc trận đấu

    async init() {
        console.log("Battle UI Initializing...");
        if (!this.ui.runButton || !this.ui.battleLogContainer ||
            !this.ui.playerSprite || !this.ui.enemySprite || !this.ui.playerName ||
            !this.ui.enemyName) {
            console.error("Battle UI critical elements not found! Check HTML IDs.");
            this.updateBattleMessage("Lỗi giao diện: Không tìm thấy các thành phần cần thiết.");
            return;
        }

        this.ui.skillButtons.forEach((btn, index) => {
            if (btn) {
                btn.addEventListener('click', () => this.handlePlayerAction('fight_action', index));
            }
        });

        // Sự kiện của nút Run sẽ được điều chỉnh dựa trên trạng thái trận đấu
        this.ui.runButton.addEventListener('click', () => {
            if (this.isBattleOver) {
                this.exitBattleScreen();
            } else {
                this.handlePlayerAction('run_action');
            }
        });

        if (this.ui.battleMusic && this.ui.battleMusic.src && !this.ui.battleMusic.src.endsWith('undefined')) {
            this.ui.battleMusic.volume = 0.3;
            this.ui.battleMusic.play().catch(e => console.warn("Autoplay prevented for battle music:", e.message));
        }

        await this.fetchInitialBattleData();
    },

    async fetchInitialBattleData() {
        // ... (Giữ nguyên như phiên bản trước)
        this.updateBattleMessage("Đang tải dữ liệu trận đấu...");
        this.toggleSkillButtons(false);
        this.ui.runButton.disabled = true;
        try {
            const response = await fetch('./php_api/game_actions.php?action=get_current_battle_data');
            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`HTTP error! Status: ${response.status}, Server Response: ${errorText}`);
            }
            const result = await response.json();
            console.log("Initial Battle Data Received:", result);

            if (result.success && result.player_pokemon && result.enemy_pokemon) {
                this.playerPokemon = result.player_pokemon;
                this.enemyPokemon = result.enemy_pokemon;
                this.updatePokemonDisplay(this.playerPokemon, 'player');
                this.updatePokemonDisplay(this.enemyPokemon, 'enemy');
                this.updateSkillsUI(this.playerPokemon.skills);

                this.updateBattleMessage(result.message || "Trận đấu bắt đầu!");
                if (result.special_effect) {
                    this.addLogMessage(result.special_effect);
                }
                // Kích hoạt nút nếu trận đấu chưa kết thúc (ví dụ, HP đối thủ > 0)
                if (this.playerPokemon.health > 0 && this.enemyPokemon.health > 0) {
                    this.toggleSkillButtons(true);
                    this.ui.runButton.disabled = false;
                } else { // Nếu một trong hai đã hết máu từ đầu (ít khả năng, nhưng để an toàn)
                    this.handleBattleOverUI(result.message || "Trận đấu đã kết thúc trước khi bắt đầu.");
                }

            } else {
                this.updateBattleMessage(`Lỗi tải dữ liệu trận đấu: ${result.message || 'Không có dữ liệu hợp lệ.'}`);
            }
        } catch (error) {
            console.error("Error fetching initial battle data:", error);
            this.updateBattleMessage("Lỗi nghiêm trọng khi tải dữ liệu trận đấu. Không thể bắt đầu.");
        }
    },

    updatePokemonDisplay(pokemonData, typePrefix) {
        // ... (Giữ nguyên như phiên bản trước, đảm bảo nó xử lý is_boss và boss_sprite_filename)
        if (!pokemonData) {
            console.warn(`updatePokemonDisplay called with no data for ${typePrefix}`);
            const nameElClear = this.ui[typePrefix === 'player' ? 'playerName' : 'enemyName'];
            const hpElClear = this.ui[typePrefix === 'player' ? 'playerHp' : 'enemyHp'];
            const dmgElClear = this.ui[typePrefix === 'player' ? 'playerDmg' : 'enemyDmg'];
            const spriteElClear = this.ui[typePrefix === 'player' ? 'playerSprite' : 'enemySprite'];
            if (nameElClear) nameElClear.textContent = "---";
            if (hpElClear) hpElClear.textContent = "--";
            if (dmgElClear) dmgElClear.textContent = "--";
            if (spriteElClear) { spriteElClear.src = ""; spriteElClear.alt = "Không có dữ liệu"; }
            return;
        }

        const nameEl = this.ui[typePrefix === 'player' ? 'playerName' : 'enemyName'];
        const hpEl = this.ui[typePrefix === 'player' ? 'playerHp' : 'enemyHp'];
        const dmgEl = this.ui[typePrefix === 'player' ? 'playerDmg' : 'enemyDmg'];
        const spriteEl = this.ui[typePrefix === 'player' ? 'playerSprite' : 'enemySprite']; // Thẻ <img>
        const spriteContainerEl = spriteEl ? spriteEl.parentElement : null; // Thẻ div.pokemon-sprite


        if (nameEl) nameEl.textContent = pokemonData.name_display || pokemonData.name || "???";
        if (hpEl) hpEl.textContent = pokemonData.health !== undefined ? Math.max(0, pokemonData.health) : "--";
        if (dmgEl) dmgEl.textContent = pokemonData.damage !== undefined ? pokemonData.damage : "--";

        if (spriteEl && spriteContainerEl) {
            let imageName = '';
            let imageFolder = 'pokemons/';

            spriteContainerEl.classList.remove('is-boss-sprite'); // Xóa class cũ

            if (typePrefix === 'enemy' && pokemonData.is_boss && pokemonData.boss_sprite_filename) {
                imageName = pokemonData.boss_sprite_filename;
                imageFolder = 'bosses/';
                spriteContainerEl.classList.add('is-boss-sprite');
            } else if (pokemonData.type) {
                imageName = String(pokemonData.type).toLowerCase() + '.png';
            }

            if (imageName) {
                spriteEl.src = `${this.assetsBaseUrl}${imageFolder}${imageName}`;
                spriteEl.alt = pokemonData.name_display || pokemonData.name || "pokemon_sprite";
                spriteEl.onerror = () => {
                    console.warn(`Could not load sprite: ${spriteEl.src}`);
                    spriteEl.src = "";
                    spriteEl.alt = 'Không có ảnh';
                };
            } else {
                console.warn(`No image identifier (type or boss_sprite_filename) for ${typePrefix} pokemon:`, pokemonData);
                spriteEl.src = "";
                spriteEl.alt = 'Không có ảnh';
            }
        }
    },

    updateBattleMessage(message) { /* ... giữ nguyên ... */
        if (this.ui.battleMessage) { this.ui.battleMessage.textContent = message; }
        console.log("Battle Message UI:", message);
    },

    addLogMessage(logEntry) { /* ... giữ nguyên ... */
        if (this.ui.battleLog && this.ui.battleLogContainer) {
            const p = document.createElement('p'); p.textContent = logEntry;
            this.ui.battleLog.appendChild(p);
            this.ui.battleLogContainer.scrollTop = this.ui.battleLogContainer.scrollHeight;
        }
    },

    clearBattleLog() { /* ... giữ nguyên ... */ if (this.ui.battleLog) { this.ui.battleLog.innerHTML = ''; } },

    playSoundEffect(soundId, soundSrc) { /* ... giữ nguyên ... */
        const sfxElement = this.ui[soundId];
        if (sfxElement) {
            try {
                const sfxUrl = new URL(soundSrc, window.location.origin + window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/') + 1)).pathname;
                if (sfxElement.src !== sfxUrl) {
                    sfxElement.src = sfxUrl;
                }
                sfxElement.currentTime = 0;
                sfxElement.play().catch(e => console.warn("Autoplay prevented for SFX:", e.message, sfxUrl));
            } catch (e) {
                console.error("Error setting/playing SFX URL:", e, soundSrc);
            }
        } else {
            console.warn(`SFX element with id '${soundId}' not found.`);
        }
    },

    handleBattleOverUI(finalMessage) {
        this.isBattleOver = true;
        this.addLogMessage(finalMessage);
        this.toggleSkillButtons(false); // Ẩn/Disable skill buttons
        if (this.ui.skillButtons[0] && this.ui.skillButtons[0].parentElement) {
            this.ui.skillButtons[0].parentElement.style.display = 'none'; // Ẩn cả container nếu cần
        }
        this.ui.runButton.textContent = 'QUAY LẠI MAP'; // Đổi chữ nút Run
        this.ui.runButton.disabled = false; // Kích hoạt nút này
        this.updateBattleMessage(finalMessage + " Nhấn 'QUAY LẠI MAP' để tiếp tục.");
    },

    exitBattleScreen() {
        console.log("Exiting battle screen...");
        sessionStorage.setItem('returnedFromBattle', 'true');
        window.location.href = 'index.html';
    },

    toggleSkillButtons(enable) {
        this.ui.skillButtons.forEach(btn => {
            if (btn) btn.disabled = !enable;
        });
    },

    updateSkillsUI(skills) {
        if (!skills || !Array.isArray(skills)) return;
        this.ui.skillButtons.forEach((btn, index) => {
            if (btn && skills[index]) {
                const s = skills[index];
                btn.textContent = `${s.name} (${s.type === 'heal' ? 'Heal' : (s.type === 'buff' ? 'Buff' : 'Dmg')})`;
                btn.title = `${s.desc} (Value: ${s.value})`;
                btn.style.display = 'inline-block';
            } else if (btn) {
                btn.style.display = 'none'; // Hide unused buttons
            }
        });
    },

    async handlePlayerAction(battleApiAction, skillIndex = 0) {
        this.updateBattleMessage("Đang xử lý...");
        this.toggleSkillButtons(false);
        this.ui.runButton.disabled = true;

        const formData = new FormData();
        formData.append('action', battleApiAction);
        if (battleApiAction === 'fight_action') {
            formData.append('skill_index', skillIndex);
        }

        try {
            const response = await fetch('./php_api/game_actions.php', { method: 'POST', body: formData });
            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`HTTP error! Status: ${response.status}, Server Response: ${errorText}`);
            }
            const result = await response.json();
            console.log("Battle Action Result:", result);

            if (result.success) {
                // Result log is now array of strings using new fightTurn logic
                if (result.fight_log && Array.isArray(result.fight_log)) {
                    result.fight_log.forEach(msg => this.addLogMessage(msg));
                    if (result.fight_log.length > 0) {
                        this.playSoundEffect('sfxHit', './assets/audio/sfx_hit_placeholder.mp3');
                    }
                }

                if (result.player_pokemon_after_fight) this.playerPokemon = result.player_pokemon_after_fight;
                else if (result.player_pokemon_after_run_fail) this.playerPokemon = result.player_pokemon_after_run_fail;

                if (result.enemy_pokemon_after_fight) this.enemyPokemon = result.enemy_pokemon_after_fight;

                this.updatePokemonDisplay(this.playerPokemon, 'player');
                this.updatePokemonDisplay(this.enemyPokemon, 'enemy');

                this.updateBattleMessage(result.outcome_message || result.message || "Lượt của bạn...");

                if (result.battle_over) {
                    this.handleBattleOverUI(result.outcome_message || "Trận đấu đã kết thúc!");
                } else {
                    this.toggleSkillButtons(true);
                    this.ui.runButton.disabled = false;
                }

            } else {
                this.updateBattleMessage(`Lỗi hành động: ${result.message}`);
                this.toggleSkillButtons(true);
                this.ui.runButton.disabled = false;
            }
        } catch (error) {
            console.error("Error during battle action:", error);
            this.updateBattleMessage("Lỗi kết nối server hoặc dữ liệu không hợp lệ trong trận đấu.");
            this.toggleSkillButtons(true);
            this.ui.runButton.disabled = false;
        }
    }
};

window.addEventListener('DOMContentLoaded', () => {
    console.log("Battle page (battle.html) loaded, initializing Battle UI...");
    battleUI.init();
});