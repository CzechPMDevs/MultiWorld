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

use CortexPE\Commando\BaseSubCommand;
use czechpmdevs\multiworld\MultiWorld;
use czechpmdevs\multiworld\util\LanguageManager;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;

class SetSpawnSubCommand extends BaseSubCommand {
	protected function prepare(): void {
		$this->setPermission("multiworld.command.setspawn");
	}

	/**
	 * @param array<string, mixed> $args
	 */
	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		if(!$sender instanceof Player) {
			$sender->sendMessage(LanguageManager::translateMessage($sender, "ingame"));
			return;
		}

		$sender->getWorld()->setSpawnLocation($sender->getLocation());
		$sender->getWorld()->save(true);

		$sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage($sender, "setspawn-success", [$sender->getWorld()->getDisplayName(), "{$sender->getPosition()->getX()}, {$sender->getPosition()->getY()}, {$sender->getPosition()->getZ()}"]));
	}
}