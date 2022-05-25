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

namespace czechpmdevs\multiworld\util;

use FilesystemIterator;
use pocketmine\Server;
use pocketmine\utils\AssumptionFailedError;
use pocketmine\world\format\io\data\BaseNbtWorldData;
use pocketmine\world\generator\GeneratorManager;
use pocketmine\world\generator\GeneratorManagerEntry;
use pocketmine\world\World;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;
use function array_filter;
use function array_map;
use function array_merge;
use function array_unique;
use function array_values;
use function copy;
use function count;
use function mkdir;
use function rename;
use function rmdir;
use function scandir;
use function str_replace;
use function strtolower;
use function unlink;

class WorldUtils {

	public static function removeWorld(string $name): int {
		if(Server::getInstance()->getWorldManager()->isWorldLoaded($name)) {
			$world = WorldUtils::getWorldByNameNonNull($name);
			if(count($world->getPlayers()) > 0) {
				foreach($world->getPlayers() as $player) {
					$player->teleport(WorldUtils::getDefaultWorldNonNull()->getSpawnLocation());
				}
			}

			Server::getInstance()->getWorldManager()->unloadWorld($world);
		}

		$removedFiles = 1;

		$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($worldPath = Server::getInstance()->getDataPath() . "/worlds/$name", FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::CHILD_FIRST);
		/** @var SplFileInfo $fileInfo */
		foreach($files as $fileInfo) {
			if($filePath = $fileInfo->getRealPath()) {
				if($fileInfo->isFile()) {
					unlink($filePath);
				} else {
					rmdir($filePath);
				}

				$removedFiles++;
			}
		}

		rmdir($worldPath);
		return $removedFiles;
	}

	/**
	 * WARNING: This method should be used only in the case, when we
	 * know, that the world is generated and loaded.
	 */
	public static function getWorldByNameNonNull(string $name): World {
		$world = Server::getInstance()->getWorldManager()->getWorldByName($name);
		if($world === null) {
			throw new AssumptionFailedError("Required world $name is null");
		}

		return $world;
	}

	public static function getDefaultWorldNonNull(): World {
		$world = Server::getInstance()->getWorldManager()->getDefaultWorld();
		if($world === null) {
			throw new AssumptionFailedError("Default world is null");
		}

		return $world;
	}

	public static function renameWorld(string $oldName, string $newName): void {
		WorldUtils::lazyUnloadWorld($oldName);

		$from = Server::getInstance()->getDataPath() . "/worlds/" . $oldName;
		$to = Server::getInstance()->getDataPath() . "/worlds/" . $newName;

		rename($from, $to);

		WorldUtils::lazyLoadWorld($newName);
		$newWorld = Server::getInstance()->getWorldManager()->getWorldByName($newName);
		if(!$newWorld instanceof World) {
			return;
		}

		$worldData = $newWorld->getProvider()->getWorldData();
		if(!$worldData instanceof BaseNbtWorldData) {
			return;
		}

		$worldData->getCompoundTag()->setString("LevelName", $newName);

		Server::getInstance()->getWorldManager()->unloadWorld($newWorld); // reloading the world
		WorldUtils::lazyLoadWorld($newName);
	}

	public static function duplicateWorld(string $worldName, string $duplicateName): void {
		if(Server::getInstance()->getWorldManager()->isWorldLoaded($worldName)) {
			WorldUtils::getWorldByNameNonNull($worldName)->save(false);
		}

		mkdir(Server::getInstance()->getDataPath() . "/worlds/$duplicateName");

		$files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(Server::getInstance()->getDataPath() . "/worlds/$worldName", FilesystemIterator::SKIP_DOTS), RecursiveIteratorIterator::SELF_FIRST);
		/** @var SplFileInfo $fileInfo */
		foreach($files as $fileInfo) {
			if($filePath = $fileInfo->getRealPath()) {
				if($fileInfo->isFile()) {
					@copy($filePath, str_replace($worldName, $duplicateName, $filePath));
				} else {
					mkdir(str_replace($worldName, $duplicateName, $filePath));
				}
			}
		}
	}

	/**
	 * @return bool Returns if the world was unloaded with the function.
	 * If it has already been unloaded before calling this function, returns FALSE!
	 */
	public static function lazyUnloadWorld(string $name, bool $force = false): bool {
		if(($world = Server::getInstance()->getWorldManager()->getWorldByName($name)) !== null) {
			return Server::getInstance()->getWorldManager()->unloadWorld($world, $force);
		}
		return false;
	}

	/**
	 * @return bool Returns if the world was loaded with the function.
	 * If it has already been loaded before calling this function, returns FALSE!
	 */
	public static function lazyLoadWorld(string $name): bool {
		return !Server::getInstance()->getWorldManager()->isWorldLoaded($name) && Server::getInstance()->getWorldManager()->loadWorld($name, true);
	}

	/**
	 * @return string[] Returns all the levels on the server including
	 * unloaded ones
	 */
	public static function getAllWorlds(): array {
		$files = scandir(Server::getInstance()->getDataPath() . "/worlds/");
		if(!$files) {
			return [];
		}

		// This is not necessary in case only clean PocketMine without other plugins is used,
		// however, due to compatibility with plugins such as NativeDimensions it's needed to keep this.
		$files = array_unique(array_merge(
			array_map(fn(World $world) => $world->getFolderName(), Server::getInstance()->getWorldManager()->getWorlds()),
			$files
		));

		return array_values(array_filter($files, function(string $fileName): bool {
			return Server::getInstance()->getWorldManager()->isWorldGenerated($fileName) &&
				$fileName != "." && $fileName != ".."; // Server->isWorldGenerated detects '.' and '..' as world, TODO - make pull request
		}));
	}

	/**
	 * @return World|null Loads and returns world, if it is generated.
	 */
	public static function getLoadedWorldByName(string $name): ?World {
		WorldUtils::lazyLoadWorld($name);

		return Server::getInstance()->getWorldManager()->getWorldByName($name);
	}

	/**
	 * @phpstan-return GeneratorManagerEntry|null
	 */
	public static function getGeneratorByName(string $name): ?GeneratorManagerEntry {
		$name = match (strtolower($name)) {
			"classic", "basic" => "normal",
			"custom" => "normal_mw",
			"superflat" => "flat",
			"nether", "hell" => "nether_mw",
			"nether_old" => "nether",
			"ender", "end" => "ender",
			"sb" => "skyblock",
			"empty", "emptyworld" => "void",
			default => strtolower($name)
		};

		return GeneratorManager::getInstance()->getGenerator($name);
	}
}