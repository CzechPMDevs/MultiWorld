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

namespace czechpmdevs\multiworld\level\dimension;

use pocketmine\level\Level;
use pocketmine\network\mcpe\protocol\ChangeDimensionPacket;
use pocketmine\Player;
use function strtolower;

class Dimension {

	public const OVERWORLD = 0;
	public const NETHER = 1;
	public const END = 2;

	public static function getDimensionByLevel(Level $level): int {
		return Dimension::getDimensionByGeneratorName($level->getProvider()->getGenerator());
	}

	public static function getDimensionByGeneratorName(string $generatorName): int {
		$generatorName = strtolower($generatorName);
		if($generatorName == "nether" || $generatorName == "hell") {
			return Dimension::NETHER;
		}
		if($generatorName == "end" || $generatorName == "ender") {
			return Dimension::END;
		}

		return Dimension::OVERWORLD;
	}

	public static function sendDimensionToPlayer(Player $player, int $dimension, bool $respawn = false): void {
		$pk = new ChangeDimensionPacket();
		$pk->position = $player->asVector3();
		$pk->dimension = $dimension;
		$pk->respawn = $respawn;

		$player->dataPacket($pk);
	}
}