<?php

declare(strict_types=1);

namespace multiworld\command;

use multiworld\MultiWorld;
use multiworld\util\LanguageManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\plugin\Plugin;

/**
 * Class GameruleCommand
 * @package multiworld\command
 */
class GameruleCommand extends Command implements PluginIdentifiableCommand {

    /**
     * GameruleCommand constructor.
     */
    public function __construct() {
        parent::__construct("gamerule", "Edit level gamerules", null, []);
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     *
     * @return mixed|void
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if($sender->hasPermission("mw.cmd.gamerule")) {
            /** @var MultiWorldCommand $mwCmd */
            $mwCmd = $this->getPlugin()->commands["multiworld"];
            $mwCmd->subcommands["gamerule"]->executeSub($sender, $args, "gamerule");
        }
        else {
            $sender->sendMessage(LanguageManager::getMsg($sender, "not-perms"));
        }
    }


    /**
     * @return Plugin|MultiWorld $plugin
     */
    public function getPlugin(): Plugin {
        return MultiWorld::getInstance();
    }
}