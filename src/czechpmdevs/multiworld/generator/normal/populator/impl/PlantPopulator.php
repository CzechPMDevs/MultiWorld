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
use czechpmdevs\multiworld\generator\normal\populator\object\Plant;
use pocketmine\block\BlockIds;
use pocketmine\level\ChunkManager;
use pocketmine\utils\Random;
use function array_merge;
use function count;
use function in_array;

class PlantPopulator extends AmountPopulator {

	/** @var Plant[] */
	private array $plants = [];
	/** @var int[] */
	private array $allowedBlocks = [];

	public function addPlant(Plant $plant): void {
		$this->plants[] = $plant;
	}

	public function allowBlockToStayAt(int $blockId): void {
		$this->allowedBlocks[] = $blockId;
	}

	public function populateObject(ChunkManager $level, int $chunkX, int $chunkZ, Random $random): void {
		if(count($this->plants) === 0) {
			return;
		}

		$this->getRandomSpawnPosition($level, $chunkX, $chunkZ, $random, $x, $y, $z);

		if($y !== -1 and $this->canPlantStay($level, $x, $y, $z)) {
			$plant = $random->nextRange(0, count($this->plants) - 1);
			$pY = $y;
			foreach($this->plants[$plant]->blocks as $block) {
				$level->setBlockIdAt($x, $pY, $z, $block->getId());
				$level->setBlockDataAt($x, $pY, $z, $block->getDamage());
				$pY++;
			}
		}
	}

	private function canPlantStay(ChunkManager $level, int $x, int $y, int $z): bool {
		$b = $level->getBlockIdAt($x, $y, $z);
		return ($b === BlockIds::AIR or $b === BlockIds::SNOW_LAYER or $b === BlockIds::WATER) and in_array($level->getBlockIdAt($x, $y - 1, $z), array_merge([BlockIds::GRASS], $this->allowedBlocks));
	}
}