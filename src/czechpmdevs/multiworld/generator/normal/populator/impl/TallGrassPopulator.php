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
use pocketmine\block\BlockIds;
use pocketmine\level\ChunkManager;
use pocketmine\utils\Random;

class TallGrassPopulator extends AmountPopulator {

	/** @var bool */
	private bool $allowDoubleGrass = true;

	public function populateObject(ChunkManager $level, int $chunkX, int $chunkZ, Random $random): void {
		$this->getRandomSpawnPosition($level, $chunkX, $chunkZ, $random, $x, $y, $z);

		if($y !== -1 and $this->canTallGrassStay($level, $x, $y, $z)) {
			$id = ($this->allowDoubleGrass && $random->nextBoundedInt(5) == 4) ? BlockIds::DOUBLE_PLANT : BlockIds::TALL_GRASS;
			$level->setBlockIdAt($x, $y, $z, $id);

			if($id == BlockIds::DOUBLE_PLANT) {
				$level->setBlockDataAt($x, $y, $z, 2);
				$level->setBlockIdAt($x, $y + 1, $z, $id);
				$level->setBlockDataAt($x, $y + 1, $z, 10);
			}
		}
	}

	private function canTallGrassStay(ChunkManager $level, int $x, int $y, int $z): bool {
		$b = $level->getBlockIdAt($x, $y, $z);
		return ($b === BlockIds::AIR or $b === BlockIds::SNOW_LAYER) and $level->getBlockIdAt($x, $y - 1, $z) === BlockIds::GRASS;
	}

	public function setDoubleGrassAllowed(bool $allowed = true): void {
		$this->allowDoubleGrass = $allowed;
	}
}
