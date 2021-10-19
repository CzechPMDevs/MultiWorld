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

use pocketmine\block\BlockIds;
use pocketmine\level\ChunkManager;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;

class SwampTree extends Tree {

	public function placeObject(ChunkManager $level, int $x, int $y, int $z, Random $random): bool {
		$vectorPosition = new Vector3($x, $y, $z);
		$position = new Vector3($vectorPosition->getFloorX(), $vectorPosition->getFloorY(), $vectorPosition->getFloorZ());

		$i = $random->nextBoundedInt(4) + 5;
		$flag = true;

		if($position->getY() >= 1 && $position->getY() + $i + 1 <= 256) {
			for($j = $position->getY(); $j <= $position->getY() + 1 + $i; ++$j) {
				$k = 1;

				if($j == $position->getY()) {
					$k = 0;
				}

				if($j >= $position->getY() + 1 + $i - 2) {
					$k = 3;
				}

				$pos2 = new Vector3();

				for($l = $position->getX() - $k; $l <= $position->getX() + $k && $flag; ++$l) {
					for($i1 = $position->getZ() - $k; $i1 <= $position->getZ() + $k && $flag; ++$i1) {
						if($j >= 0 && $j < 256) {
							$pos2->setComponents($l, $j, $i1);
							/*if (!$this->canGrowInto($worldIn->getBlockIdAt($pos2->x, $pos2->y, $pos2->z))) {
								$flag = false;
							}*/
						} else {
							$flag = false;
						}
					}
				}
			}

			if(!$flag) {
				return false;
			} else {
				$down = $position->down();
				$block = $level->getBlockIdAt($down->getFloorX(), $down->getFloorY(), $down->getFloorZ());

				if(($block == BlockIds::GRASS || $block == BlockIds::DIRT) && $position->getY() < 256 - $i - 1) {
					$level->setBlockIdAt($down->getFloorX(), $down->getFloorY(), $down->getFloorZ(), BlockIds::DIRT);

					for($k1 = $position->getY() - 3 + $i; $k1 <= $position->getY() + $i; ++$k1) {
						$j2 = $k1 - ($position->getY() + $i);
						$l2 = 2 - $j2 / 2;

						for($j3 = $position->getX() - $l2; $j3 <= $position->getX() + $l2; ++$j3) {
							$k3 = $j3 - $position->getX();

							for($i4 = $position->getZ() - $l2; $i4 <= $position->getZ() + $l2; ++$i4) {
								$j1 = $i4 - $position->getZ();

								if(abs($k3) != $l2 || abs($j1) != $l2 || $random->nextBoundedInt(2) != 0 && $j2 != 0) {
									$id = $level->getBlockIdAt((int)$j3, (int)$k1, (int)$i4);
									if($id == BlockIds::AIR || $id == BlockIds::LEAVES || $id == BlockIds::VINE) {
										$level->setBlockIdAt((int)$j3, (int)$k1, (int)$i4, BlockIds::LEAVES);
									}
								}
							}
						}
					}

					for($l1 = 0; $l1 < $i; ++$l1) {
						$up = $position->up($l1);
						$id = $level->getBlockIdAt($position->getFloorX(), $up->getFloorY(), $position->getFloorZ());

						if($id == BlockIds::AIR || $id == BlockIds::LEAVES || $id == BlockIds::WATER || $id == BlockIds::STILL_WATER) {
							$level->setBlockIdAt((int)$up->getX(), (int)$up->getY(), (int)$up->getZ(), BlockIds::WOOD);
						}
					}

					for($i2 = $position->getY() - 3 + $i; $i2 <= $position->getY() + $i; ++$i2) {
						$k2 = $i2 - ($position->getY() + $i);
						$i3 = 2 - $k2 / 2;
						$pos2 = new Vector3();

						for($l3 = $position->getX() - $i3; $l3 <= $position->getX() + $i3; ++$l3) {
							for($j4 = $position->getZ() - $i3; $j4 <= $position->getZ() + $i3; ++$j4) {
								$pos2->setComponents($l3, $i2, $j4);

								if($level->getBlockIdAt((int)$pos2->x, (int)$pos2->y, (int)$pos2->z) == BlockIds::LEAVES) {
									$blockpos2 = $pos2->west();
									$blockpos3 = $pos2->east();
									$blockpos4 = $pos2->north();
									$blockpos1 = $pos2->south();

									if($random->nextBoundedInt(4) == 0 && $level->getBlockIdAt((int)$blockpos2->x, (int)$blockpos2->y, (int)$blockpos2->z) == BlockIds::AIR) {
										$this->addHangingVine($level, $blockpos2, 8);
									}

									if($random->nextBoundedInt(4) == 0 && $level->getBlockIdAt((int)$blockpos3->x, (int)$blockpos3->y, (int)$blockpos3->z) == BlockIds::AIR) {
										$this->addHangingVine($level, $blockpos3, 2);
									}

									if($random->nextBoundedInt(4) == 0 && $level->getBlockIdAt((int)$blockpos4->x, (int)$blockpos4->y, (int)$blockpos4->z) == BlockIds::AIR) {
										$this->addHangingVine($level, $blockpos4, 1);
									}

									if($random->nextBoundedInt(4) == 0 && $level->getBlockIdAt((int)$blockpos1->x, (int)$blockpos1->y, (int)$blockpos1->z) == BlockIds::AIR) {
										$this->addHangingVine($level, $blockpos1, 4);
									}
								}
							}
						}
					}
					return true;
				} else {
					return false;
				}
			}
		} else {
			return false;
		}
	}

	private function addHangingVine(ChunkManager $worldIn, Vector3 $pos, int $meta): void {
		$this->addVine($worldIn, $pos, $meta);
		$i = 4;

		for($pos = $pos->down(); $i > 0 && $worldIn->getBlockIdAt((int)$pos->x, (int)$pos->y, (int)$pos->z) == BlockIds::AIR; --$i) {
			$this->addVine($worldIn, $pos, $meta);
			$pos = $pos->down();
		}
	}

	private function addVine(ChunkManager $worldIn, Vector3 $pos, int $meta): void {
		$worldIn->setBlockIdAt((int)$pos->getX(), (int)$pos->getY(), (int)$pos->getZ(), BlockIds::VINE);
		$worldIn->setBlockDataAt((int)$pos->getX(), (int)$pos->getY(), (int)$pos->getZ(), $meta);
	}
}

