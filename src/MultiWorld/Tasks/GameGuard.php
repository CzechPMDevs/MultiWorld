<?php

namespace MultiWorld\Tasks;

use MultiWorld\MultiWorld;
use MultiWorld\WorldManager;
use pocketmine\scheduler\PluginTask;

class GameGuard extends PluginTask {

    /** @var  MultiWorld */
    public $plugin;

    /** @var  WorldManager */
    public $manager;

    public function __construct($plugin) {
        $this->plugin = $plugin;
        $this->manager = $this->plugin->manager;
        parent::__construct($plugin);
    }

    public function onRun($currentTick) {
        $levels = $this->plugin->getConfig()->get("creative-levels");
        foreach ($this->plugin->getServer()->getOnlinePlayers() as $player) {
            $level = $player->getLevel();
            if (in_array($level->getName(), $levels)) {
                if ($player->getGamemode() == $player::SURVIVAL) {
                    $player->setGamemode($player::CREATIVE);
                }
            } else {
                if (!$player->hasPermission("wm.gm.creative") && $player->getGamemode() == $player::CREATIVE) {
                    $player->setGamemode($player::SURVIVAL);
                }
            }
        }
    }
}
