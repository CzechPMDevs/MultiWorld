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

use InvalidArgumentException;
use LogicException;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\FloatTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\network\mcpe\protocol\GameRulesChangedPacket;
use pocketmine\network\mcpe\protocol\types\BoolGameRule;
use pocketmine\network\mcpe\protocol\types\FloatGameRule;
use pocketmine\network\mcpe\protocol\types\IntGameRule;
use pocketmine\player\Player;
use pocketmine\world\format\io\data\BaseNbtWorldData;
use pocketmine\world\World;
use UnexpectedValueException;
use function array_key_exists;
use function is_bool;
use function is_float;
use function is_int;
use function json_decode;
use function json_encode;

/** @internal */
final class GameRules {

	/** @var GameRule[] */
	private array $gameRules;

	/**
	 * @param GameRule[] $gameRules Game rules whose are not set, will be replaced by default values
	 * @phpstan-param array<string, GameRule> $gameRules
	 */
	public function __construct(array $gameRules = []) {
		$this->gameRules = $gameRules;
		$this->addMissingRules();
	}

	private function addMissingRules(): void {
		foreach(GameRule::getAll() as $rule) {
			if(!array_key_exists($rule->getRuleName(), $this->gameRules)) {
				$this->gameRules[$rule->getRuleName()] = $rule;
			}
		}
	}

	/**
	 * @return GameRule[]
	 */
	public function getRules(): array {
		return $this->gameRules;
	}

	public function setRule(GameRule $rule): self {
		$this->gameRules[$rule->getRuleName()] = $rule;
		return $this;
	}

	public function getRule(GameRule $rule): GameRule {
		return $this->gameRules[$rule->getRuleName()];
	}

	public static function loadFromWorld(World $world): GameRules {
		$worldData = $world->getProvider()->getWorldData();
		if(!$worldData instanceof BaseNbtWorldData) {
			return new GameRules();
		}

		$nbt = $worldData->getCompoundTag()->getCompoundTag("GameRules");
		if($nbt === null) {
			return new GameRules();
		}

		if($nbt->count() == 0) { // PocketMine creates GameRules nbt, but without any rules
			return new GameRules();
		}

		return GameRules::unserializeGameRules($nbt);
	}

	public static function saveForWorld(World $world, GameRules $gameRules): bool {
		$worldData = $world->getProvider()->getWorldData();
		if(!$worldData instanceof BaseNbtWorldData) {
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
		foreach($gameRules->getRules() as $name => $gameRule) {
			if($value = json_encode($gameRule->getValue())) {
				$nbt->setString($name, $value);
				continue;
			}
			throw new UnexpectedValueException("Unable to encode value ({$gameRule->getValue()}) for rule $name.");
		}

		return $nbt;
	}

	/**
	 * Unserializes GameRules from World Provider
	 */
	public static function unserializeGameRules(CompoundTag $nbt): GameRules {
		$rules = [];
		/** @var StringTag|IntTag|FloatTag $value */
		foreach($nbt->getValue() as $index => $value) {
			$ruleValue = json_decode((string)$value->getValue());
			if(!is_bool($ruleValue) && !is_int($ruleValue) && !is_float($ruleValue)) {
				throw new LogicException("Invalid game rule value for $index");
			}

			$rule = GameRule::fromRuleName($index)->setValue($ruleValue);
			$rules[$rule->getRuleName()] = $rule;
		}

		return new GameRules($rules);
	}

	public function getRuleValue(string $name): GameRule {
		if(!array_key_exists($name, $this->gameRules)) {
			throw new InvalidArgumentException("Requested invalid game rule $name.");
		}

		return $this->gameRules[$name]; // TODO - Find better way to make the analyser happy
	}

	public function applyToPlayer(Player $player): self {
		$pk = new GameRulesChangedPacket();
		$pk->gameRules = $this->gameRules;

		$player->getNetworkSession()->sendDataPacket($pk);

		return $this;
	}

	public function applyToWorld(World $world): self {
		$pk = new GameRulesChangedPacket();
		$pk->gameRules = $this->gameRules;

		foreach($world->getPlayers() as $player) {
			$player->getNetworkSession()->sendDataPacket($pk);
		}

		self::saveForWorld($world, $this);
		return $this;
	}
}