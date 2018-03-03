<?php

declare(strict_types=1);

namespace multiworld\command\subcommand;

use multiworld\command\MultiWorldCommand;
use multiworld\MultiWorld;
use multiworld\util\LanguageManager;
use pocketmine\command\CommandSender;

/**
 * Class LoadSubcommand
 * @package multiworld\command\subcommand
 */
class LoadSubcommand extends MultiWorldCommand implements SubCommand {

    /**
     * LoadSubcommand constructor.
     */
    public function __construct() {}

    /**
     * @param CommandSender $sender
     * @param array $args
     * @param string $name
     * @return mixed|void
     */
    public function executeSub(CommandSender $sender, array $args, string $name) {
        if (empty($args[0])) {
            $sender->sendMessage(LanguageManager::translateMessage("load-usage"));
            return;
        }

        if(!$this->getServer()->isLevelGenerated($args[0])) {
            $sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage("load-levelnotexists"));
            return;
        }

        if($this->getServer()->isLevelLoaded($args[0])) {
            $sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage("load-loaded"));
            return;
        }

        $this->getServer()->loadLevel($args[0]);
        $sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage("load-done"));
        return;
    }
}
