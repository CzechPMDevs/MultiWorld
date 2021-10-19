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

use pocketmine\block\BlockIds;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\populator\Populator;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;

class GlowstoneSphere extends Populator {

	public const SPHERE_RADIUS = 3;

	public function populate(ChunkManager $level, int $chunkX, int $chunkZ, Random $random) {
		$level->getChunk($chunkX, $chunkZ);
		if($random->nextRange(0, 10) !== 0) return;

		$x = $random->nextRange($chunkX << 4, ($chunkX << 4) + 15);
		$z = $random->nextRange($chunkZ << 4, ($chunkZ << 4) + 15);

		$sphereY = 0;

		for($y = 0; $y < 127; $y++) {
			if($level->getBlockIdAt($x, $y, $z) == 0) {
				$sphereY = $y;
			}
		}

		if($sphereY < 80) {
			return;
		}

		$this->placeGlowstoneSphere($level, $random, new Vector3($x, $sphereY - $random->nextRange(2, 4), $z));
	}

	public function placeGlowStoneSphere(ChunkManager $level, Random $random, Vector3 $position): void {
		for($x = $position->getX() - $this->getRandomRadius($random); $x < $position->getX() + $this->getRandomRadius($random); $x++) {
			$xsqr = ($position->getX() - $x) * ($position->getX() - $x);
			for($y = $position->getY() - $this->getRandomRadius($random); $y < $position->getY() + $this->getRandomRadius($random); $y++) {
				$ysqr = ($position->getY() - $y) * ($position->getY() - $y);
				for($z = $position->getZ() - $this->getRandomRadius($random); $z < $position->getZ() + $this->getRandomRadius($random); $z++) {
					$zsqr = ($position->getZ() - $z) * ($position->getZ() - $z);
					if(($xsqr + $ysqr + $zsqr) < (pow(2, $this->getRandomRadius($random)))) {
						if($random->nextRange(0, 4) !== 0) {
							/** @phpstan-ignore-next-line */
							$level->setBlockIdAt($x, $y, $z, BlockIds::GLOWSTONE);
						}
					}
				}
			}
		}
	}

	public function getRandomRadius(Random $random): int {
		return $random->nextRange(self::SPHERE_RADIUS, self::SPHERE_RADIUS + 2);
	}

}