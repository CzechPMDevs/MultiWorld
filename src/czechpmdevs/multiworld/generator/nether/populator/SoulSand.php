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

use pocketmine\block\VanillaBlocks;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\generator\populator\Populator;
use function pow;

class SoulSand implements Populator {

	public function populate(ChunkManager $world, int $chunkX, int $chunkZ, Random $random): void {
		if ($random->nextRange(0, 6) !== 0) return;

		$x = $random->nextRange($chunkX << 4, ($chunkX << 4) + 15);
		$z = $random->nextRange($chunkZ << 4, ($chunkZ << 4) + 15);

		$sphereY = 0;

		for ($y = 45; $y > 0; $y--) {
			if ($world->getBlockAt($x, $y, $z)->isSameType(VanillaBlocks::AIR())) {
				$sphereY = $y;
			}
		}

		if ($sphereY - 3 < 2) {
			return;
		}

		if (!$world->getBlockAt($x, $sphereY - 3, $z)->isSameType(VanillaBlocks::NETHERRACK())) {
			return;
		}

		$this->placeSoulSand($world, $random, new Vector3($x, $sphereY - $random->nextRange(2, 4), $z));
	}

	public function placeSoulSand(ChunkManager $world, Random $random, Vector3 $position): void {
		$radiusX = $random->nextRange(8, 15);
		$radiusZ = $random->nextRange(8, 15);
		$radiusY = $random->nextRange(5, 8);
		for ($x = $position->getX() - $radiusX; $x < $position->getX() + $radiusX; $x++) {
			$xsqr = ($position->getX() - $x) * ($position->getX() - $x);
			for ($y = $position->getY() - $radiusY; $y < $position->getY() + $radiusY; $y++) {
				$ysqr = ($position->getY() - $y) * ($position->getY() - $y);
				for ($z = $position->getZ() - $radiusZ; $z < $position->getZ() + $radiusZ; $z++) {
					$zsqr = ($position->getZ() - $z) * ($position->getZ() - $z);
					if (($xsqr + $ysqr + $zsqr) < (pow(2, $random->nextRange(3, 6)))) {
						/** @phpstan-ignore-next-line */
						if ($world->getBlockAt($x, $y, $z)->isSameType(VanillaBlocks::NETHERRACK())) {
							/** @phpstan-ignore-next-line */
							$world->setBlockAt($x, $y, $z, VanillaBlocks::SOUL_SAND());
							/** @phpstan-ignore-next-line */
							if ($random->nextRange(0, 3) == 3 && $world->getBlockAt($x, $y + 1, $z)->isSameType(VanillaBlocks::AIR())) {
								/** @phpstan-ignore-next-line */
								$world->setBlockAt($x, $y + 1, $z, VanillaBlocks::NETHER_WART());
							}
						}
					}
				}
			}
		}
	}

}