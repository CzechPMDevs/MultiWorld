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

namespace czechpmdevs\multiworld\generator\normal\object;

use pocketmine\block\utils\MushroomBlockType;
use pocketmine\block\VanillaBlocks;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use function abs;

class HugeMushroom extends Tree {

//    public function __construct() {
//        $this->overridable[BlockLegacyIds::MYCELIUM] = true;
//    }

	public function placeObject(ChunkManager $world, int $spawnX, int $spawnY, int $spawnZ, Random $random): void {
		$block = $random->nextBoolean() ? VanillaBlocks::BROWN_MUSHROOM_BLOCK() : VanillaBlocks::RED_MUSHROOM_BLOCK();
		$maxY = 3 + $random->nextBoundedInt(1);

		for ($i = 0; $i <= $maxY; $i++) {
			$world->setBlockAt($spawnX, $spawnY + $i, $spawnZ, $block);
		}

		switch ($block->isSameType(VanillaBlocks::RED_MUSHROOM_BLOCK())) {
			case true:
				$data = 0;
				for ($i = -1; $i <= 1; $i++) {
					for ($j = -1; $j <= 1; $j++) {
						$world->setBlockAt($spawnX + $j, $spawnY + $maxY + 1, $spawnZ + $i, $block = $block->setMushroomBlockType(MushroomBlockType::getAll()[++$data]));

						for ($y = $maxY; $y >= 1; $y--) {
							if (abs($i) == 1 && abs($j) == 1) {
								$i1 = $i < 0 ? $i - 1 : $i + 1;
								$j1 = $j < 0 ? $j - 1 : $j + 1;

								$world->setBlockAt($spawnX + $j1, $y + $spawnY, $spawnZ + $i, $block);
								$world->setBlockAt($spawnX + $j, $y + $spawnY, $spawnZ + $i1, $block);
							} else {
								$i1 = $i < 0 ? $i - 1 : ($i > 0 ? $i + 1 : $i);
								$j1 = $j < 0 ? $j - 1 : ($j > 0 ? $j + 1 : $j);

								if ($j1 == $i1 && $j1 == 0) {
									continue;
								}

								$world->setBlockAt($spawnX + $j1, $y + $spawnY, $spawnZ + $i1, $block);
							}
						}
					}
				}
				break;

			case false:
				for ($i = -2; $i <= 2; $i++) {
					for ($j = -2; $j <= 2; $j++) {
						$world->setBlockAt($spawnX + $j, $maxY + 1 + $spawnY, $spawnZ + $i, $block->setMushroomBlockType(MushroomBlockType::CAP_MIDDLE()));
					}
				}

				$data = 0;
				for ($i = -1; $i <= 1; $i++) {
					for ($j = -1; $j <= 1; $j++) {
						$data++;

						$i1 = $i * 3; // z
						$j1 = $j * 3; // x
						if (abs($i1) == 3 && abs($j1) == 3) {
							$i11 = $i1 < 0 ? $i1 + 1 : $i1 - 1;
							$j11 = $j1 < 0 ? $j1 + 1 : $j1 - 1;

							$world->setBlockAt($spawnX + $j1, $maxY + 1 + $spawnY, $spawnZ + $i11, $block = $block->setMushroomBlockType(MushroomBlockType::getAll()[$data]));
							$world->setBlockAt($spawnX + $j11, $maxY + 1 + $spawnY, $spawnZ + $i1, $block);
						} else {
							if ($i1 === 0) {
								$world->setBlockAt($spawnX + $j1, $maxY + 1 + $spawnY, $spawnZ + 1, $block = $block->setMushroomBlockType(MushroomBlockType::getAll()[$data]));
								$world->setBlockAt($spawnX + $j1, $maxY + 1 + $spawnY, $spawnZ, $block);
								$world->setBlockAt($spawnX + $j1, $maxY + 1 + $spawnY, $spawnZ - 1, $block);
							} else {
								$world->setBlockAt($spawnX + 1, $maxY + 1 + $spawnY, $spawnZ + $i1, $block);
								$world->setBlockAt($spawnX, $maxY + 1 + $spawnY, $spawnZ + $i1, $block);
								$world->setBlockAt($spawnX - 1, $maxY + 1 + $spawnY, $spawnZ + $i1, $block);
							}
						}
					}
				}
				break;
		}
	}
}