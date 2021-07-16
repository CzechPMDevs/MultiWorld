<?php /** @noinspection PhpRedundantCatchClauseInspection */

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

use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\network\mcpe\protocol\ProtocolInfo;
use pocketmine\network\mcpe\protocol\SettingsCommandPacket;
use pocketmine\network\mcpe\protocol\StartGamePacket;
use ReflectionClass;
use ReflectionException;
use ReflectionProperty;
use function array_filter;
use function class_exists;
use function count;
use function strpos;

class Utils {

    /**
     * Hacky function which makes plugin independent on protocol changes
     * made on packets whose are not used by MultiWorld
     */
    public static function isProtocolCompatible(): bool {
        if(class_exists(ProtocolInfo::class) && ProtocolInfo::CURRENT_PROTOCOL == 448) {
            return true;
        }

        if (!class_exists(GameRulesChangedPacket::class)) {
            return false;
        }

        static $requiredVarType = "@phpstan-var array<string, array{0: int, 1: bool|int|float, 2: bool}>";

        // Checking for changes in GameRulesChangedPacket
        try {
            $ref = new ReflectionClass(GameRulesChangedPacket::class);
            $prop = $ref->getProperty("gameRules");

            if (!($doc = $prop->getDocComment())) {
                return false;
            }

            if (strpos($doc, $requiredVarType) === false) {
                return false;
            }

            if (count(array_filter($ref->getProperties(), function (ReflectionProperty $property): bool {
                    return $property->getDeclaringClass()->getName() == GameRulesChangedPacket::class;
                })) != 1) {
                return false;
            }

        } catch (ReflectionException $e) {
            return false;
        }

        // ... for changes in StartGamePacket
        try {
            $ref = new ReflectionClass(StartGamePacket::class);
            $prop = $ref->getProperty("gameRules");

            if (!($doc = $prop->getDocComment())) {
                return false;
            }

            if (strpos($doc, $requiredVarType) === false) {
                return false;
            }
        } catch (ReflectionException $e) {
            return false;
        }

        // ... LoginPacket
        try {
            $ref = new ReflectionClass(LoginPacket::class);
            $prop = $ref->getProperty("locale");

            if(!($doc = $prop->getDocComment())) {
                return false;
            }

            if(strpos($doc, "@var string") === false) {
                return false;
            }
        } catch (ReflectionException $e) {
            return false;
        }

        // ... SettingsCommandPacket
        try {
            $ref = new ReflectionClass(SettingsCommandPacket::class);
            $prop = $ref->getProperty("command");

            if(!($doc = $prop->getDocComment())) {
                return false;
            }

            if(strpos($doc, "@var string") === false) {
                return false;
            }

            /** @noinspection PhpExpressionResultUnusedInspection */
            $ref->getMethod("getCommand"); // Throws exception if the method does not exists.
        } catch (ReflectionException $e) {
            return false;
        }

        return true;
    }
}
