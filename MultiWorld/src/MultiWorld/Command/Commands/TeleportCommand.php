<?php

namespace MultiWorld\Command\Commands;

use MultiWorld\Command\MultiWorldCommand;
use MultiWorld\MultiWorld;
use MultiWorld\Util\LanguageManager;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\Server;

class TeleportCommand {

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
     * @return bool
     */
    public function execute(Player $sender, array $args) {
        if(isset($args[1])) {
            if(Server::getInstance()->isLevelGenerated($args[1])) {
                if(!Server::getInstance()->isLevelLoaded($args[1])) {
                    Server::getInstance()->loadLevel($args[1]);
                    MultiWorld::getInstance()->getLogger()->debug(MultiWorld::getPrefix().str_replace("%1", $args[1], LanguageManager::translateMessage("teleport-load")));
                }
                if(isset($args[2])) {
                    $player = Server::getInstance()->getPlayer($args[2]);
                    if($player->isOnline()) {
                        $player->teleport(Server::getInstance()->getLevelByName($args[1])->getSafeSpawn(), 0, 0);
                        $player->sendMessage(MultiWorld::getPrefix().str_replace("%1", $args[1], LanguageManager::translateMessage("teleport-done-1")));
                        $sender->sendMessage(MultiWorld::getPrefix().str_replace("%1", $args[1], str_replace("%2", $player->getName(), LanguageManager::translateMessage("teleport-done-2"))));
                    }
                    else {
                        $sender->sendMessage(MultiWorld::getPrefix().LanguageManager::translateMessage("teleport-playernotexists"));
                    }
                }
                else {
                    $sender->teleport(Server::getInstance()->getLevelByName($args[1])->getSafeSpawn(), 0, 0);
                    $sender->sendMessage(MultiWorld::getPrefix().str_replace("%1", $args[1], LanguageManager::translateMessage("teleport-done-1")));
                }

            }
            else {
                $sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage("teleport-levelnotexists"));
            }
        }
        else {
            $sender->sendMessage(MultiWorld::getPrefix().LanguageManager::translateMessage("teleport-usage"));
        }



    }
}