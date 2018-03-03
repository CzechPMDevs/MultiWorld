<?php

declare(strict_types=1);

namespace multiworld\command;

use multiworld\command\subcommand\CreateSubcommand;
use multiworld\command\subcommand\DeleteSubcommand;
use multiworld\command\subcommand\HelpSubcommand;
use multiworld\command\subcommand\InfoSubcommand;
use multiworld\command\subcommand\ListSubcommand;
use multiworld\command\subcommand\LoadSubcommand;
use multiworld\command\subcommand\SubCommand;
use multiworld\command\subcommand\TeleportSubcommand;
use multiworld\command\subcommand\UnloadSubcommand;
use multiworld\command\subcommand\UpdateSubcommand;
use multiworld\MultiWorld;
use multiworld\util\ConfigManager;
use multiworld\util\LanguageManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\level\generator\Generator;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\Server;

/**
 * Class MultiWorldCommand
 * @package multiworld\Command
 */
class MultiWorldCommand extends Command implements PluginIdentifiableCommand {

    /** @var  MultiWorld $plugin */
    public $plugin;

    /** @var array $subcommands */
    public $subcommands = [];

    /**
     * MultiWorldCommand constructor.
     */
    public function __construct() {
        parent::__construct("multiworld", "MultiWorld commands", null, ["mw"]);
        $this->plugin = MultiWorld::getInstance();
        $this->registerSubcommands();
    }

    public function registerSubcommands() {
        $this->subcommands["help"] = new HelpSubcommand;
        $this->subcommands["create"] = new CreateSubcommand;
        $this->subcommands["teleport"] = new TeleportSubcommand;
        $this->subcommands["list"] = new ListSubcommand;
        $this->subcommands["load"] = new LoadSubcommand;
        $this->subcommands["unload"] = new UnloadSubcommand;
        $this->subcommands["delete"] = new DeleteSubcommand;
        $this->subcommands["update"] = new UpdateSubcommand;
        $this->subcommands["info"] = new InfoSubcommand;
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(empty($args[0])) {
            if($sender->hasPermission("mw.cmd")) {
                $sender->sendMessage(LanguageManager::translateMessage("default-usage"));
                return;
            }
            $sender->sendMessage(LanguageManager::translateMessage("not-perms"));
            return;
        }

        if($this->getSubcommand($args[0]) === null) {
            $sender->sendMessage(LanguageManager::translateMessage("default-usage"));
            return;
        }

        if(!$this->checkPerms($sender, $args[0])) {
            $sender->sendMessage("not-perms");
            return;
        }

        $name = $args[0];

        array_shift($args);

        /** @var SubCommand $subCommand */
        $subCommand = $this->subcommands[$this->getSubcommand($name)];

        $subCommand->executeSub($sender, $args, $this->getSubcommand($name));
    }

    /**
     * @param string $name
     * @return string|null $name
     */
    public function getSubcommand(string $name) {
        switch ($name) {
            case "help":
            case "?":
                return "help";
            case "create":
            case "generate":
            case "new":
                return "create";
            case "tp":
            case "teleport":
            case "move":
                return "teleport";
            case "list":
            case "ls":
                return "list";
            case "load":
            case "ld":
                return "load";
            case "unload":
            case "unld":
                return "unload";
            case "remove":
            case "delete":
            case "rm":
            case "del":
            case "dl":
                return "delete";
            case "update":
            case "ue":
                return "update";
            case "info":
            case "i":
                return "info";
        }
        return null;
    }

    /**
     * @param CommandSender $sender
     * @param string $command
     * @return bool
     */
    public function checkPerms(CommandSender $sender, string $command):bool {
        if($sender instanceof Player) {
            if(!$sender->hasPermission("mw.cmd.{$command}")) {
                $sender->sendMessage(LanguageManager::translateMessage("not-perms"));
                return false;
            } else {
                return true;
            }
        } else {
            return true;
        }
    }

    /**
     * @return Server
     */
    public function getServer():Server {
        return Server::getInstance();
    }

    /**
     * @return Plugin|MultiWorld $multiWorld
     */
    public function getPlugin(): Plugin {
        return MultiWorld::getInstance();
    }
}