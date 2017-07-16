<?php

namespace MultiWorld\Command\Commands;

use MultiWorld\Command\MultiWorldCommand;
use MultiWorld\MultiWorld;
use MultiWorld\Util\LanguageManager;
use pocketmine\command\CommandSender;

class CreateCommand {

    /** @var  MultiWorld */
    public $plugin;

    /** @var  MultiWorldCommand */
    public $command;

    public function __construct(MultiWorld $plugin, MultiWorldCommand $command) {
        $this->plugin = $plugin;
        $this->command = $command;
    }

    /**
     * @param CommandSender $sender
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $sender, array $args) {
        if(empty($args[1])) {
            $sender->sendMessage(MultiWorld::getPrefix().LanguageManager::translateMessage("create-usage"));
        }
        else {
            MultiWorld::getBasicGenerator()->generateLevel($args[1], $args[2], $args[3]);
            $sender->sendMessage(MultiWorld::getPrefix().str_replace("%1", $args[1], LanguageManager::translateMessage("create.generating")));
        }
    }
}