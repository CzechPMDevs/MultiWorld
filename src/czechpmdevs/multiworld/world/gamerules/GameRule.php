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

namespace czechpmdevs\multiworld\world\gamerules;

use czechpmdevs\multiworld\world\gamerules\type\BoolGameRule;
use czechpmdevs\multiworld\world\gamerules\type\IntGameRule;
use LogicException;
use pocketmine\utils\EnumTrait;
use function strtolower;

/**
 * This doc-block is generated automatically, do not modify it manually.
 * This must be regenerated whenever registry members are added, removed or changed.
 * @see \pocketmine\utils\RegistryUtils::_generateMethodAnnotations()
 *
 * @method static BoolGameRule COMMAND_BLOCKS_ENABLED()
 * @method static BoolGameRule COMMAND_BLOCK_OUTPUT()
 * @method static BoolGameRule DO_DAYLIGHT_CYCLE()
 * @method static BoolGameRule DO_ENTITY_DROPS()
 * @method static BoolGameRule DO_FIRE_TICK()
 * @method static BoolGameRule DO_IMMEDIATE_RESPAWN()
 * @method static BoolGameRule DO_INSOMNIA()
 * @method static BoolGameRule DO_MOB_LOOT()
 * @method static BoolGameRule DO_TILE_DROPS()
 * @method static BoolGameRule DO_WEATHER_CYCLE()
 * @method static BoolGameRule DROWNING_DAMAGE()
 * @method static BoolGameRule FALL_DAMAGE()
 * @method static BoolGameRule FIRE_DAMAGE()
 * @method static BoolGameRule FREEZE_DAMAGE()
 * @method static BoolGameRule FUNCTION_COMMAND_LIMIT()
 * @method static BoolGameRule KEEP_INVENTORY()
 * @method static IntGameRule MAX_COMMAND_CHAIN_LENGTH()
 * @method static BoolGameRule MOB_GRIEFING()
 * @method static BoolGameRule NATURAL_REGENERATION()
 * @method static BoolGameRule PVP()
 * @method static IntGameRule RANDOM_TICK_SPEED()
 * @method static BoolGameRule SEND_COMMAND_FEEDBACK()
 * @method static BoolGameRule SHOW_COORDINATES()
 * @method static BoolGameRule SHOW_DEATH_MESSAGE()
 * @method static BoolGameRule SHOW_TAGS()
 * @method static IntGameRule SPAWN_RADIUS()
 * @method static BoolGameRule TNT_EXPLODES()
 */
abstract class GameRule extends \pocketmine\network\mcpe\protocol\types\GameRule {
	use EnumTrait {
		__construct as Enum___construct;
	}

	protected static function setup(): void {
		self::registerAll(
			new BoolGameRule("command_blocks_enabled", "commandBlocksEnabled", true),
			new BoolGameRule("command_block_output", "commandBlockOutput", true),
			new BoolGameRule("do_daylight_cycle", "doDaylightCycle", true),
			new BoolGameRule("do_entity_drops", "doEntityDrops", true),
			new BoolGameRule("do_fire_tick", "doFireTick", true),
			new BoolGameRule("do_insomnia", "doInsomnia", true),
			new BoolGameRule("do_immediate_respawn", "doImmediateRespawn", false),
			new BoolGameRule("do_mob_loot", "doMobLoot", true),
			new BoolGameRule("do_tile_drops", "doTileDrops", true),
			new BoolGameRule("do_weather_cycle", "doWeatherCycle", true),
			new BoolGameRule("drowning_damage", "drowningDamage", true),
			new BoolGameRule("fall_damage", "fallDamage", true),
			new BoolGameRule("fire_damage", "fireDamage", true),
			new BoolGameRule("freeze_damage", "freezeDamage", true),
			new BoolGameRule("function_command_limit", "functionCommandLimit", true),
			new BoolGameRule("keep_inventory", "keepInventory", false),
			new IntGameRule("max_command_chain_length", "maxCommandChainLength", 65536),
			new BoolGameRule("mob_griefing", "mobGriefing", true),
			new BoolGameRule("natural_regeneration", "naturalRegeneration", true),
			new BoolGameRule("pvp", "pvp", true),
			new IntGameRule("random_tick_speed", "randomTickSpeed", 1),
			new BoolGameRule("send_command_feedback", "sendCommandFeedback", true),
			new BoolGameRule("show_coordinates", "showCoordinates", true),
			new BoolGameRule("show_death_message", "showDeathMessage", true),
			new IntGameRule("spawn_radius", "spawnRadius", 5),
			new BoolGameRule("tnt_explodes", "tntExplodes", true),
			new BoolGameRule("show_tags", "showTags", true)
		);
	}

	private string $ruleName;
	private int $type;
	protected bool|int|float $value;

	private bool $isPlayerModifiable = false;

	/** @noinspection PhpMissingParentConstructorInspection */
	protected function __construct(string $enumName, string $ruleName, int $type) {
		$this->Enum___construct($enumName);
		$this->ruleName = $ruleName;
		$this->type = $type;
	}

	public function getRuleName(): string {
		return $this->ruleName;
	}

	public function getTypeId(): int {
		return $this->type;
	}

	/**
	 * @return $this
	 */
	public function setValue(bool|int|float $value): GameRule {
		$this->value = $value;
		return $this;
	}

	public function getValue(): bool|int|float {
		return $this->value;
	}

	public function isPlayerModifiable(): bool {
		return $this->isPlayerModifiable;
	}

	public static function fromRuleName(string $name): GameRule {
		foreach(self::getAll() as $rule) {
			if(strtolower($rule->getRuleName()) === strtolower($name)) {
				return $rule;
			}
		}

		throw new LogicException("Requested unknown rule $name");
	}
}
