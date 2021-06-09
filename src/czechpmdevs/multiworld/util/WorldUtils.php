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

namespace czechpmdevs\multiworld\utils;

use czechpmdevs\multiworld\generator\ender\EnderGenerator;
use czechpmdevs\multiworld\generator\nether\NetherGenerator;
use czechpmdevs\multiworld\generator\normal\NormalGenerator;
use czechpmdevs\multiworld\generator\skyblock\SkyBlockGenerator;
use czechpmdevs\multiworld\generator\void\VoidGenerator;
use pocketmine\level\format\io\BaseLevelProvider;
use pocketmine\level\generator\Flat;
use pocketmine\level\generator\hell\Nether;
use pocketmine\level\generator\normal\Normal;
use pocketmine\level\Level;
use pocketmine\Server;
use function basename;
use function is_dir;
use function is_file;
use function rmdir;
use function scandir;
use function unlink;
use const DIRECTORY_SEPARATOR;

class WorldUtils {

    public const GENERATOR_NORMAL = 0;
    public const GENERATOR_NORMAL_CUSTOM = 1;
    public const GENERATOR_HELL = 2;
    public const GENERATOR_ENDER = 3;
    public const GENERATOR_FLAT = 4;
    public const GENERATOR_VOID = 5;
    public const GENERATOR_SKYBLOCK = 6;

    public const GENERATOR_HELL_OLD = 7;

    /**
     * @param string $levelName
     * @param int $seed
     * @param int $generator
     *
     * @return bool $isGenerated
     */
    public static function generateLevel(string $levelName, int $seed = 0, int $generator = WorldUtils::GENERATOR_NORMAL): bool {
        if (self::isLevelGenerated($levelName)) {
            return false;
        }

        $generatorClass = Normal::class;

        switch ($generator) {
            case self::GENERATOR_HELL:
                $generatorClass = NetherGenerator::class;
                break;
            case self::GENERATOR_ENDER:
                $generatorClass = EnderGenerator::class;
                break;
            case self::GENERATOR_FLAT:
                $generatorClass = Flat::class;
                break;
            case self::GENERATOR_VOID:
                $generatorClass = VoidGenerator::class;
                break;
            case self::GENERATOR_HELL_OLD:
                $generatorClass = Nether::class;
                break;
            case self::GENERATOR_SKYBLOCK:
                $generatorClass = SkyBlockGenerator::class;
                break;
            case self::GENERATOR_NORMAL_CUSTOM:
                $generatorClass = NormalGenerator::class;
        }

        return Server::getInstance()->generateLevel($levelName, $seed, $generatorClass);
    }

    public static function isLevelGenerated(string $levelName): bool {
        return Server::getInstance()->isLevelGenerated($levelName) && !in_array($levelName, [".", ".."]);
    }

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

    public static function isLevelLoaded(string $levelName): bool {
        return Server::getInstance()->isLevelLoaded($levelName);
    }

    public static function getLevel(string $name): ?Level {
        return Server::getInstance()->getLevelByName($name);
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

    public static function lazyLoadLevel(string $name): bool {
        return !Server::getInstance()->isLevelLoaded($name) && Server::getInstance()->loadLevel($name);
    }
}