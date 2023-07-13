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
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\WorldException;

class TeleportSubCommand extends BaseSubCommand {
	protected function prepare(): void {
		$this->registerArgument(0, new RawStringArgument("world"));
		$this->registerArgument(1, new RawStringArgument("player", true));

		$this->setPermission("multiworld.command.teleport");
	}

	/**
	 * @param array<string, mixed> $args
	 */
	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		/** @var string $world */
		$world = $args["world"];
		/** @var string|null $player */
		$player = $args["player"] ?? null;

		try {
			$targetWorld = WorldUtils::getLoadedWorldByName($world);
		} catch(WorldException $e) {
			$sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage($sender, "teleport-error", [$e->getMessage()]));
			return;
		}

		if($targetWorld === null) {
			$sender->sendMessage(LanguageManager::translateMessage($sender, "teleport-levelnotexists", [$world]));
			return;
		}

		if($player === null) {
			if(!$sender instanceof Player) {
				$sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage($sender, "teleport-usage"));
				return;
			}

			$sender->teleport($targetWorld->getSpawnLocation());
			$sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage($sender, "teleport-done-1", [$targetWorld->getDisplayName()]));
			return;
		}

		$targetPlayer = Server::getInstance()->getPlayerByPrefix($player);
		if($targetPlayer === null) {
			$sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage($sender, "teleport-playernotexists", [$player]));
			return;
		}

		$targetPlayer->teleport($targetWorld->getSafeSpawn());

		$targetPlayer->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage($sender, "teleport-done-1", [$targetWorld->getDisplayName()]));
		$sender->sendMessage(LanguageManager::translateMessage($sender, "teleport-done-2", [$targetWorld->getDisplayName(), $targetPlayer->getName()]));
	}
}
