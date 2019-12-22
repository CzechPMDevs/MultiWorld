<?php

/**
 * MultiWorld - PocketMine plugin that manages worlds.
 * Copyright (C) 2018 - 2019  CzechPMDevs
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

namespace czechpmdevs\multiworld\api;

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

/**
 * Class WorldManagementAPI
 * @package czechpmdevs\multiworld\api
 */
class WorldManagementAPI {

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
     *
     * @return bool
     */
    public static function isLevelLoaded(string $levelName): bool {
        return Server::getInstance()->isLevelLoaded($levelName);
    }

    /**
     * @param string $levelName
     *
     * @return bool
     */
    public static function isLevelGenerated(string $levelName): bool {
        return Server::getInstance()->isLevelGenerated($levelName) && !in_array($levelName, [".", ".."]);
    }

    /**
     * @param string $name
     * @return null|Level
     */
    public static function getLevel(string $name): ?Level {
        return Server::getInstance()->getLevelByName($name);
    }

    /**
     * @param string $name
     *
     * @return bool $isLoaded
     */
    public static function loadLevel(string $name): bool {
        return self::isLevelLoaded($name) ? false : Server::getInstance()->loadLevel($name);
    }

    /**
     * @param Level $level
     *
     * @return bool
     */
    public static function unloadLevel(Level $level): bool {
        return $level->getServer()->unloadLevel($level);
    }

    /**
     * @param string $levelName
     * @param int $seed
     * @param int $generator
     *
     * @return bool $isGenerated
     */
    public static function generateLevel(string $levelName, int $seed = 0, int $generator = WorldManagementAPI::GENERATOR_NORMAL): bool {
        if(self::isLevelGenerated($levelName)) {
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

    /**
     * @param string $name
     *
     * @return int $files
     */
    public static function removeLevel(string $name): int {
        if(self::isLevelLoaded($name)) {
            $level = self::getLevel($name);

            if(count($level->getPlayers()) > 0) {
                foreach ($level->getPlayers() as $player) {
                    $player->teleport(Server::getInstance()->getDefaultLevel()->getSpawnLocation());
                }
            }

            $level->getServer()->unloadLevel($level);
        }

        return self::removeDir(Server::getInstance()->getDataPath()."/worlds/" . $name);
    }

    /**
     * @param string $oldName
     * @param string $newName
     */
    public static function renameLevel(string $oldName, string $newName) {
        if(self::isLevelLoaded($oldName)) self::unloadLevel(self::getLevel($oldName));

        $from = Server::getInstance()->getDataPath() . "/worlds/" . $oldName;
        $to = Server::getInstance()->getDataPath() . "/worlds/" . $newName;

        rename($from, $to);

        self::loadLevel($newName);
        $provider = self::getLevel($newName)->getProvider();

        if(!$provider instanceof BaseLevelProvider) return;
        $provider->getLevelData()->setString("LevelName", $newName);
        $provider->saveLevelData();

        self::unloadLevel(self::getLevel($newName));
        self::loadLevel($newName); // reloading the level
    }

    /**
     * @return string[] $levels
     */
    public static function getAllLevels(): array {
        $levels = [];
        foreach (glob(Server::getInstance()->getDataPath() . "/worlds/*") as $world) {
            if(count(scandir($world)) >= 4) { // don't forget to .. & .
                $levels[] = basename($world);
            }
        }
        return $levels;
    }

    /**
     * @param string $path
     * @return int
     */
    private static function removeFile(string $path): int {
        unlink($path); return 1;
    }

    /**
     * @param string $dirPath
     * @return int
     */
    private static function removeDir(string $dirPath): int {
        $files = 1;
        if(basename($dirPath) == "." || basename($dirPath) == "..") {
            return 0;
        }
        foreach (scandir($dirPath) as $item) {
            if($item != "." || $item != "..") {
                if(is_dir($dirPath . DIRECTORY_SEPARATOR . $item)) {
                    $files += self::removeDir($dirPath . DIRECTORY_SEPARATOR . $item);
                }
                if(is_file($dirPath . DIRECTORY_SEPARATOR . $item)) {
                    $files += self::removeFile($dirPath . DIRECTORY_SEPARATOR . $item);
                }
            }

        }
        rmdir($dirPath);
        return $files;
    }
}