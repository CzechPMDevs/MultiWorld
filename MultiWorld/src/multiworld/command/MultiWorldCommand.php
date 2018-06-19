<?php

/**
 * MultiWorld - PocketMine plugin that manages worlds.
 * Copyright (C) 2018  CzechPMDevs
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
use multiworld\util\LanguageManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\level\generator\Generator;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AvailableCommandsPacket;
use pocketmine\network\mcpe\protocol\types\CommandData;
use pocketmine\network\mcpe\protocol\types\CommandEnum;
use pocketmine\network\mcpe\protocol\types\CommandParameter;
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

    public function sendCommandParameters(Player $player) {
        $pk = new AvailableCommandsPacket;

        $data = new CommandData;
        $data->commandName = $this->getName();
        $data->commandDescription = $this->getDescription();

        $data->aliases = new CommandEnum;
        $data->aliases->enumName = "MultiWorld Aliases";
        $data->aliases->enumValues = $this->getAliases();

        $parameter = new CommandParameter;
        $parameter->paramName = "subcommand";
        $parameter->paramType = $pk::ARG_TYPE_STRING;
        $parameter->isOptional = true;

        $data->overloads[0][0] = $parameter;

        $pk->commandData[$this->getName()] = $data;
        $player->dataPacket($pk);
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