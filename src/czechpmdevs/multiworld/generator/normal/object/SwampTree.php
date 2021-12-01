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
use pocketmine\math\Vector3;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use function abs;

class SwampTree extends Tree {

	public function placeObject(ChunkManager $world, int $x, int $y, int $z, Random $random): void {
		$vectorPosition = new Vector3($x, $y, $z);
		$position = new Vector3($vectorPosition->getFloorX(), $vectorPosition->getFloorY(), $vectorPosition->getFloorZ());

		$i = $random->nextBoundedInt(4) + 5;

		$down = $position->down();
		$block = $world->getBlockAt($down->getFloorX(), $down->getFloorY(), $down->getFloorZ())->getId();

		if(($block == BlockLegacyIds::GRASS || $block == BlockLegacyIds::DIRT) && $position->getY() < 256 - $i - 1) {
			$world->setBlockAt($down->getFloorX(), $down->getFloorY(), $down->getFloorZ(), VanillaBlocks::DIRT());

			for($k1 = $position->getY() - 3 + $i; $k1 <= $position->getY() + $i; ++$k1) {
				$j2 = $k1 - ($position->getY() + $i);
				$l2 = 2 - $j2 / 2;

				for($j3 = $position->getX() - $l2; $j3 <= $position->getX() + $l2; ++$j3) {
					$k3 = $j3 - $position->getX();

					for($i4 = $position->getZ() - $l2; $i4 <= $position->getZ() + $l2; ++$i4) {
						$j1 = $i4 - $position->getZ();

						if(abs($k3) != $l2 || abs($j1) != $l2 || $random->nextBoundedInt(2) != 0 && $j2 != 0) {
							$id = $world->getBlockAt((int)$j3, (int)$k1, (int)$i4)->getId();
							if($id == BlockLegacyIds::AIR || $id == BlockLegacyIds::LEAVES || $id == BlockLegacyIds::VINE) {
								$world->setBlockAt((int)$j3, (int)$k1, (int)$i4, VanillaBlocks::OAK_LEAVES());
							}
						}
					}
				}
			}

			for($l1 = 0; $l1 < $i; ++$l1) {
				$up = $position->up($l1);
				$id = $world->getBlockAt($position->getFloorX(), $up->getFloorY(), $position->getFloorZ())->getId();

				if($id == BlockLegacyIds::AIR || $id == BlockLegacyIds::LEAVES || $id == BlockLegacyIds::WATER || $id == BlockLegacyIds::STILL_WATER) {
					$world->setBlockAt((int)$up->getX(), (int)$up->getY(), (int)$up->getZ(), VanillaBlocks::OAK_WOOD());
				}
			}

			for($i2 = $position->getY() - 3 + $i; $i2 <= $position->getY() + $i; ++$i2) {
				$k2 = $i2 - ($position->getY() + $i);
				$i3 = 2 - $k2 / 2;

				for($l3 = $position->getX() - $i3; $l3 <= $position->getX() + $i3; ++$l3) {
					for($j4 = $position->getZ() - $i3; $j4 <= $position->getZ() + $i3; ++$j4) {
						$pos2 = new Vector3($l3, $i2, $j4);

						if($world->getBlockAt((int)$pos2->x, (int)$pos2->y, (int)$pos2->z)->getId() == BlockLegacyIds::LEAVES) {
							$blockpos2 = $pos2->west();
							$blockpos3 = $pos2->east();
							$blockpos4 = $pos2->north();
							$blockpos1 = $pos2->south();

							if($random->nextBoundedInt(4) == 0 && $world->getBlockAt((int)$blockpos2->x, (int)$blockpos2->y, (int)$blockpos2->z)->getId() == BlockLegacyIds::AIR) {
								$this->addHangingVine($world, $blockpos2);
							}

							if($random->nextBoundedInt(4) == 0 && $world->getBlockAt((int)$blockpos3->x, (int)$blockpos3->y, (int)$blockpos3->z)->getId() == BlockLegacyIds::AIR) {
								$this->addHangingVine($world, $blockpos3);
							}

							if($random->nextBoundedInt(4) == 0 && $world->getBlockAt((int)$blockpos4->x, (int)$blockpos4->y, (int)$blockpos4->z)->getId() == BlockLegacyIds::AIR) {
								$this->addHangingVine($world, $blockpos4);
							}

							if($random->nextBoundedInt(4) == 0 && $world->getBlockAt((int)$blockpos1->x, (int)$blockpos1->y, (int)$blockpos1->z)->getId() == BlockLegacyIds::AIR) {
								$this->addHangingVine($world, $blockpos1);
							}
						}
					}
				}
			}
		}
	}

	private function addHangingVine(ChunkManager $worldIn, Vector3 $pos): void {
		$this->addVine($worldIn, $pos);
		$i = 4;

		for($pos = $pos->down(); $i > 0 && $worldIn->getBlockAt((int)$pos->x, (int)$pos->y, (int)$pos->z)->getId() == BlockLegacyIds::AIR; --$i) {
			$this->addVine($worldIn, $pos);
			$pos = $pos->down();
		}
	}

	private function addVine(ChunkManager $worldIn, Vector3 $pos): void {
		$worldIn->setBlockAt((int)$pos->getX(), (int)$pos->getY(), (int)$pos->getZ(), VanillaBlocks::VINES());
	}
}

