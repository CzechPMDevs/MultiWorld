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
use pocketmine\world\World;
use function file_exists;
use function str_replace;

class DeleteSubCommand extends BaseSubCommand {
	protected function prepare(): void {
		$this->registerArgument(0, new RawStringArgument("worldName"));

		$this->setPermission("multiworld.command.delete");
	}

	/**
	 * @param array<string, mixed> $args
	 */
	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		/** @var string $worldName */
		$worldName = $args["worldName"];

		if(!Server::getInstance()->getWorldManager()->isWorldGenerated($worldName) || !file_exists(Server::getInstance()->getDataPath() . "worlds/$worldName")) {
			$sender->sendMessage(MultiWorld::getPrefix() . str_replace("%1", $worldName, LanguageManager::translateMessage($sender, "delete-levelnotexists")));
			return;
		}

		$world = Server::getInstance()->getWorldManager()->getWorldByName($worldName);
		if($world instanceof World) { // World is loaded
			if(WorldUtils::getDefaultWorldNonNull()->getId() === $world->getId()) {
				$sender->sendMessage("§cCould not remove default world!");
				return;
			}
		}

		$sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage($sender, "delete-done", [(string)WorldUtils::removeWorld($worldName)]));
	}
}
