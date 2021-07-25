<?php /** @noinspection PhpUnused */

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

namespace czechpmdevs\multiworld\level\gamerules;

use InvalidStateException;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\network\mcpe\protocol\types\BoolGameRule;
use pocketmine\network\mcpe\protocol\types\FloatGameRule;
use pocketmine\network\mcpe\protocol\types\GameRule;
use pocketmine\network\mcpe\protocol\types\IntGameRule;
use pocketmine\player\Player;
use pocketmine\world\format\io\data\BaseNbtWorldData;
use pocketmine\world\World;
use UnexpectedValueException;
use function array_key_exists;
use function array_map;
use function count;
use function get_class;
use function is_bool;
use function is_float;
use function is_int;
use function json_decode;
use function json_encode;

final class GameRules {

    public const GAMERULE_COMMAND_BLOCKS_ENABLED = "commandBlocksEnabled";
    public const GAMERULE_COMMAND_BLOCK_OUTPUT = "commandBlockOutput";
    public const GAMERULE_DO_DAYLIGHT_CYCLE = "doDaylightCycle";
    public const GAMERULE_DO_ENTITY_DROPS = "doEntityDrops";
    public const GAMERULE_DO_FIRE_TICKS = "doFireTick";
    public const GAMERULE_DO_INSOMNIA = "doInsomnia";
    public const GAMERULE_DO_IMMEDIATE_RESPAWN = "doImmediateRespawn";
    public const GAMERULE_DO_MOB_LOOT = "doMobLoot";
    public const GAMERULE_DO_MOB_SPAWNING = "doMobSpawning";
    public const GAMERULE_DO_TILE_DROPS = "doTileDrops";
    public const GAMERULE_DO_WEATHER_CYCLE = "doWeatherCycle";
    public const GAMERULE_DROWNING_DAMAGE = "drowningDamage";
    public const GAMERULE_FALL_DAMAGE = "fallDamage";
    public const GAMERULE_FIRE_DAMAGE = "fireDamage";
    public const GAMERULE_FREEZE_DAMAGE = "freezeDamage"; // Requires Minecraft 1.17^
    public const GAMERULE_FUNCTION_COMMAND_LIMIT = "functionCommandLimit";
    public const GAMERULE_KEEP_INVENTORY = "keepInventory";
    public const GAMERULE_MAX_COMMAND_CHAIN_LENGTH = "maxCommandChainLength";
    public const GAMERULE_MOB_GRIEFING = "mobGriefing";
    public const GAMERULE_NATURAL_REGENERATION = "naturalRegeneration";
    public const GAMERULE_PVP = "pvp";
    public const GAMERULE_RANDOM_TICK_SPEED = "randomTickSpeed";
    public const GAMERULE_SEND_COMMAND_FEEDBACK = "sendCommandFeedback";
    public const GAMERULE_SHOW_COORDINATES = "showCoordinates";
    public const GAMERULE_SHOW_DEATH_MESSAGES = "showDeathMessages";
    public const GAMERULE_SPAWN_RADIUS = "spawnRadius";
    public const GAMERULE_TNT_EXPLODES = "tntExplodes";
    public const GAMERULE_SHOW_TAGS = "showTags";

    /**
     * @var bool $allowPlayersEditGameRulesFromGame
     *
     * Enabling this options will be players with operator permissions
     * permitted to edit game rules from Minecraft settings
     */
    public static bool $allowPlayersEditGameRulesFromGame = true;

    /** @var GameRules */
    private static GameRules $defaultGameRules;

    /** @var GameRule[] */
    private array $gameRules = [];

    /**
     * @param GameRule[] $gameRules If the array is empty, default GameRules will be used
     */
    public function __construct(array $gameRules = []) {
        // In case for default GameRules
        if (!isset(GameRules::$defaultGameRules)) {
            $this->gameRules = $gameRules;
            return;
        }

        if (empty($gameRules)) {
            $this->gameRules = GameRules::getDefaultGameRules()->getGameRules();
            return;
        }

        // Removing invalid GameRules
        foreach ($gameRules as $key => $value) {
            if (!GameRules::$defaultGameRules->exists($key)) {
                unset($gameRules[$key]); // GameRule does not exist at all
            } elseif (get_class($value) != get_class(GameRules::$defaultGameRules->getGameRules()[$key])) {
                unset($gameRules[$key]); // GameRule has invalid type
            }
        }

        // Adding new GameRules
        foreach (GameRules::$defaultGameRules->gameRules as $key => $gameRule) {
            if (!array_key_exists($key, $gameRules)) {
                $gameRules[$key] = $gameRule;
            }
        }

        $this->gameRules = $gameRules;
    }

