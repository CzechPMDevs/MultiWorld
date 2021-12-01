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

use pocketmine\block\BlockLegacyIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use function abs;

class DarkOakTree extends Tree {

	public function placeObject(ChunkManager $world, int $x, int $y, int $z, Random $random): void {
		$i = $random->nextBoundedInt(3) + $random->nextBoundedInt(2) + 6;

		if($y >= 1 && $y + $i + 1 < 256) {
			$block = $world->getBlockAt($x, $y - 1, $z);

			if(!$block->isSameType(VanillaBlocks::GRASS()) && !$block->isSameType(VanillaBlocks::DIRT())) {
				return;
			} else {
				$world->setBlockAt($x, $y - 1, $z, VanillaBlocks::DIRT());
				$world->setBlockAt($x + 1, $y - 1, $z, VanillaBlocks::DIRT());
				$world->setBlockAt($x + 1, $y - 1, $z + 1, VanillaBlocks::DIRT());
				$world->setBlockAt($x, $y - 1, $z + 1, VanillaBlocks::DIRT());

				$i1 = $i - $random->nextBoundedInt(4);
				$j1 = 2 - $random->nextBoundedInt(3); // x1 -> j1
				$x1 = $x; // k1 -> y1 -> x1
				$z1 = $z;
				$i2 = $y + $i - 1;

				for($x2 = 0; $x2 < $i; ++$x2) {
					if($x2 >= $i1 && $j1 > 0) {
						--$j1;
					}

					$y2 = $y + $x2;
					$material = $world->getBlockAt($x1, $y2, $z1)->getId();

					if($material == BlockLegacyIds::AIR || $material == BlockLegacyIds::LEAVES) {
						$world->setBlockAt($x1, $y2, $z1, VanillaBlocks::DARK_OAK_WOOD());
						$world->setBlockAt($x1 + 1, $y2, $z1, VanillaBlocks::DARK_OAK_WOOD());
						$world->setBlockAt($x1 + 1, $y2, $z1 + 1, VanillaBlocks::DARK_OAK_WOOD());
						$world->setBlockAt($x1, $y2, $z1 + 1, VanillaBlocks::DARK_OAK_WOOD());
					}
				}

				for($i3 = -2; $i3 <= 0; ++$i3) {
					for($l3 = -2; $l3 <= 0; ++$l3) {
						$k4 = -1;
						$world->setBlockAt($x1 + $i3, $i2 + $k4, $z1 + $l3, VanillaBlocks::DARK_OAK_LEAVES());
						$world->setBlockAt(1 + $x1 - $i3, $i2 + $k4, $z1 + $l3, VanillaBlocks::DARK_OAK_LEAVES());
						$world->setBlockAt($x1 + $i3, $i2 + $k4, 1 + $z1 - $l3, VanillaBlocks::DARK_OAK_LEAVES());
						$world->setBlockAt(1 + $x1 - $i3, $i2 + $k4, 1 + $z1 - $l3, VanillaBlocks::DARK_OAK_LEAVES());

						if(($i3 > -2 || $l3 > -1) && ($i3 != -1 || $l3 != -2)) {
							$k4 = 1;
							$world->setBlockAt($x1 + $i3, $i2 + $k4, $z1 + $l3, VanillaBlocks::DARK_OAK_LEAVES());
							$world->setBlockAt(1 + $x1 - $i3, $i2 + $k4, $z1 + $l3, VanillaBlocks::DARK_OAK_LEAVES());
							$world->setBlockAt($x1 + $i3, $i2 + $k4, 1 + $z1 - $l3, VanillaBlocks::DARK_OAK_LEAVES());
							$world->setBlockAt(1 + $x1 - $i3, $i2 + $k4, 1 + $z1 - $l3, VanillaBlocks::DARK_OAK_LEAVES());
						}
					}
				}

				if($random->nextBoolean()) {
					$world->setBlockAt($x1, $i2 + 2, $z1, VanillaBlocks::DARK_OAK_LEAVES());
					$world->setBlockAt($x1 + 1, $i2 + 2, $z1, VanillaBlocks::DARK_OAK_LEAVES());
					$world->setBlockAt($x1, $i2 + 2, $z1 + 1, VanillaBlocks::DARK_OAK_LEAVES());
					$world->setBlockAt($x1 + 1, $i2 + 2, $z1 + 1, VanillaBlocks::DARK_OAK_LEAVES());
				}

				for($j3 = -3; $j3 <= 4; ++$j3) {
					for($i4 = -3; $i4 <= 4; ++$i4) {
						if(($j3 != -3 || $i4 != -3) && ($j3 != -3 || $i4 != 4) && ($j3 != 4 || $i4 != -3) && ($j3 != 4 || $i4 != 4) && (abs($j3) < 3 || abs($i4) < 3)) {
							$world->setBlockAt($x1 + $j3, $i2, $z1 + $i4, VanillaBlocks::DARK_OAK_LEAVES());
						}
					}
				}

				for($k3 = -1; $k3 <= 2; ++$k3) {
					for($j4 = -1; $j4 <= 2; ++$j4) {
						if(($k3 < 0 || $k3 > 1 || $j4 < 0 || $j4 > 1) && $random->nextBoundedInt(3) <= 0) {
							$l4 = $random->nextBoundedInt(3) + 2;

							for($i5 = 0; $i5 < $l4; ++$i5) {
								$world->setBlockAt($x + $k3, $i2 - $i5 - 1, $z + $j4, VanillaBlocks::DARK_OAK_LEAVES());
							}

							for($j5 = -1; $j5 <= 1; ++$j5) {
								for($l2 = -1; $l2 <= 1; ++$l2) {
									$world->setBlockAt($x1 + $k3 + $j5, $i2, $z1 + $j4 + $l2, VanillaBlocks::DARK_OAK_LEAVES());
								}
							}

							for($k5 = -2; $k5 <= 2; ++$k5) {
								for($l5 = -2; $l5 <= 2; ++$l5) {
									if(abs($k5) != 2 || abs($l5) != 2) {
										$world->setBlockAt($x1 + $k3 + $k5, $i2 - 1, $z1 + $j4 + $l5, VanillaBlocks::DARK_OAK_LEAVES());
									}
								}
							}
						}
					}
				}
			}
		}
	}

	public function canPlaceObject(ChunkManager $world, int $x, int $y, int $z, Random $random): bool {
		return parent::canPlaceObject($world, $x, $y, $z, $random) &&
			parent::canPlaceObject($world, $x + 1, $y, $z, $random) &&
			parent::canPlaceObject($world, $x + 1, $y, $z + 1, $random) &&
			parent::canPlaceObject($world, $x, $y, $z + 1, $random);
	}
}