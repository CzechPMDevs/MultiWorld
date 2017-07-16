<?php

namespace MultiWorld\Command\Commands;

use MultiWorld\Command\MultiWorldCommand;
use MultiWorld\MultiWorld;
use MultiWorld\Util\LanguageManager;
use pocketmine\Player;

class HelpCommand {

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
        $sender->sendMessage(LanguageManager::translateMessage("help-0")."\n".
            LanguageManager::translateMessage("help-1")."\n".
            LanguageManager::translateMessage("help-2")."\n".
            LanguageManager::translateMessage("help-3")."\n".
            LanguageManager::translateMessage("help-4")."\n");
    }
}