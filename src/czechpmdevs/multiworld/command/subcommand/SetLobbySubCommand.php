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
use RuntimeException;
use function array_shift;
use function count;
use function explode;
use function file_get_contents;
use function file_put_contents;
use function implode;

class SetLobbySubCommand extends BaseSubCommand {
	private const DEFAULT_WORLD_PROPERTIES_KEY = "level-name";

	protected function prepare(): void {
		$this->setPermission("multiworld.command.setlobby");
	}

	/**
	 * @param array<string, mixed> $args
	 */
	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		if(!$sender instanceof Player) {
			$sender->sendMessage(LanguageManager::translateMessage($sender, "ingame"));
			return;
		}

		try {
			$this->updateProperties($sender->getWorld()->getFolderName());
		} catch(RuntimeException $e) {
			$sender->sendMessage(LanguageManager::translateMessage($sender, "setlobby-error", [$e->getMessage()]));
			return;
		}

		$sender->getServer()->getWorldManager()->setDefaultWorld($sender->getWorld());

		$sender->getWorld()->setSpawnLocation($sender->getLocation());
		$sender->getWorld()->save(true);

		$sender->sendMessage(MultiWorld::getPrefix() . LanguageManager::translateMessage($sender, "setlobby-success", [$sender->getWorld()->getDisplayName(), "{$sender->getPosition()->getX()}, {$sender->getPosition()->getY()}, {$sender->getPosition()->getZ()}"]));
	}

	private function updateProperties(string $newDefaultLevelName): void {
		$contents = file_get_contents(Server::getInstance()->getDataPath() . "server.properties");
		if(!$contents) {
			throw new RuntimeException("Could not access server.properties");
		}

		$lines = explode("\n", $contents);
		$updated = false;
		foreach($lines as &$line) {
			$split = explode("=", $line);
			if(count($split) <= 1) {
				continue;
			}

			$key = array_shift($split);
			if($key !== self::DEFAULT_WORLD_PROPERTIES_KEY) {
				continue;
			}

			$line = "$key=$newDefaultLevelName";
			$updated = true;
			break;
		}

		if(!$updated) {
			$lines[] = self::DEFAULT_WORLD_PROPERTIES_KEY . "=" . $newDefaultLevelName;
		}

		if(file_put_contents(Server::getInstance()->getDataPath() . "server.properties", implode("\n", $lines)) === false) {
			throw new RuntimeException("Could not write to server.properties");
		}
	}
}