    /**
     * @return GameRule[]
     */
    public function getGameRules(): array {
        return $this->gameRules;
    }

    public static function getDefaultGameRules(): GameRules {
        return clone GameRules::$defaultGameRules;
    }

    public function exists(string $index): bool {
        return array_key_exists($index, $this->gameRules);
    }

    /**
     * @param mixed $value
     */
    public static function getPropertyType($value): string {
        if (is_bool($value)) {
            return BoolGameRule::class;
        }
        if (is_int($value)) {
            return IntGameRule::class;
        }
        if (is_float($value)) {
            return FloatGameRule::class;
        }

        throw new InvalidStateException("Unknown type for proper");
    }

    /**
     * @param GameRule[] $defaultGameRules
     */
    public static function init(array $defaultGameRules): void {
        GameRules::$defaultGameRules = new GameRules($defaultGameRules);
    }

    public static function loadFromWorld(World $world): GameRules {
        $worldData = $world->getProvider()->getWorldData();
        if (!$worldData instanceof BaseNbtWorldData) {
            return new GameRules();
        }

        $nbt = $worldData->getCompoundTag()->getCompoundTag("GameRules");
        if ($nbt === null) {
            return new GameRules();
        }

        if ($nbt->count() == 0) { // PocketMine creates GameRules nbt, but without any rules
            return new GameRules();
        }

        return GameRules::unserializeGameRules($nbt);
    }

    /**
     * Unserializes GameRules from World Provider
     */
    public static function unserializeGameRules(CompoundTag $nbt): GameRules {
        return new GameRules(array_map(function (StringTag $stringTag) {
            $value = json_decode($stringTag->getValue());
            if(is_bool($value)) {
                return new BoolGameRule($value, GameRules::$allowPlayersEditGameRulesFromGame);
            }
            if(is_int($value)) {
                return new IntGameRule($value, GameRules::$allowPlayersEditGameRulesFromGame);
            }
            if(is_float($value)) {
                return new FloatGameRule($value, GameRules::$allowPlayersEditGameRulesFromGame);
            }

            throw new InvalidStateException("Received unknown type for '$value'.");
        }, $nbt->getValue()));
    }

    public static function saveForWorld(World $world, GameRules $gameRules): bool {
        $worldData = $world->getProvider()->getWorldData();
        if (!$worldData instanceof BaseNbtWorldData) {
            return false;
        }

        $worldData->getCompoundTag()->setTag("GameRules", GameRules::serializeGameRules($gameRules));
        return true;
    }

    /**
     * Serializes GameRules for World Provider
     */
    public static function serializeGameRules(GameRules $gameRules): CompoundTag {
        $nbt = new CompoundTag();
        /** @var BoolGameRule|IntGameRule|FloatGameRule $gameRule */
        foreach ($gameRules->getGameRules() as $name => $gameRule) {
            if($value = json_encode($gameRule->getValue())) {
                $nbt->setString($name, $value);
                continue;
            }
            throw new UnexpectedValueException("Unable to encode value ({$gameRule->getValue()}) for rule $name.");
        }

        return $nbt;
    }

    /**
     * @return BoolGameRule|IntGameRule|FloatGameRule
     */
    public function getRuleValue(string $name): GameRule {
        if(!array_key_exists($name, $this->gameRules)) {
            throw new InvalidStateException("Requested invalid game rule $name.");
        }

        /** @phpstan-ignore-next-line */
        return $this->gameRules[$name]; // TODO - Find better way to make the analyser happy
    }

    public function setRuleValue(string $name, GameRule $value): void {
        $this->gameRules[$name] = $value;
    }

    public function rulesCount(): int {
        return count($this->gameRules);
    }

    public function applyToPlayer(Player $player): void {
        $pk = new GameRulesChangedPacket();
        $pk->gameRules = $this->gameRules;

        $player->getNetworkSession()->sendDataPacket($pk);
    }

    public function applyToWorld(World $world): void {
        $pk = new GameRulesChangedPacket();
        $pk->gameRules = $this->gameRules;

        foreach ($world->getPlayers() as $player) {
            $player->getNetworkSession()->sendDataPacket($pk);
        }
    }
}