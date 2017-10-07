<?php

declare(strict_types=1);

namespace multiworld\util;

use multiworld\task\MultiWorldTask;
use pocketmine\level\Level;
use pocketmine\Server;

/**
 * Class DataTask
 * @package multiworld\util
 */
class DataTask extends MultiWorldTask {

    /** @var  Data $data */
    public $data;

    /**
     * DataTask constructor.
     * @param Data $data
     */
    public function __construct(Data $data) {
        $this->data = $data;
    }

    /**
     * @param int $currentTick
     */
    public function onRun(int $currentTick) {
        $this->checkGamemode();
    }

    public function checkGamemode() {
        $level = Server::getInstance()->getLevelByName($this->getData()->getLevelName());
        if(!$level instanceof Level) return;
        foreach ($level->getPlayers() as $player) {
            if(!$player->hasPermission("mw.gamerule.gamemode")) {
                if($player->getGamemode() != $this->getData()->getGameMode()) {
                    $player->setGamemode($this->getData()->getGameMode());
                }
            }
        }
    }

    /**
     * @return Data
     */
    public function getData():Data {
        return $this->data;
    }
}