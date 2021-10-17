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
use pocketmine\block\VanillaBlocks;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;

class TallGrassPopulator extends AmountPopulator {

	private bool $allowDoubleGrass = true;

	public function populateObject(ChunkManager $world, int $chunkX, int $chunkZ, Random $random): void {
		if (!$this->getSpawnPositionOn($world->getChunk($chunkX, $chunkZ), $random, [VanillaBlocks::GRASS()], $x, $y, $z)) {
			return;
		}

		if ($this->allowDoubleGrass && $random->nextBoundedInt(5) == 0) {
			$world->setBlockAt($chunkX * 16 + $x, $y, $chunkZ * 16 + $z, VanillaBlocks::DOUBLE_TALLGRASS());
			return;
		}

		$world->setBlockAt($chunkX * 16 + $x, $y, $chunkZ * 16 + $z, VanillaBlocks::TALL_GRASS());
	}

	public function setDoubleGrassAllowed(bool $allowed = true): void {
		$this->allowDoubleGrass = $allowed;
	}
}
