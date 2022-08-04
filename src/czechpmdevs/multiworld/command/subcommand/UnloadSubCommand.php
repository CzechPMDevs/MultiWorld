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

class UnloadSubCommand extends BaseSubCommand {
	protected function prepare(): void {
		$this->registerArgument(0, new RawStringArgument("world"));

		$this->setPermission("multiworld.command.unload");
	}

	/**
	 * @param array<string, mixed> $args
	 */
	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		/** @var string $worldName */
		$worldName = $args["world"];
		
		if(!Server::getInstance()->getWorldManager()->isWorldGenerated($worldName)) {
			$sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage($sender, "unload-levelnotexists", [$worldName]));
			return;
		}

		if(!Server::getInstance()->getWorldManager()->isWorldLoaded($worldName)) {
			$sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage($sender, "unload-unloaded"));
			return;
		}

		$world = WorldUtils::getWorldByNameNonNull($worldName);
		if($world->getId() === Server::getInstance()->getWorldManager()->getDefaultWorld()?->getId()) {
			$sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage($sender, "unload-default"));
			return;
		}

		Server::getInstance()->getWorldManager()->unloadWorld($world);
		$sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage($sender, "unload-done"));
	}
}
