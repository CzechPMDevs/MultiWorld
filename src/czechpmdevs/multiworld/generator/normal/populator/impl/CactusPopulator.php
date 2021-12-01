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

class CactusPopulator extends AmountPopulator {

	public function populateObject(ChunkManager $world, int $chunkX, int $chunkZ, Random $random): void {
		if(!$this->getSpawnPositionOn($world->getChunk($chunkX, $chunkZ), $random, [VanillaBlocks::SAND(), VanillaBlocks::RED_SAND()], $x, $y, $z)) {
			return;
		}

		$x += $chunkX * 16;
		$z += $chunkZ * 16;

		if(
			!$world->getBlockAt($x + 1, $y, $z)->isSameType(VanillaBlocks::AIR()) ||
			!$world->getBlockAt($x, $y, $z + 1)->isSameType(VanillaBlocks::AIR()) ||
			!$world->getBlockAt($x - 1, $y, $z)->isSameType(VanillaBlocks::AIR()) ||
			!$world->getBlockAt($x, $y, $z - 1)->isSameType(VanillaBlocks::AIR())
		) {
			return;
		}

		$size = $random->nextBoundedInt(4);
		for($i = 0; $i < $size; ++$i) {
			$world->setBlockAt($x, $y + $i, $z, VanillaBlocks::CACTUS());
		}
	}
}