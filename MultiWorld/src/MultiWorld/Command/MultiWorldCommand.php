<?php

namespace MultiWorld\Command;

use MultiWorld\Command\Commands\CreateCommand;
use MultiWorld\Command\Commands\HelpCommand;
use MultiWorld\Command\Commands\ImportCommand;
use MultiWorld\Command\Commands\ListCommand;
use MultiWorld\Command\Commands\LoadCommand;
use MultiWorld\Command\Commands\TeleportCommand;
use MultiWorld\Command\Commands\UnloadCommand;
use MultiWorld\MultiWorld;
use MultiWorld\Util\LanguageManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;

class MultiWorldCommand {

    /** @var MultiWorld  */
    public $plugin;

    /** @var  HelpCommand */
    public $helpcmd;

    /** @var  CreateCommand */
    public $createcmd;

    /** @var  TeleportCommand */
    public $teleportcmd;

    /** @var  ImportCommand */
    public $importcmd;

    /** @var  ListCommand */
    public $listcmd;

    /** @var  LoadCommand */
    public $loadcmd;

    /** @var  UnloadCommand */
    public $unloadcmd;

    public function __construct(MultiWorld $plugin) {
        $this->plugin = $plugin;
    }

    public function initCommands() {
        $this->helpcmd = new HelpCommand(MultiWorld::getInstance(), $this);
        $this->createcmd = new CreateCommand(MultiWorld::getInstance(), $this);
        $this->teleportcmd = new TeleportCommand(MultiWorld::getInstance(), $this);
        $this->importcmd = new ImportCommand(MultiWorld::getInstance(), $this);
    }

    /**
     * @param string $alias
     * @return string $command
     */
    public function getCommandByAlias($alias) {
        switch ($alias) {
            case "?":
            case "help":
                return "help";
            case "create":
            case "add":
            case "generate":
                return "create";
            case "teleport":
            case "tp":
            case "move":
                return "move";
            case "import":
                return "import";
            case "list":
            case "ls":
            case "worlds":
            case "levels":
                return "list";
            case "load":
                return "load";
            case "unload":
                return "unload";
            default:
                return "help";

        }
    }

    /**
     * @param Command $cmd
     * @param CommandSender $sender
     * @param array $args
     * @return bool
     */
    public function onCommand(CommandSender $sender, Command $cmd, array $args) {
        if(!($sender instanceof Player)) {
            return false;
        }
        if(!$sender->hasPermission("mw.cmd.".$this->getCommandByAlias($args[0]))) {
            $sender->sendMessage(LanguageManager::translateMessage("not-perms"));
            return false;
        }
        if($cmd->getName() == "multiworld") {
            if(empty($args[0])) {
                if($sender->hasPermission("mw.cmd.help")) {
                    $sender->sendMessage("default-usage");
                }
                else {
                    $sender->sendMessage(LanguageManager::translateMessage("not-perms"));
                }
                return false;
            }
            switch (strtolower($args[0])) {
                case "help":
                case "?":
                    $this->helpcmd->execute($sender, $args);
                    break;
                case "create":
                case "add":
                case "generate":
                    $this->createcmd->execute($sender, $args);
                    break;
                case "teleport":
                case "tp":
                case "move":
                    $this->teleportcmd->execute($sender, $args);
                    break;
                case "import":
                    $this->importcmd->execute($sender, $args);
                    break;
                case "list":
                case "ls":
                case "levels":
                case "worlds":
                    $this->listcmd->execute($sender, $args);
                    break;
                case "load":
                    $this->loadcmd->execute($sender, $args);
                    break;
                case "unload":
                    $this->unloadcmd->execute($sender, $args);
                    break;
            }
        }
    }
}