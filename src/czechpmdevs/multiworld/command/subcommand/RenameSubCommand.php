<?php

/**
 * MultiWorld - PocketMine plugin that manages worlds.
 * Copyright (C) 2018 - 2023  CzechPMDevs
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

namespace czechpmdevs\multiworld\command\subcommand;

use CortexPE\Commando\args\RawStringArgument;
use CortexPE\Commando\BaseSubCommand;
use czechpmdevs\multiworld\MultiWorld;
use czechpmdevs\multiworld\util\LanguageManager;
use czechpmdevs\multiworld\util\WorldUtils;
use pocketmine\command\CommandSender;
use pocketmine\Server;
use RuntimeException;

class RenameSubCommand extends BaseSubCommand {
	protected function prepare(): void {
		$this->registerArgument(0, new RawStringArgument("worldName"));
		$this->registerArgument(1, new RawStringArgument("newName"));

		$this->setPermission("multiworld.command.rename");
	}
	/**
	 * @param array<string, mixed> $args
	 */
	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		/** @var string $worldName */
		$worldName = $args["worldName"];
		/** @var string $newName */
		$newName = $args["newName"];

		if(Server::getInstance()->getWorldManager()->isWorldGenerated($newName)) {
			$sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage($sender, "rename-exists", [$newName]));
			return;
		}

		if(!Server::getInstance()->getWorldManager()->isWorldGenerated($worldName)) {
			$sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage($sender, "rename-levelnotfound", [$worldName]));
			return;
		}

		if(WorldUtils::getDefaultWorldNonNull()->getFolderName() === $worldName) {
			$sender->sendMessage("§cCould not rename default world!");
			return;
		}

		try {
			WorldUtils::renameWorld($worldName, $newName);
		} catch(RuntimeException) {
			$sender->sendMessage(LanguageManager::translateMessage($sender, "rename-error"));
			return;
		}

		$sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage($sender, "rename-done", [$worldName, $newName]));
	}
}
