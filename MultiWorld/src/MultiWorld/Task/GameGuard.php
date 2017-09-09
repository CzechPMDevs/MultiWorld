<?php

namespace MultiWorld\Task;

use MultiWorld\MultiWorld;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\scheduler\PluginTask;

class GameGuard extends PluginTask {

    /** @var MultiWorld */
    public $plugin;

    /**
     * GameGuard constructor.
     * @param MultiWorld $plugin
     */
    public function __construct(MultiWorld $plugin) {
        $this->plugin = $plugin;
        parent::__construct($plugin);
    }

    public function onRun(int $currentTick) {
        $configData = $this->plugin->configmgr->configData;
        $survWorlds = $configData["survivalWorlds"];
        $creaWorlds = $configData["creaWorlds"];
        $adveWorlds = $configData["adventureWorlds"];
        $specWorlds = $configData["spectatorWorlds"];
        foreach ($this->plugin->getServer()->getLevels() as $level) {
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
    function changeGamemode(Level $level, int $gamemode) {
        foreach ($level->getPlayers() as $player) {
            if(!$player->hasPermission("mw.gm")) {
                $player->setGamemode($gamemode);
            }
        }
    }
}