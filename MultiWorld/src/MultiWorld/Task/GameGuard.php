<?php

namespace multiworld\task;

use pocketmine\level\Level;
use pocketmine\Player;

class GameGuard extends MultiWorldTask {

    public function onRun(int $currentTick) {
        $configData = $this->getPlugin()->getConfigManager()->configData;
        $survWorlds = $configData["survivalWorlds"];
        $creaWorlds = $configData["creaWorlds"];
        $adveWorlds = $configData["adventureWorlds"];
        $specWorlds = $configData["spectatorWorlds"];
        foreach ($this->getPlugin()->getServer()->getLevels() as $level) {
            if(in_array($level->getName(), $survWorlds)) {
                $this->changeGamemode($level, Player::SURVIVAL);
            }
            if(in_array($level->getName(), $creaWorlds)) {
                $this->changeGamemode($level, Player::CREATIVE);
            }
            if(in_array($level->getName(), $adveWorlds)) {
                $this->changeGamemode($level, Player::ADVENTURE);
            }
            if(in_array($level->getName(), $specWorlds)) {
                $this->changeGamemode($level, Player::SPECTATOR);
            }
        }
    }

    /**
     * @param Level $level
     * @param int $gamemode
     */
    public function changeGamemode(Level $level, int $gamemode) {
        foreach ($level->getPlayers() as $player) {
            if(!$player->hasPermission("mw.gm")) {
                $player->setGamemode($gamemode);
            }
        }
    }
}