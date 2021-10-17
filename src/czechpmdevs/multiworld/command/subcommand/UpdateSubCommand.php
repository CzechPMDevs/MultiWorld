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
use pocketmine\command\CommandSender;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use function count;
use function is_numeric;
use function str_replace;
use function strtolower;

class UpdateSubCommand implements SubCommand {

	public function execute(CommandSender $sender, array $args, string $name): void {
		if (!isset($args[0])) {
			$sender->sendMessage(LanguageManager::translateMessage($sender, "update-usage"));
			return;
		}

		switch (strtolower($args[0])) {
			case "spawn":
				if (!isset($args[1]) && ($sender instanceof Player)) {
					$sender->getWorld()->setSpawnLocation($sender->getPosition()->asVector3());
					$sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage($sender, "update-spawn-done", [$sender->getWorld()->getDisplayName()]));
					break;
				}

				if (count($args) < 5 || !is_numeric($args[2]) || !is_numeric($args[3]) || !is_numeric($args[4])) {
					$sender->sendMessage(LanguageManager::translateMessage($sender, "update-usage"));
					break;
				}

				if (!$sender->getServer()->getWorldManager()->isWorldGenerated($args[1])) {
					$sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage($sender, "update-levelnotexists"));
					break;
				}

				WorldUtils::lazyLoadWorld($args[1]);
				WorldUtils::getWorldByNameNonNull($args[1])->setSpawnLocation(new Vector3((int)$args[2], (int)$args[3], (int)$args[4]));

				$sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage($sender, "update-done"));
				break;
			case "lobby":
			case "hub":
				if (!$sender instanceof Player) {
					$sender->sendMessage(LanguageManager::translateMessage($sender, "update-notsupported"));
					break;
				}

				$sender->getWorld()->setSpawnLocation($sender->getPosition()->asVector3());
				$sender->getServer()->getWorldManager()->setDefaultWorld($sender->getWorld());

				$sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage($sender, "update-lobby-done", [$sender->getWorld()->getFolderName()]));
				break;
			case "default":
			case "defaultlevel":
				if (!isset($args[1])) {
					$sender->sendMessage(LanguageManager::translateMessage($sender, "update-usage"));
					break;
				}

				if (!$sender->getServer()->getWorldManager()->isWorldGenerated($args[1])) {
					$sender->sendMessage(MultiWorld::getPrefix() . str_replace("%1", $args[1], LanguageManager::translateMessage($sender, "update-levelnotexists")));
					break;
				}

				if (!$sender->getServer()->getWorldManager()->isWorldLoaded($args[1])) {
					$sender->getServer()->getWorldManager()->loadWorld($args[1], true);
				}

				$sender->getServer()->getWorldManager()->setDefaultWorld(WorldUtils::getWorldByNameNonNull($args[1]));
				$sender->sendMessage(MultiWorld::getPrefix() . str_replace("%1", $args[1], LanguageManager::translateMessage($sender, "update-default-done")));
				break;
			default:
				$sender->sendMessage(LanguageManager::translateMessage($sender, "update-usage"));
				break;
		}
	}
}
