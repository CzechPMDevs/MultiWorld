<?php

namespace MultiWorld\Command\Commands;

use MultiWorld\Command\MultiWorldCommand;
use MultiWorld\MultiWorld;
use MultiWorld\Util\LanguageManager;
use pocketmine\Player;
use pocketmine\Server;

class UnoadCommand {

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
        if(isset($args[1])) {
            if(Server::getInstance()->isLevelGenerated($args[1])) {
                if(Server::getInstance()->isLevelLoaded($args[1])) {
                    Server::getInstance()->unloadLevel(Server::getInstance()->getLevelByName($args[1]));
                    $sender->sendMessage(MultiWorld::getPrefix().str_replace("%1", $args[1], LanguageManager::translateMessage("unload-done")));

                }
                else {
                    $sender->sendMessage(MultiWorld::getPrefix().str_replace("%1", $args[1], LanguageManager::translateMessage("unload-unloaded")));
                }
            }
            else {
                $sender->sendMessage(MultiWorld::getPrefix().str_replace("%1", $args[1], LanguageManager::translateMessage("unload-levelnotexists")));
            }
        }
        else {
            $sender->sendMessage(MultiWorld::getPrefix().LanguageManager::translateMessage("unload-usage"));
        }
    }
}