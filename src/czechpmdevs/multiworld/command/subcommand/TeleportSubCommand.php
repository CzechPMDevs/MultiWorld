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

namespace czechpmdevs\multiworld\command\subcommand;

use czechpmdevs\multiworld\MultiWorld;
use czechpmdevs\multiworld\util\LanguageManager;
use czechpmdevs\multiworld\util\WorldUtils;
use Exception;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class TeleportSubCommand implements SubCommand {

	public function execute(CommandSender $sender, array $args, string $name): void {
		try {
			if (!isset($args[0])) {
				$sender->sendMessage(LanguageManager::translateMessage($sender, "teleport-usage"));
				return;
			}

			$world = WorldUtils::getLoadedWorldByName($args[0]);
			if ($world === null) {
				$sender->sendMessage(LanguageManager::translateMessage($sender, "teleport-levelnotexists", [$args[0]]));
				return;
			}

			if (!isset($args[1])) {
				if (!$sender instanceof Player) {
					$sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage($sender, "teleport-usage"));
					return;
				}

				$sender->teleport($world->getSpawnLocation());
				$sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage($sender, "teleport-done-1", [$world->getDisplayName()]));
				return;
			}

			$player = Server::getInstance()->getPlayerByPrefix($args[1]);
			if ((!$player instanceof Player) || !$player->isOnline()) {
				$sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage($sender, "teleport-playernotexists"));
				return;
			}

			$player->teleport($world->getSafeSpawn());

			$player->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage($sender, "teleport-done-1", [$world->getDisplayName()]));
			$sender->sendMessage(LanguageManager::translateMessage($sender, "teleport-done-2", [$world->getDisplayName(), $player->getName()]));
			return;
		} catch (Exception $exception) {
			$sender->sendMessage("§cAn error occurred while teleporting player between worlds: " . $exception->getMessage() . " (at line: " . $exception->getLine() . " , file: " . $exception->getFile() . ")");
		}
	}
}
