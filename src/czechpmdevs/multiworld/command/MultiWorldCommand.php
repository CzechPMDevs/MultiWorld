<?php

/**
 * MultiWorld - PocketMine plugin that manages worlds.
 * Copyright (C) 2018 - 2021  CzechPMDevs
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

use czechpmdevs\multiworld\command\subcommand\CreateSubCommand;
use czechpmdevs\multiworld\command\subcommand\DeleteSubCommand;
use czechpmdevs\multiworld\command\subcommand\DuplicateSubCommand;
use czechpmdevs\multiworld\command\subcommand\HelpSubCommand;
use czechpmdevs\multiworld\command\subcommand\InfoSubCommand;
use czechpmdevs\multiworld\command\subcommand\ListSubCommand;
use czechpmdevs\multiworld\command\subcommand\LoadSubCommand;
use czechpmdevs\multiworld\command\subcommand\ManageSubCommand;
use czechpmdevs\multiworld\command\subcommand\RenameSubCommand;
use czechpmdevs\multiworld\command\subcommand\SubCommand;
use czechpmdevs\multiworld\command\subcommand\TeleportSubCommand;
use czechpmdevs\multiworld\command\subcommand\UnloadSubCommand;
use czechpmdevs\multiworld\command\subcommand\UpdateSubCommand;
use czechpmdevs\multiworld\MultiWorld;
use czechpmdevs\multiworld\util\LanguageManager;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginIdentifiableCommand;
use pocketmine\Player;
use pocketmine\plugin\Plugin;
use pocketmine\Server;

class MultiWorldCommand extends Command implements PluginIdentifiableCommand {

	/** @var  MultiWorld */
	public MultiWorld $plugin;

	/** @var SubCommand[] */
	public array $subcommands = [];

	public function __construct() {
		parent::__construct("multiworld", "MultiWorld commands", null, ["mw"]);
		$this->plugin = MultiWorld::getInstance();
		$this->registerSubcommands();
	}

	public function registerSubcommands(): void {
		$this->subcommands["create"] = new CreateSubCommand;
		$this->subcommands["delete"] = new DeleteSubCommand;
		$this->subcommands["duplicate"] = new DuplicateSubCommand;
		$this->subcommands["help"] = new HelpSubCommand;
		$this->subcommands["info"] = new InfoSubCommand;
		$this->subcommands["list"] = new ListSubCommand;
		$this->subcommands["load"] = new LoadSubCommand;
		$this->subcommands["manage"] = new ManageSubCommand;
		$this->subcommands["rename"] = new RenameSubCommand;
		$this->subcommands["teleport"] = new TeleportSubCommand;
		$this->subcommands["unload"] = new UnloadSubCommand;
		$this->subcommands["update"] = new UpdateSubCommand;
	}

	public function execute(CommandSender $sender, string $commandLabel, array $args) {
		if(!isset($args[0])) {
			if($sender->hasPermission("mw.cmd")) {
				$sender->sendMessage(LanguageManager::translateMessage($sender, "default-usage"));
				return;
			}
			$sender->sendMessage(LanguageManager::translateMessage($sender, "not-perms"));
			return;
		}

		$subCommandName = $this->getSubCommandNameByAlias($args[0]);
		if($subCommandName === null) {
			$sender->sendMessage(LanguageManager::translateMessage($sender, "default-usage"));
			return;
		}

		$subCommand = $this->subcommands[$subCommandName] ?? null;
		if($subCommand === null) {
			$sender->sendMessage(LanguageManager::translateMessage($sender, "default-usage"));
			return;
		}

		if(!$this->checkPerms($sender, $args[0])) {
			$sender->sendMessage(LanguageManager::translateMessage($sender, "not-perms"));
			return;
		}

		array_shift($args);
		$subCommand->execute($sender, $args, $subCommandName);
	}

	public function getSubCommandNameByAlias(string $alias): ?string {
		switch($alias) {
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
			case "duplicate":
			case "copy":
			case "cp":
				return "duplicate";
		}
		return null;
	}

	public function checkPerms(CommandSender $sender, string $command): bool {
		if($sender instanceof Player) {
			if(!$sender->hasPermission("mw.cmd." . $this->getSubCommandNameByAlias($command))) {
				$sender->sendMessage(LanguageManager::translateMessage($sender, "not-perms"));
				return false;
			} else {
				return true;
			}
		} else {
			return true;
		}
	}

	public function getServer(): Server {
		return Server::getInstance();
	}

	public function getPlugin(): Plugin {
		return MultiWorld::getInstance();
	}
}