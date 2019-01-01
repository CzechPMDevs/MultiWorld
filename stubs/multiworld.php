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

/*
 * PHP stubs for MultiWorld API
 *
 * Version: 1.5.x
 */

namespace multiworld\api;

/**
 * Class WorldManagementAPI
 */
class WorldManagementAPI {

    public const GENERATOR_NORMAL = 0;
    public const GENERATOR_HELL = 1;
    public const GENERATOR_ENDER = 2;
    public const GENERATOR_FLAT = 3;
    public const GENERATOR_VOID = 4;
    public const GENERATOR_SKYBLOCK = 5;

    public const GENERATOR_HELL_OLD = 6;

    /**
     * Expected level folder name
     * @param string $levelName
     *
     * @return bool $isLoaded
     */
    public static function isLevelLoaded(string $levelName): bool {}

    /**
     * Expected level folder name
     * @param string $levelName
     *
     * @return bool
     */
    public static function isLevelGenerated(string $levelName): bool {}

    /**
     * Returns level (only when is loaded and generated)
     * Expected level folder name
     * @param string $levelName
     *
     * @return \pocketmine\level\Level
     */
    public static function getLevel(string $levelName): ?\pocketmine\level\Level {}

    /**
     * Expected level folder name
     * @param string $levelName
     *
     * @return bool $isLoaded
     */
    public static function loadLevel(string $levelName): bool {}

    /**
     * @param \pocketmine\level\Level $level
     *
     * @return bool
     */
    public static function unloadLevel(\pocketmine\level\Level $level): bool {}

    /**
     * @param string $levelName
     * @param int $seed
     * @param int $generator
     *
     * @return bool
     */
    public static function generateLevel(string $levelName, int $seed = 0, int $generator = WorldManagementAPI::GENERATOR_NORMAL): bool {}

    /**
     * Expected level folder name
     * @param string $name
     *
     * @return int $removedFiles
     */
    public static function removeLevel(string $name): int {}

}

/**
 * Class WorldGameRulesAPI
 */
class WorldGameRulesAPI {

    /**
     * Returns game rules for GameRulesChangedPacket from level.dat
     * @param \pocketmine\level\Level $level
     *
     * @return array[] $gameRules
     */
    public static function getLevelGameRules(\pocketmine\level\Level $level): array {}

    /**
     * Expected game rules format like on GameRulesChangedPaket
     * @param \pocketmine\level\Level $level
     * @param array $gameRules
     *
     * @return bool $isSet
     */
    public static function setLevelGameRules(\pocketmine\level\Level $level, array $gameRules): bool {}

    /**
     * Simply update level game rule and send packets to level players
     * @param \pocketmine\level\Level $level
     * @param string $gameRule
     * @param bool $value
     *
     * @return bool $isUpdated
     */
    public static function updateLevelGameRule(\pocketmine\level\Level $level, string $gameRule, bool $value): bool {}

    /**
     * Reloads level game rules (Sends packet to players)
     *
     * @param \pocketmine\level\Level $level
     */
    public static function reloadGameRules(\pocketmine\level\Level $level) {}

    /**
     * Returns all implemented values in game rules format like on GameRulesChangedPacket
     *
     * @return array $rules
     */
    public static function getDefaultGameRules(): array {}

    /**
     * keepinventory -> keepInventory
     *
     * @param string $lowerString
     *
     * @return string $upperString
     */
    public static function getRuleFromLowerString(string $lowerString): string {}


    /**
     * Sends GameRulesChangedPacket to player
     *
     * @param \pocketmine\Player $player
     *
     * When is $level null, is used level, where the player is located
     * @param null|\pocketmine\level\Level $level
     */
    public static function updateGameRules(\pocketmine\Player $player, ?\pocketmine\level\Level $level = null) {}

    /**
     * Returns all implemented game rules
     *
     * @return string[] $rules
     */
    public static function getAllGameRules(): array {}

    /**
     * Handles game rule change (stops time, ...)
     *
     * @param \pocketmine\level\Level $level
     * @param array $gameRules
     */
    public static function handleGameRuleChange(\pocketmine\level\Level $level, array $gameRules) {}
}