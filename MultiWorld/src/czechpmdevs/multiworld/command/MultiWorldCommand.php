<?php

/**
 * MultiWorld - PocketMine plugin that manages worlds.
 * Copyright (C) 2018 - 2019  CzechPMDevs
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

declare(strict_types=1);

namespace czechpmdevs\multiworld\command;

use czechpmdevs\multiworld\command\subcommand\CreateSubcommand;
use czechpmdevs\multiworld\command\subcommand\DeleteSubcommand;
use czechpmdevs\multiworld\command\subcommand\GameruleSubcommand;
use czechpmdevs\multiworld\command\subcommand\HelpSubcommand;
use czechpmdevs\multiworld\command\subcommand\InfoSubcommand;
use czechpmdevs\multiworld\command\subcommand\ListSubcommand;
use czechpmdevs\multiworld\command\subcommand\LoadSubcommand;
use czechpmdevs\multiworld\command\subcommand\ManageSubcommand;
use czechpmdevs\multiworld\command\subcommand\RenameSubcommand;
use czechpmdevs\multiworld\command\subcommand\SubCommand;
use czechpmdevs\multiworld\command\subcommand\TeleportSubcommand;
use czechpmdevs\multiworld\command\subcommand\UnloadSubcommand;
use czechpmdevs\multiworld\command\subcommand\UpdateSubcommand;
use czechpmdevs\multiworld\MultiWorld;
use czechpmdevs\multiworld\util\LanguageManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\Server;

/**
 * Class MultiWorldCommand
 * @package czechpmdevs\multiworld\Command
 */
class MultiWorldCommand extends Command implements PluginIdentifiableCommand {

    /** @var  MultiWorld $plugin */
    public $plugin;

    /** @var SubCommand[] $subcommands */
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
        $this->subcommands["gamerule"] = new GameruleSubcommand;
        $this->subcommands["manage"] = new ManageSubcommand;
        $this->subcommands["rename"] = new RenameSubcommand;
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     *
     * @return mixed|void
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args) {
        if(!isset($args[0])) {
            if($sender->hasPermission("mw.cmd")) {
                $sender->sendMessage(LanguageManager::getMsg($sender, "default-usage"));
                return;
            }
            $sender->sendMessage(LanguageManager::getMsg($sender, "not-perms"));
            return;
        }


        if($this->getSubcommand($args[0]) === null) {
            $sender->sendMessage(LanguageManager::getMsg($sender, "default-usage"));
            return;
        }

        if(!$this->checkPerms($sender, $args[0])) {
            $sender->sendMessage(LanguageManager::getMsg($sender, "not-perms"));
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
     *
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
            case "gamerule":
            case "gr":
            case "gamer":
            case "grule":
                return "gamerule";
            case "manage":
            case "mng":
            case "mg":
                return "manage";
            case "rename":
            case "rnm":
            case "re":
                return "rename";
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
            if(!$sender->hasPermission("mw.cmd." . $this->getSubcommand($command))) {
                $sender->sendMessage(LanguageManager::getMsg($sender, "not-perms"));
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
    public function getServer(): Server {
        return Server::getInstance();
    }

    /**
     * @return Plugin|MultiWorld $multiWorld
     */
    public function getPlugin(): Plugin {
        return MultiWorld::getInstance();
    }
}