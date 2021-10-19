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
use pocketmine\math\Vector2;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;
use function abs;

class AcaciaTree extends Tree {

	public function placeObject(ChunkManager $level, int $x, int $y, int $z, Random $random) {
		$position = new Vector3($x, $y, $z);
		$i = $random->nextBoundedInt(3) + $random->nextBoundedInt(3) + 5;
		$flag = true;

		if($position->getY() >= 1 && $position->getY() + $i + 1 <= 256) {
			for($j = (int)$position->getY(); $j <= $position->getY() + 1 + $i; ++$j) {
				$k = 1;

				if($j == $position->getY()) {
					$k = 0;
				}

				if($j >= $position->getY() + 1 + $i - 2) {
					$k = 2;
				}

				$vector3 = new Vector3();

				for($l = (int)$position->getX() - $k; $l <= $position->getX() + $k && $flag; ++$l) {
					for($i1 = (int)$position->getZ() - $k; $i1 <= $position->getZ() + $k && $flag; ++$i1) {
						if($j >= 0 && $j < 256) {

							$vector3->setComponents($l, $j, $i1);
							/*if (!$this->canPlaceObject($level, $level->getBlockIdAt((int)$vector3->x, (int)$vector3->y, (int)$vector3->z))) {
								$flag = false;
							}*/
						} else {
							$flag = false;
						}
					}
				}
			}

			if(!$flag) {
				return;
			} else {
				$down = $position->down();
				$block = $level->getBlockIdAt($down->getFloorX(), $down->getFloorY(), $down->getFloorZ());

				if(($block == BlockIds::GRASS || $block == BlockIds::DIRT) && $position->getY() < 256 - $i - 1) {
					/** @phpstan-ignore-next-line */
					$level->setBlockIdAt($position->getX(), $position->getY() - 1, $position->getZ(), BlockIds::DIRT);
					$face = $random->nextRange(0, 3);
					$k2 = $i - $random->nextBoundedInt(4) - 1;
					$l2 = 3 - $random->nextBoundedInt(3);
					$i3 = $position->getFloorX();
					$j1 = $position->getFloorZ();
					$k1 = 0;

					for($l1 = 0; $l1 < $i; ++$l1) {
						$i2 = $position->getFloorY() + $l1;

						if($l1 >= $k2 && $l2 > 0) {
							$i3 += $this->getXYFromDirection($face)->getX();
							$j1 += $this->getXYFromDirection($face)->getY();
							--$l2;
						}

						$blockpos = new Vector3($i3, $i2, $j1);
						$material = $level->getBlockIdAt($blockpos->getFloorX(), $blockpos->getFloorY(), $blockpos->getFloorZ());

						if($material == BlockIds::AIR || $material == BlockIds::LEAVES) {
							$this->placeLogAt($level, $blockpos);
							$k1 = $i2;
						}
					}

					$blockpos2 = new Vector3($i3, $k1, $j1);

					for($j3 = -3; $j3 <= 3; ++$j3) {
						for($i4 = -3; $i4 <= 3; ++$i4) {
							if(abs($j3) != 3 || abs($i4) != 3) {
								/** @phpstan-ignore-next-line */
								$this->placeLeafAt($level, $blockpos2->add($j3, 0, $i4));
							}
						}
					}

					$blockpos2 = $blockpos2->up();

					for($k3 = -1; $k3 <= 1; ++$k3) {
						for($j4 = -1; $j4 <= 1; ++$j4) {
							/** @phpstan-ignore-next-line */
							$this->placeLeafAt($level, $blockpos2->add($k3, 0, $j4));
						}
					}

					$this->placeLeafAt($level, $blockpos2->east(2));
					$this->placeLeafAt($level, $blockpos2->west(2));
					$this->placeLeafAt($level, $blockpos2->south(2));
					$this->placeLeafAt($level, $blockpos2->north(2));
					$i3 = $position->getFloorX();
					$j1 = $position->getFloorZ();
					$face1 = $random->nextRange(0, 3);

					if($face1 != $face) {
						$l3 = $k2 - $random->nextBoundedInt(2) - 1;
						$k4 = 1 + $random->nextBoundedInt(3);
						$k1 = 0;

						for($l4 = $l3; $l4 < $i && $k4 > 0; --$k4) {
							if($l4 >= 1) {
								$j2 = $position->getFloorY() + $l4;
								$i3 += $this->getXYFromDirection($face)->getX();
								$j1 += $this->getXYFromDirection($face)->getY();
								$blockpos1 = new Vector3($i3, $j2, $j1);
								$material1 = $level->getBlockIdAt($blockpos1->getFloorX(), $blockpos1->getFloorY(), $blockpos1->getFloorZ());

								if($material1 == BlockIds::AIR || $material1 == BlockIds::LEAVES) {
									$this->placeLogAt($level, $blockpos1);
									$k1 = $j2;
								}
							}

							++$l4;
						}

						if($k1 > 0) {
							$blockpos3 = new Vector3($i3, $k1, $j1);

							for($i5 = -2; $i5 <= 2; ++$i5) {
								for($k5 = -2; $k5 <= 2; ++$k5) {
									if(abs($i5) != 2 || abs($k5) != 2) {
										/** @phpstan-ignore-next-line */
										$this->placeLeafAt($level, $blockpos3->add($i5, 0, $k5));
									}
								}
							}

							$blockpos3 = $blockpos3->up();

							for($j5 = -1; $j5 <= 1; ++$j5) {
								for($l5 = -1; $l5 <= 1; ++$l5) {
									/** @phpstan-ignore-next-line */
									$this->placeLeafAt($level, $blockpos3->add($j5, 0, $l5));
								}
							}
						}
					}
					return;
				} else {
					return;
				}
			}
		}
	}

	private function getXYFromDirection(int $direction): Vector2 {
		switch($direction) {
			case 0:
				return new Vector2(1, 0);
			case 1:
				return new Vector2(0, 1);
			case 2:
				return new Vector2(-1, 0);
			default:
				return new Vector2(0, -1);
		}
	}

	private function placeLogAt(ChunkManager $worldIn, Vector3 $pos): void {
		$worldIn->setBlockIdAt((int)$pos->getX(), (int)$pos->getY(), (int)$pos->getZ(), BlockIds::WOOD2);
	}

	private function placeLeafAt(ChunkManager $worldIn, Vector3 $pos): void {
		$material = $worldIn->getBlockIdAt($pos->getFloorX(), $pos->getFloorY(), $pos->getFloorZ());

		if($material == BlockIds::AIR || $material == BlockIds::LEAVES) {
			$worldIn->setBlockIdAt((int)$pos->getX(), (int)$pos->getY(), (int)$pos->getZ(), BlockIds::LEAVES2);
		}
	}
}

