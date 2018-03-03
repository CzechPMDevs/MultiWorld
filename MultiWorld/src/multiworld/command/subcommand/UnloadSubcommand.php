<?php

declare(strict_types=1);

namespace multiworld\command\subcommand;

use multiworld\command\MultiWorldCommand;
use multiworld\MultiWorld;
use multiworld\util\LanguageManager;
use pocketmine\command\CommandSender;

/**
 * Class UnloadSubcommand
 * @package multiworld\command\subcommand
 */
class UnloadSubcommand extends MultiWorldCommand implements SubCommand {

    /**
     * UnloadSubcommand constructor.
     */
    public function __construct() {}

    /**
     * @param CommandSender $sender
     * @param array $args
     * @param string $name
     *
     * @return mixed|void
     */
    public function executeSub(CommandSender $sender, array $args, string $name) {
        if (empty($args[0])) {
            $sender->sendMessage(LanguageManager::translateMessage("unload-usage"));
            return;
        }

        if(!$this->getServer()->isLevelGenerated($args[0])) {
            $sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage("unload-levelnotexists"));
            return;
        }

        if(!$this->getServer()->isLevelLoaded($args[0])) {
            $sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage("unload-unloaded"));
            return;
        }

        $this->getServer()->unloadLevel($this->getServer()->getLevelByName($args[0]));
        $sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage("unload-done"));
        return;
    }
}
