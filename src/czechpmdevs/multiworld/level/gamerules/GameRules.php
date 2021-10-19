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
use pocketmine\level\format\io\BaseLevelProvider;
use pocketmine\level\Level;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\NamedTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\Player;
use function array_filter;
use function array_key_exists;
use function array_map;
use function count;
use function is_bool;
use function is_int;

final class GameRules {

	public const TYPE_INVALID = 0;
	public const TYPE_BOOL = 1;
	public const TYPE_INTEGER = 2;

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

	/**
	 * @noinspection PhpPluralMixedCanBeReplacedWithArrayInspection
	 *
	 * @var mixed[]
	 * @phpstan-var array<string, bool|int|float>
	 */
	private array $gameRules = [];

	/**
	 * @noinspection PhpPluralMixedCanBeReplacedWithArrayInspection
	 *
	 * @param mixed[] $gameRules If the array is empty, default GameRules will be used
	 * @phpstan-param array<string, bool|int|float> $gameRules
	 */
	public function __construct(array $gameRules = []) {
		// In case for default GameRules
		if(!isset(GameRules::$defaultGameRules)) {
			$this->gameRules = $gameRules;
			return;
		}

		if(empty($gameRules)) {
			$this->gameRules = GameRules::getDefaultGameRules()->getGameRules();
			return;
		}

		// Removing invalid GameRules
		foreach($gameRules as $key => $value) {
			if(!GameRules::$defaultGameRules->keyExists($key)) {
				unset($gameRules[$key]); // GameRule does not exist at all
			} elseif(GameRules::getPropertyType($value) != GameRules::getPropertyType(GameRules::$defaultGameRules->getGameRules()[$key])) {
				unset($gameRules[$key]); // GameRule has invalid type
			}
		}

		// Adding new GameRules
		foreach(GameRules::$defaultGameRules->gameRules as $key => $gameRule) {
			if(!array_key_exists($key, $gameRules)) {
				$gameRules[$key] = $gameRule;
			}
		}

		$this->gameRules = $gameRules;
	}

	/**
	 * @noinspection PhpPluralMixedCanBeReplacedWithArrayInspection
	 *
	 * @return mixed[]
	 * @phpstan-return array<string, int|float|bool>
	 */
	public function getGameRules(): array {
		return $this->gameRules;
	}

	public static function getDefaultGameRules(): GameRules {
		return clone GameRules::$defaultGameRules;
	}

	public function keyExists(string $index): bool {
		return array_key_exists($index, $this->gameRules);
	}

	/**
	 * @param mixed $value
	 */
	public static function getPropertyType($value): int {
		if(is_bool($value)) {
			return GameRules::TYPE_BOOL;
		}
		if(is_int($value)) {
			return GameRules::TYPE_INTEGER;
		}

		return GameRules::TYPE_INVALID;
	}

	/**
	 * @noinspection PhpPluralMixedCanBeReplacedWithArrayInspection
	 *
	 * @param mixed[] $defaultGameRules
	 * @phpstan-var array<string, bool|int|float>
	 */
	public static function init(array $defaultGameRules): void {
		GameRules::$defaultGameRules = new GameRules($defaultGameRules);
	}

	public static function loadFromLevel(Level $level): GameRules {
		$provider = $level->getProvider();
		if(!$provider instanceof BaseLevelProvider) {
			return new GameRules();
		}

		$nbt = $provider->getLevelData()->getCompoundTag("GameRules");
		if($nbt === null) {
			return new GameRules();
		}

		if($nbt->count() == 0) { // PocketMine creates GameRules nbt, but without any rules
			return new GameRules();
		}

		return GameRules::unserializeGameRules($nbt);
	}

	/**
	 * Unserializes GameRules from World Provider
	 */
	public static function unserializeGameRules(CompoundTag $nbt): GameRules {
		return new GameRules(array_map(function(StringTag $stringTag) {
			if($stringTag->getValue() == "true") {
				return true;
			}
			if($stringTag->getValue() == "false") {
				return false;
			}

			return (int)$stringTag->getValue();
		}, array_filter($nbt->getValue(), fn(NamedTag $tag) => $tag instanceof StringTag))); // There are some programs whose saving those incorrect. Although it makes more sense than Mojang's way, it won't be supported.
	}

	public static function saveForLevel(Level $level, GameRules $gameRules): bool {
		$provider = $level->getProvider();
		if(!$provider instanceof BaseLevelProvider) {
			return false;
		}

		$provider->getLevelData()->setTag(GameRules::serializeGameRules($gameRules));
		return true;
	}

	/**
	 * Serializes GameRules for World Provider
	 */
	public static function serializeGameRules(GameRules $gameRules): CompoundTag {
		/** @var StringTag[] $stringTagArray */
		$stringTagArray = [];
		foreach($gameRules->getGameRules() as $name => [$type, $value]) {
			if($type == GameRules::TYPE_BOOL) {
				$stringTagArray[$name] = new StringTag($name, $value ? "true" : "false");
			} elseif($type == GameRules::TYPE_INTEGER) {
				$stringTagArray[$name] = new StringTag($name, (string)$value);
			}
		}

		return new CompoundTag("GameRules", $stringTagArray);
	}

	/**
	 * WARNING: This will only change the rule only on the server, to send it
	 * To player use GameRules::applyToPlayer(Player) method
	 */
	public function setBool(string $index, bool $value): void {
		$this->gameRules[$index] = $value;
	}

	public function getBool(string $index): bool {
		$value = $this->gameRules[$index] ?? null;
		if(!is_bool($value)) {
			throw new InvalidStateException("Received invalid type for Game Rule $index, got '$value' expected bool.");
		}

		return $value;
	}

	/**
	 * WARNING: This will only change the rule only on the server, to send it
	 * To player use GameRules::applyToPlayer(Player) method
	 */
	public function setInteger(string $index, int $value): void {
		$this->gameRules[$index] = $value;
	}

	public function getInteger(string $index): int {
		$value = $this->gameRules[$index] ?? null;
		if(!is_int($value)) {
			throw new InvalidStateException("Received invalid type for Game Rule $index, got '$value' expected integer.");
		}

		return $value;
	}

	public function rulesCount(): int {
		return count($this->gameRules);
	}

	public function applyToPlayer(Player $player): void {
		$pk = new GameRulesChangedPacket();
		$pk->gameRules = array_map(fn($gameRule) => [
			GameRules::getPropertyType($gameRule),
			$gameRule,
			GameRules::$allowPlayersEditGameRulesFromGame
		], $this->gameRules);

		$player->dataPacket($pk);
	}

	public function applyToLevel(Level $level): void {
		$pk = new GameRulesChangedPacket();
		$pk->gameRules = array_map(fn($gameRule) => [
			GameRules::getPropertyType($gameRule),
			$gameRule,
			GameRules::$allowPlayersEditGameRulesFromGame
		], $this->gameRules);

		$level->broadcastGlobalPacket($pk);
	}
}