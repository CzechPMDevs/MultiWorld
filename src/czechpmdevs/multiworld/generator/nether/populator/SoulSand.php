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

namespace czechpmdevs\multiworld\generator\nether\populator;

use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\populator\Populator;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;

class SoulSand extends Populator {

	public function populate(ChunkManager $level, int $chunkX, int $chunkZ, Random $random) {
		if($random->nextRange(0, 6) !== 0) return;

		$x = $random->nextRange($chunkX << 4, ($chunkX << 4) + 15);
		$z = $random->nextRange($chunkZ << 4, ($chunkZ << 4) + 15);

		$sphereY = 0;

		for($y = 45; $y > 0; $y--) {
			if($level->getBlockIdAt($x, $y, $z) == 0) {
				$sphereY = $y;
			}
		}

		if($sphereY - 3 < 2) {
			return;
		}

		if($level->getBlockIdAt($x, $sphereY - 3, $z) != Block::NETHERRACK) {
			return;
		}

		$this->placeSoulSand($level, $random, new Vector3($x, $sphereY - $random->nextRange(2, 4), $z));
	}

	public function placeSoulSand(ChunkManager $level, Random $random, Vector3 $position): void {
		$radiusX = $random->nextRange(8, 15);
		$radiusZ = $random->nextRange(8, 15);
		$radiusY = $random->nextRange(5, 8);
		for($x = $position->getX() - $radiusX; $x < $position->getX() + $radiusX; $x++) {
			$xsqr = ($position->getX() - $x) * ($position->getX() - $x);
			for($y = $position->getY() - $radiusY; $y < $position->getY() + $radiusY; $y++) {
				$ysqr = ($position->getY() - $y) * ($position->getY() - $y);
				for($z = $position->getZ() - $radiusZ; $z < $position->getZ() + $radiusZ; $z++) {
					$zsqr = ($position->getZ() - $z) * ($position->getZ() - $z);
					if(($xsqr + $ysqr + $zsqr) < (pow(2, $random->nextRange(3, 6)))) {
						/** @phpstan-ignore-next-line */
						if($level->getBlockIdAt($x, $y, $z) == Block::NETHERRACK) {
							/** @phpstan-ignore-next-line */
							$level->setBlockIdAt($x, $y, $z, Block::SOUL_SAND);
							/** @phpstan-ignore-next-line */
							if($random->nextRange(0, 3) == 3 && $level->getBlockIdAt($x, $y + 1, $z) == Block::AIR) {
								/** @phpstan-ignore-next-line */
								$level->setBlockIdAt($x, $y + 1, $z, Block::NETHER_WART_PLANT);
							}
						}
					}
				}
			}
		}
	}

}