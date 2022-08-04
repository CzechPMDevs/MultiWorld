<?php

/**
 * MultiWorld - PocketMine plugin that manages worlds.
 * Copyright (C) 2018 - 2022  CzechPMDevs
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

class DuplicateSubCommand extends BaseSubCommand {
	protected function prepare(): void {
		$this->registerArgument(0, new RawStringArgument("worldName"));
		$this->registerArgument(1, new RawStringArgument("duplicatedWorldName", true));

		$this->setPermission("multiworld.command.duplicate");
	}

	/**
	 * @param array<string, mixed> $args
	 */
	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		/** @var string $worldName */
		$worldName = $args["worldName"];
		/** @var string $duplicatedWorldName */
		$duplicatedWorldName = $args["duplicatedWorldName"] ?? $worldName . "_copy";
		if(Server::getInstance()->getWorldManager()->isWorldGenerated($duplicatedWorldName)) {
			$sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage($sender, "duplicate-exists", [$duplicatedWorldName]));
			return;
		}

		if(!Server::getInstance()->getWorldManager()->isWorldGenerated($worldName)) {
			$sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage($sender, "duplicate-levelnotfound", [$worldName]));
			return;
		}

		WorldUtils::duplicateWorld($worldName, $duplicatedWorldName);
		$sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage($sender, "duplicate-done", [$worldName, $duplicatedWorldName]));
	}
}