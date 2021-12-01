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

namespace czechpmdevs\multiworld\generator\normal\populator\impl;

use czechpmdevs\multiworld\generator\normal\populator\AmountPopulator;
use czechpmdevs\multiworld\generator\normal\populator\impl\plant\Plant;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use function count;

class PlantPopulator extends AmountPopulator {

	/** @var Plant[] */
	private array $plants = [];

	public function addPlant(Plant $plant): void {
		$this->plants[] = $plant;
	}

	public function populateObject(ChunkManager $world, int $chunkX, int $chunkZ, Random $random): void {
		if(count($this->plants) === 0) {
			return;
		}

		$plant = $this->plants[$random->nextBoundedInt(count($this->plants))];
		if($this->getSpawnPositionOn($world->getChunk($chunkX, $chunkZ), $random, $plant->getAllowedUnderground(), $x, $y, $z)) {
			$world->setBlockAt($chunkX * 16 + $x, $y, $chunkZ * 16 + $z, $plant->getBlock());
		}
	}
}