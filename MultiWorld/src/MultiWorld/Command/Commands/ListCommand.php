<?php

namespace MultiWorld\Command\Commands;

use MultiWorld\Command\MultiWorldCommand;
use MultiWorld\MultiWorld;
use MultiWorld\Util\ConfigManager;
use MultiWorld\Util\LanguageManager;
use pocketmine\Player;

class ListCommand {

    /** @var  MultiWorld */
    public $plugin;

    /** @var  MultiWorldCommand */
    public $command;

    public function __construct(MultiWorld $plugin, MultiWorldCommand $command) {
        $this->plugin = $plugin;
        $this->command = $command;
    }

    /**
     * @param Player $sender
     * @param array $args
     * @return bool;
     */
    public function execute(Player $sender, array $args) {
        $list = scandir(ConfigManager::getDataPath()."worlds");
        unset($list[0]);
        unset($list[1]);
        $list = implode(", ", $list);
        $sender->sendMessage(MultiWorld::getPrefix().str_replace("%1", $list, LanguageManager::translateMessage("list-done")));
    }
}