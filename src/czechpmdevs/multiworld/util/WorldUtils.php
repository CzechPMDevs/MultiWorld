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

namespace czechpmdevs\multiworld\util;

use czechpmdevs\multiworld\generator\ender\EnderGenerator;
use czechpmdevs\multiworld\generator\nether\NetherGenerator;
use czechpmdevs\multiworld\generator\normal\NormalGenerator;
use czechpmdevs\multiworld\generator\skyblock\SkyBlockGenerator;
use czechpmdevs\multiworld\generator\void\VoidGenerator;
use InvalidArgumentException;
use pocketmine\level\format\io\BaseLevelProvider;
use pocketmine\level\generator\Flat;
use pocketmine\level\generator\Generator;
use pocketmine\level\generator\GeneratorManager;
use pocketmine\level\generator\hell\Nether;
use pocketmine\level\generator\normal\Normal;
use pocketmine\level\Level;
use pocketmine\Server;
use pocketmine\utils\AssumptionFailedError;
use function basename;
use function count;
use function is_dir;
use function is_file;
use function rename;
use function rmdir;
use function scandir;
use function strtolower;
use function unlink;
use const DIRECTORY_SEPARATOR;

class WorldUtils {

    public static function removeLevel(string $name): int {
        if (Server::getInstance()->isLevelLoaded($name)) {
            /** @phpstan-var Level $level */
            $level = Server::getInstance()->getLevelByName($name);
            if (count($level->getPlayers()) > 0) {
                foreach ($level->getPlayers() as $player) {
                    /** @phpstan-ignore-next-line */
                    $player->teleport(Server::getInstance()->getDefaultLevel()->getSpawnLocation());
                }
            }

            Server::getInstance()->unloadLevel($level);
        }

        return WorldUtils::removeDirectory(Server::getInstance()->getDataPath() . DIRECTORY_SEPARATOR . "worlds" . DIRECTORY_SEPARATOR . $name);
    }

    private static function removeDirectory(string $dirPath): int {
        $removedFolders = 1;
        if (basename($dirPath) == "." || basename($dirPath) == ".." || !is_dir($dirPath)) {
            return 0;
        }

        if(!($files = scandir($dirPath))) {
            return 0;
        }

        foreach ($files as $file) {
            if ($file != "." || $file != "..") {
                if (is_dir($dirPath . DIRECTORY_SEPARATOR . $file)) {
                    $removedFolders += WorldUtils::removeDirectory($dirPath . DIRECTORY_SEPARATOR . $file);
                    continue;
                }

                if (is_file($dirPath . DIRECTORY_SEPARATOR . $file)) {
                    unlink($dirPath . DIRECTORY_SEPARATOR . $file);
                    $removedFolders++;
                }
            }

        }

        rmdir($dirPath);
        return $removedFolders;
    }

    public static function renameLevel(string $oldName, string $newName): void {
        if (Server::getInstance()->isLevelLoaded($oldName)) {
            /** @phpstan-ignore-next-line */
            Server::getInstance()->unloadLevel(Server::getInstance()->getLevelByName($oldName));
        }

        $from = Server::getInstance()->getDataPath() . "/worlds/" . $oldName;
        $to = Server::getInstance()->getDataPath() . "/worlds/" . $newName;

        rename($from, $to);

        WorldUtils::lazyLoadLevel($newName);
        $newLevel = Server::getInstance()->getLevelByName($newName);
        if(!$newLevel instanceof Level) {
            return;
        }

        $provider = $newLevel->getProvider();
        if (!$provider instanceof BaseLevelProvider) {
            return;
        }

        $provider->getLevelData()->setString("LevelName", $newName);
        $provider->saveLevelData();

        Server::getInstance()->unloadLevel($newLevel);
        WorldUtils::lazyLoadLevel($newName); // reloading the level
    }

    /**
     * @return bool Returns if the level was loaded with the function.
     * If it has already been loaded before calling this function, returns FALSE!
     */
    public static function lazyLoadLevel(string $name): bool {
        return !Server::getInstance()->isLevelLoaded($name) && Server::getInstance()->loadLevel($name);
    }

    /**
     * @return bool Returns if the level was unloaded with the function.
     * If it has already been unloaded before calling this function, returns FALSE!
     */
    public static function lazyUnloadLevel(string $name, bool $force = false): bool {
        if(($level = Server::getInstance()->getLevelByName($name)) !== null) {
            return Server::getInstance()->unloadLevel($level, $force);
        }
        return false;
    }

    /**
     * @return Level|null Loads and returns level, if it is generated.
     */
    public static function getLoadedLevelByName(string $name): ?Level {
        WorldUtils::lazyLoadLevel($name);

        return Server::getInstance()->getLevelByName($name);
    }

    /**
     * WARNING: This method should be used only in the case, when we
     * know, that the level is generated and loaded.
     */
    public static function getLevelByNameNonNull(string $name): Level {
        $level = Server::getInstance()->getLevelByName($name);
        if($level === null) {
            throw new AssumptionFailedError("Required level $name is null");
        }

        return $level;
    }

    public static function getDefaultLevelNonNull(): Level {
        $level = Server::getInstance()->getDefaultLevel();
        if($level === null) {
            throw new AssumptionFailedError("Default level is null");
        }

        return $level;
    }

    /**
     * @phpstan-return class-string<Generator>|null
     */
    public static function getGeneratorByName(string $name): ?string {
        switch (strtolower($name)) {
            case "normal":
            case "classic":
            case "basic":
                return Normal::class;
            case "custom":
                return NormalGenerator::class;
            case "flat":
            case "superflat":
                return Flat::class;
            case "nether":
            case "hell":
                return NetherGenerator::class;
            case "ender":
            case "end":
                return EnderGenerator::class;
            case "void":
                return VoidGenerator::class;
            case "skyblock":
            case "sb":
            case "sky":
                return SkyBlockGenerator::class;
            case "nether_old":
                return Nether::class;
        }

        try {
            return GeneratorManager::getGenerator($name, true);
        } catch (InvalidArgumentException $e) {}

        return null;
    }
}