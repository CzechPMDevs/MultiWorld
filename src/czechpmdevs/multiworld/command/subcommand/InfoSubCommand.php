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
use czechpmdevs\multiworld\util\LanguageManager;
use czechpmdevs\multiworld\util\WorldUtils;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\Server;
use pocketmine\world\World;
use function count;

class InfoSubCommand extends BaseSubCommand {
	protected function prepare(): void {
		$this->registerArgument(0, new RawStringArgument("worldName", true));

		$this->setPermission("multiworld.command.info");
	}

	/**
	 * @param array<string, mixed> $args
	 */
	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		/** @var string|null $worldName */
		$worldName = $args["worldName"] ?? null;

		if($worldName === null && !($sender instanceof Player)) {
			$sender->sendMessage("§cUsage: §7/mw info <worldName>"); // This cannot be translated, because the sender is always console
			return;
		}

		if($worldName !== null) {
			if(!Server::getInstance()->getWorldManager()->isWorldGenerated($worldName)) {
				$sender->sendMessage(LanguageManager::translateMessage($sender, "info.levelnotexists", [$worldName]));
				return;
			}

			if(WorldUtils::lazyLoadWorld($worldName)) {
				$sender->sendMessage($this->getInfoMessage($sender, WorldUtils::getWorldByNameNonNull($worldName)));
			} else {
				$sender->sendMessage(LanguageManager::translateMessage($sender, "info.unloaded", [$worldName]));
			}
		} elseif($sender instanceof Player) {
			$sender->sendMessage($this->getInfoMessage($sender, $sender->getWorld()));
		}
	}

	private function getInfoMessage(CommandSender $sender, World $world): string {
		return LanguageManager::translateMessage($sender, "info", [$world->getDisplayName()]) . "\n" .
			LanguageManager::translateMessage($sender, "info-name", [$world->getDisplayName()]) . "\n" .
			LanguageManager::translateMessage($sender, "info-folderName", [$world->getFolderName()]) . "\n" .
			LanguageManager::translateMessage($sender, "info-players", [(string)count($world->getPlayers())]) . "\n" .
			LanguageManager::translateMessage($sender, "info-generator", [$world->getProvider()->getWorldData()->getGenerator()]) . "\n" .
			LanguageManager::translateMessage($sender, "info-seed", [(string)$world->getSeed()]) . "\n" .
			LanguageManager::translateMessage($sender, "info-time", [(string)$world->getTime()]);
	}
}
