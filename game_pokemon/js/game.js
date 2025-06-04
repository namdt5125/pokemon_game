// File: js/game.js

const game = {
    tileSize: 65, 
    player: {
        x: 0,
        y: 0,
        domElement: null,
        facing: 'down',
        sprites: {
            down: 'haha.png',
            up: 'haha.png', 
            left: 'moving_user.png',
            right: 'moving_user.png'
        }
    },
    currentMapData: null,
    currentMapName: 'map1', // Map bắt đầu mặc định
    tileProperties: null,
    mapGridDom: null,
    ui: {
        mapDisplay: null,
        playerCoords: null,
        gameMessage: null,
        pkmName: null,
        pkmType: null,
        pkmHp: null,
        pkmDmg: null,
        pkmImage: null,
        trainerNameDisplay: null
    },
    audio: {
        backgroundMusic: null,
        sfx: null
    },
    assetsBaseUrl: './assets/images/',
    bossSpritesOnMap: [],
    inBattle: false, // Sẽ được đặt lại khi quay về từ battle.html

    async init() {
        console.log("Game init started...");
        this.mapGridDom = document.getElementById('map-grid');
        this.player.domElement = document.getElementById('player');
        
        this.ui.mapDisplay = document.getElementById('current-map-display');
        this.ui.playerCoords = document.getElementById('player-coords');
        this.ui.gameMessage = document.getElementById('game-message');
        
        this.ui.pkmName = document.getElementById('pkm-name');
        this.ui.pkmType = document.getElementById('pkm-type');
        this.ui.pkmHp = document.getElementById('pkm-hp');
        this.ui.pkmDmg = document.getElementById('pkm-dmg');
        this.ui.pkmImage = document.getElementById('pkm-image');
        this.ui.trainerNameDisplay = document.getElementById('trainer-name-display');
        
        this.audio.backgroundMusic = document.getElementById('background-music');
        this.audio.sfx = document.getElementById('sfx-sound');

        // Kiểm tra xem có phải quay lại từ trận đấu không
        if (sessionStorage.getItem('returnedFromBattle')) {
            console.log("Returned from battle. Resuming game...");
            sessionStorage.removeItem('returnedFromBattle');
            this.inBattle = false; // Đảm bảo không còn trong trạng thái chiến đấu
            // currentMapName nên được lấy từ session PHP khi tải lại thông tin trainer
            // Hoặc nếu battle.js lưu map hiện tại vào sessionStorage
            const storedMap = sessionStorage.getItem('mapBeforeBattle');
            this.currentMapName = storedMap || this.currentMapName; 
            if(storedMap) sessionStorage.removeItem('mapBeforeBattle');

            await this.fetchAndUpdatePlayerPokemonInfo(); // Tải lại thông tin mới nhất
            if (this.ui.trainerNameDisplay && this.ui.trainerNameDisplay.textContent !== '-' && 
                this.ui.trainerNameDisplay.textContent !== 'Lỗi tải' &&
                !this.ui.trainerNameDisplay.textContent.includes("Không tìm thấy")) {
                 await this.loadMapByName(this.currentMapName);
                 this.setGameMessage("Chào mừng trở lại bản đồ!");
            } else {
                this.setGameMessage("Lỗi tải thông tin người chơi sau trận đấu. Vui lòng thử đăng nhập lại.");
                console.error("Player info not loaded post-battle, map loading aborted.");
            }
        } else {
            // Khởi tạo bình thường nếu không phải quay lại từ trận đấu
            const playerInfoLoaded = await this.fetchAndUpdatePlayerPokemonInfo(); 
            if (playerInfoLoaded) {
                 await this.loadMapByName(this.currentMapName);
            } else {
                this.setGameMessage("Không thể tải thông tin người chơi. Vui lòng thử đăng nhập lại hoặc 'Chơi Mới Hoàn Toàn' từ trang Profile.");
                console.error("Player info not loaded or invalid during initial init, map loading aborted.");
            }
        }

        document.addEventListener('keydown', (event) => this.handleKeyPress(event));
        this.updateUIDisplay(); 
        this.playBackgroundMusic(); // Sẽ phát nếu this.inBattle là false
    },

    async fetchAndUpdatePlayerPokemonInfo() {
        console.log("Fetching player info from server (action: get_trainer_info)...");
        let success = false; // Biến để theo dõi thành công của fetch
        try {
            const response = await fetch('php_api/game_actions.php?action=get_trainer_info');
            if (!response.ok) {
                const errorText = await response.text();
                console.error(`fetchAndUpdatePlayerPokemonInfo - HTTP error! Status: ${response.status}, Server says: ${errorText}`);
                this.clearPlayerPokemonInfoUI("Lỗi server: " + response.status);
                this.setGameMessage(`Lỗi tải thông tin người chơi: ${response.status}`);
                return false;
            }
            const result = await response.json();
            console.log("Player info received:", result);

            if (result.success && result.trainer) {
                const trainer = result.trainer;
                const pkm = trainer.pokemon;

                if(this.ui.trainerNameDisplay) this.ui.trainerNameDisplay.textContent = trainer.name || "N/A";

                if (pkm && typeof pkm === 'object') {
                    if(this.ui.pkmName) this.ui.pkmName.textContent = pkm.name || "-";
                    if(this.ui.pkmType) this.ui.pkmType.textContent = pkm.type || "-";
                    if(this.ui.pkmHp) this.ui.pkmHp.textContent = (pkm.health !== undefined && pkm.health !== null) ? pkm.health : "-";
                    if(this.ui.pkmDmg) this.ui.pkmDmg.textContent = (pkm.damage !== undefined && pkm.damage !== null) ? pkm.damage : "-";

                    if (this.ui.pkmImage && pkm.type) {
                        const imageName = String(pkm.type).toLowerCase() + '.png';
                        const imageUrl = `${this.assetsBaseUrl}pokemons/${imageName}`;
                        this.ui.pkmImage.src = imageUrl;
                        this.ui.pkmImage.alt = pkm.name || "pokemon";
                        this.ui.pkmImage.style.display = 'block';
                        this.ui.pkmImage.onerror = () => {
                            console.warn(`Could not load image: ${this.ui.pkmImage.src}`);
                            this.ui.pkmImage.style.display = 'none';
                        };
                    } else if (this.ui.pkmImage) {
                        this.ui.pkmImage.style.display = 'none';
                    }
                    success = true; // Đánh dấu fetch thành công
                } else {
                    this.clearPlayerPokemonInfoUI("Không có Pokémon");
                    console.warn("Player info success, but no valid pokemon data for trainer:", trainer.name);
                }
            } else {
                console.warn("Failed to fetch player info or data incomplete:", result.message);
                this.clearPlayerPokemonInfoUI(result.message || "Chưa có dữ liệu");
            }
        } catch (error) {
            console.error("Exception during fetchAndUpdatePlayerPokemonInfo:", error);
            this.clearPlayerPokemonInfoUI("Lỗi kết nối");
            this.setGameMessage("Lỗi kết nối nghiêm trọng khi tải thông tin người chơi.");
        }
        return success; // Trả về trạng thái thành công/thất bại
    },

    clearPlayerPokemonInfoUI(reason = "-") {
        const displayReason = (reason === "-" || reason === "N/A" || reason === "Không tìm thấy thông tin Trainer hợp lệ trong session.") ? "-" : reason;
        if(this.ui.trainerNameDisplay) this.ui.trainerNameDisplay.textContent = displayReason;
        if(this.ui.pkmName) this.ui.pkmName.textContent = "-";
        if(this.ui.pkmType) this.ui.pkmType.textContent = "-";
        if(this.ui.pkmHp) this.ui.pkmHp.textContent = "-";
        if(this.ui.pkmDmg) this.ui.pkmDmg.textContent = "-";
        if(this.ui.pkmImage) {
            this.ui.pkmImage.src = "";
            this.ui.pkmImage.alt = "Chưa có ảnh";
            this.ui.pkmImage.style.display = 'none';
        }
    },
    
    async loadMapByName(mapName) {
        if (this.inBattle) { this.setGameMessage("Không thể chuyển map khi đang trong trận đấu!"); return; }
        
        try {
            const formData = new FormData();
            formData.append('action', 'update_current_map'); 
            formData.append('map_name', mapName);
            const mapUpdateResponse = await fetch('php_api/game_actions.php', { method: 'POST', body: formData }); 
            if(mapUpdateResponse.ok){
                const mapUpdateResult = await mapUpdateResponse.json(); // Luôn parse JSON để kiểm tra success
                if(mapUpdateResult.success) {
                    console.log(`Server updated current_map to ${mapName}`);
                     // Cập nhật currentMapName trên client SAU KHI server xác nhận
                    this.currentMapName = mapName;
                } else {
                    console.warn(`Server failed to update current_map: ${mapUpdateResult.message}`);
                    // Không nên thay đổi this.currentMapName nếu server không cập nhật được
                }
            } else { 
                const errorText = await mapUpdateResponse.text();
                console.error(`HTTP error updating current_map! Status: ${mapUpdateResponse.status}. Server response: ${errorText}`);
            }
        } catch (error) { console.error("Error updating current map on server:", error); }

        console.log(`Loading map data for: ${mapName}...`); // mapName vẫn là map được yêu cầu
        this.setGameMessage(`Đang tải ${mapName}...`);
        try {
            const response = await fetch(`php_api/map_data.php?action=get_map_data&map_name=${mapName}`);
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            const data = await response.json();
            if (data.success) {
                // this.currentMapName đã được cập nhật ở trên nếu server OK
                this.currentMapData = data.map_info;
                this.tileProperties = data.tile_properties;
                if (this.currentMapData.start_pos) {
                    this.player.x = this.currentMapData.start_pos.x;
                    this.player.y = this.currentMapData.start_pos.y;
                } else { this.player.x = 0; this.player.y = 0; console.warn(`Map ${mapName} is missing start_pos.`); }
                
                this.renderMap();
                this.updatePlayerPosition(); 
                this.updateUIDisplay();    
                this.setGameMessage(`Chào mừng tới ${this.currentMapData.name}`);
                this.playBackgroundMusic();
                console.log(`${mapName} loaded. Player at: (${this.player.x}, ${this.player.y})`);
            } else { console.error("Failed to load map data:", data.message); this.setGameMessage(`Lỗi tải map: ${data.message}`); }
        } catch (error) { console.error("Error fetching map data:", error); this.setGameMessage("Lỗi kết nối tới server để tải map."); }
    },
    
    getTileProperty(tileId, propertyName) { if (this.tileProperties && this.tileProperties[tileId]) { return this.tileProperties[tileId][propertyName]; } return undefined; },
    
    renderMap() { 
        if (!this.currentMapData || !this.mapGridDom || !this.tileProperties) { console.error("Map data for rendering not available."); return; } 
        this.mapGridDom.innerHTML = ''; this.clearBossSprites(); 
        const layout = this.currentMapData.layout; 
        if (!layout || layout.length === 0 || !Array.isArray(layout[0])) { console.error("Map layout invalid."); return; } 
        const numRows = layout.length; const numCols = layout[0].length; 
        this.mapGridDom.style.gridTemplateColumns = `repeat(${numCols}, ${this.tileSize}px)`; 
        this.mapGridDom.style.gridTemplateRows = `repeat(${numRows}, ${this.tileSize}px)`; 
        const gameContainer = document.getElementById('game-container'); 
        gameContainer.style.width = `${numCols * this.tileSize}px`; 
        gameContainer.style.height = `${numRows * this.tileSize}px`; 
        for (let r = 0; r < numRows; r++) { for (let c = 0; c < numCols; c++) { 
            const tileId = layout[r][c]; const tileDiv = document.createElement('div'); 
            tileDiv.classList.add('tile'); 
            const tileImagePath = this.getTileProperty(tileId, 'image'); 
            if (tileImagePath) { tileDiv.style.backgroundImage = `url('${this.assetsBaseUrl}tiles/${tileImagePath}')`; } 
            else { tileDiv.textContent = tileId; tileDiv.style.backgroundColor = '#ccc'; } 
            this.mapGridDom.appendChild(tileDiv); 
            if (this.getTileProperty(tileId, 'is_boss')) { 
                const bossSpriteName = this.getTileProperty(tileId, 'boss_sprite'); 
                if (bossSpriteName) { 
                    const bossDiv = document.createElement('div'); 
                    bossDiv.classList.add('boss-sprite'); 
                    bossDiv.style.backgroundImage = `url('${this.assetsBaseUrl}bosses/${bossSpriteName}')`; 
                    bossDiv.style.left = `${c * this.tileSize}px`; 
                    bossDiv.style.top = `${r * this.tileSize}px`; 
                    document.getElementById('game-container').appendChild(bossDiv); 
                    this.bossSpritesOnMap.push(bossDiv); 
                } 
            } 
        } } 
    },

    clearBossSprites() { this.bossSpritesOnMap.forEach(sprite => sprite.remove()); this.bossSpritesOnMap = []; },

    updatePlayerPosition() { 
        if (!this.player.domElement) return; 
        this.player.domElement.style.left = `${this.player.x * this.tileSize}px`; 
        this.player.domElement.style.top = `${this.player.y * this.tileSize}px`; 
        const playerSpriteFile = this.player.sprites[this.player.facing] || this.player.sprites.down; 
        this.player.domElement.style.backgroundImage = `url('${this.assetsBaseUrl}player/${playerSpriteFile}')`; 
        if (this.player.facing === 'right' && playerSpriteFile === this.player.sprites.left) { this.player.domElement.style.transform = 'scaleX(-1)'; } 
        else { this.player.domElement.style.transform = 'scaleX(1)'; } 
        this.updateUIDisplay(); 
    },

    updateUIDisplay() { 
        if (this.ui.mapDisplay && this.currentMapData) this.ui.mapDisplay.textContent = this.currentMapData.name; 
        if (this.ui.playerCoords) this.ui.playerCoords.textContent = `(${this.player.x}, ${this.player.y})`; 
    },
    
    setGameMessage(message) { if (this.ui.gameMessage) this.ui.gameMessage.textContent = message; console.log("Game Message:", message); },

    playBackgroundMusic() {
        if (this.inBattle) { // Không phát nhạc map nếu đang trong trận đấu
            if (this.audio.backgroundMusic && !this.audio.backgroundMusic.paused) {
                this.audio.backgroundMusic.pause();
            }
            return;
        }
        if (this.currentMapData && this.currentMapData.music && this.audio.backgroundMusic) { 
            try { 
                const musicUrl = new URL(this.currentMapData.music, window.location.origin + window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/') + 1)).href; 
                if (this.audio.backgroundMusic.src !== musicUrl) { this.audio.backgroundMusic.src = musicUrl; } 
                if (this.audio.backgroundMusic.paused) { // Chỉ play nếu đang paused
                    this.audio.backgroundMusic.play().catch(e => console.warn("Autoplay prevented for background music:", e.message)); 
                }
            } catch(e) { console.error("Error setting/playing background music URL:", e); } 
        } 
    },

    playSoundEffect(soundFile) { 
        if (this.audio.sfx) { 
            try { 
                const sfxUrl = new URL(soundFile, window.location.origin + window.location.pathname.substring(0, window.location.pathname.lastIndexOf('/') + 1)).href; 
                this.audio.sfx.src = sfxUrl; 
                this.audio.sfx.play().catch(e => console.warn("Autoplay prevented for SFX:", e.message)); 
            } catch(e) { console.error("Error setting/playing SFX URL:", e); } 
        } 
    },

    handleKeyPress(event) { 
        if (this.inBattle) { this.setGameMessage("Đang trong trận đấu, không thể di chuyển!"); return; } 
        let newX = this.player.x; let newY = this.player.y; let newFacing = this.player.facing; 
        switch (event.key.toLowerCase()) { 
            case 'arrowup': case 'w': newY--; newFacing = 'up'; break; 
            case 'arrowdown': case 's': newY++; newFacing = 'down'; break; 
            case 'arrowleft': case 'a': newX--; newFacing = 'left'; break; 
            case 'arrowright': case 'd': newX++; newFacing = 'right'; break; 
            default: return; 
        } 
        event.preventDefault(); this.player.facing = newFacing; 
        if (this.isValidMove(newX, newY)) { 
            this.player.x = newX; this.player.y = newY; 
            this.updatePlayerPosition(); 
            this.checkCurrentTileAction(); 
        } else { this.updatePlayerPosition(); this.setGameMessage("Không thể đi hướng đó!"); } 
    },

    isValidMove(x, y) { /* ... giữ nguyên ... */ 
        if (!this.currentMapData || !this.currentMapData.layout) return false; 
        const layout = this.currentMapData.layout; 
        if (y < 0 || y >= layout.length || x < 0 || x >= layout[0].length) return false; 
        const tileId = layout[y][x]; 
        return this.getTileProperty(tileId, 'walkable'); 
    },

    checkCurrentTileAction() { /* ... giữ nguyên ... */ 
        if (!this.currentMapData || !this.currentMapData.layout) return; 
        const tileId = this.currentMapData.layout[this.player.y][this.player.x]; 
        if (this.getTileProperty(tileId, 'is_boss')) { 
            const bossId = this.getTileProperty(tileId, 'boss_id'); 
            this.setGameMessage(`Chạm trán boss ${bossId}!`); 
            this.playSoundEffect('../assets/audio/encounter_sfx.mp3'); 
            this.initiateBattle('trigger_boss_battle', { boss_id: bossId }); 
            return; 
        } 
        const encounterChance = this.getTileProperty(tileId, 'encounter_chance'); 
        if (encounterChance > 0) { 
            if (Math.random() * 100 < encounterChance) { 
                this.setGameMessage("Gặp Pokemon hoang dã!"); 
                this.playSoundEffect('../assets/audio/encounter_sfx.mp3'); 
                this.initiateBattle('trigger_wild_encounter'); 
            } 
        } 
    },

    async initiateBattle(apiAction, params = {}) {
        if (this.inBattle) { // Kiểm tra lại để tránh gọi nhiều lần
            console.warn("Already attempting to initiate battle or in battle.");
            return;
        }
        
        this.setGameMessage("Đang chuẩn bị trận đấu...");
        // Không set this.inBattle = true; ở đây ngay lập tức
        // Chỉ set sau khi server xác nhận và trước khi chuyển hướng

        const formData = new FormData();
        formData.append('action', apiAction);
        for (const key in params) {
            formData.append(key, params[key]);
        }

        try {
            const response = await fetch('php_api/game_actions.php', { method: 'POST', body: formData });
            if (!response.ok) {
                 const errorText = await response.text();
                 throw new Error(`HTTP error! status: ${response.status}, response: ${errorText}`);
            }
            const result = await response.json();

            if (result.success && result.player_pokemon && result.enemy_pokemon) {
                console.log("Battle initiated successfully with server. Data:", result);
                // Dữ liệu trận đấu (result) đã được server lưu vào $_SESSION['player_pokemon'] và $_SESSION['enemy']
                // Không cần thiết phải lưu vào sessionStorage của client nữa nếu battle_ui.js sẽ fetch lại.
                // Tuy nhiên, nếu muốn truyền trực tiếp thì sessionStorage là một cách.
                // sessionStorage.setItem('currentBattleData', JSON.stringify(result)); 
                
                this.inBattle = true; 
                if(this.audio.backgroundMusic && !this.audio.backgroundMusic.paused) {
                    this.audio.backgroundMusic.pause(); 
                }
                sessionStorage.setItem('mapBeforeBattle', this.currentMapName); // Lưu map hiện tại để quay lại
                window.location.href = 'battle.html'; 
            } else {
                this.setGameMessage(`Lỗi bắt đầu trận đấu: ${result.message || 'Dữ liệu không hợp lệ từ server.'}`);
                this.inBattle = false; 
            }
        } catch (error) {
            console.error("Error initiating battle:", error);
            this.setGameMessage("Lỗi kết nối server hoặc dữ liệu không hợp lệ khi bắt đầu trận đấu.");
            this.inBattle = false;
        }
    },
    
    // Các hàm showBattleOptions, handleBattleAction, endCurrentBattleOnServer, endCurrentBattle
    // sẽ được chuyển hoàn toàn sang js/battle_ui.js
    // Tuy nhiên, chúng ta cần một hàm để xử lý khi quay lại từ battle.html
    async resumeGameAfterBattle() {
        console.log("Resuming game after battle...");
        this.inBattle = false; // Đặt lại cờ
        // Tải lại thông tin người chơi (vì HP, DMG có thể đã thay đổi, Pokémon có thể lên cấp)
        const playerInfoUpdated = await this.fetchAndUpdatePlayerPokemonInfo();
        if (playerInfoUpdated) {
            this.setGameMessage("Trận đấu đã kết thúc. Chào mừng trở lại!");
        } else {
            this.setGameMessage("Trận đấu kết thúc, nhưng có lỗi khi cập nhật thông tin người chơi.");
        }
        this.playBackgroundMusic(); // Chơi lại nhạc nền map
    }
};

// Xử lý khi trang tải lại sau khi từ battle.html quay về
// battle_ui.js sẽ đặt flag này trước khi chuyển hướng về index.html
if (sessionStorage.getItem('returnedFromBattle')) {
    sessionStorage.removeItem('returnedFromBattle');
    // Chờ game object được tạo rồi mới gọi resume
    window.addEventListener('DOMContentLoaded', () => {
        // init có thể đã chạy, gọi resume trực tiếp
        // game.init() sẽ được gọi bởi window.onload ở dưới
        // Thay vào đó, chúng ta sẽ gọi resume sau khi init đã hoàn tất
        // Hoặc, game.init() sẽ tự kiểm tra và gọi resume
    });
}

window.onload = () => {
    console.log("Window loaded, preparing to initialize game...");
    game.init(); // init bây giờ đã bao gồm logic kiểm tra 'returnedFromBattle'
};