<?php

/**
 * MultiWorld - PocketMine plugin that manages worlds.
 * Copyright (C) 2018 - 2022  CzechPMDevs
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

class GlowstoneSphere implements Populator {

	public const SPHERE_RADIUS = 3;

	public function populate(ChunkManager $world, int $chunkX, int $chunkZ, Random $random): void {
		$world->getChunk($chunkX, $chunkZ);
		if($random->nextRange(0, 10) !== 0) return;

		$x = $random->nextRange($chunkX << 4, ($chunkX << 4) + 15);
		$z = $random->nextRange($chunkZ << 4, ($chunkZ << 4) + 15);

		$sphereY = 0;

		for($y = 0; $y < 127; $y++) {
			if($world->getBlockAt($x, $y, $z)->isSameType(VanillaBlocks::AIR())) {
				$sphereY = $y;
			}
		}

		if($sphereY < 80) {
			return;
		}

		$this->placeGlowstoneSphere($world, $random, new Vector3($x, $sphereY - $random->nextRange(2, 4), $z));
	}

	public function placeGlowStoneSphere(ChunkManager $world, Random $random, Vector3 $position): void {
		$minX = $position->getX() - $this->getRandomRadius($random);
		$maxX = $position->getX() + $this->getRandomRadius($random);
		$minY = $position->getY() - $this->getRandomRadius($random);
		$maxY = $position->getY() + $this->getRandomRadius($random);
		$minZ = $position->getZ() - $this->getRandomRadius($random);
		$maxZ = $position->getZ() + $this->getRandomRadius($random);
		for($x = $minX; $x < $maxX; ++$x) {
			$xsqr = ($position->getX() - $x) * ($position->getX() - $x);
			for($y = $minY; $y < $maxY; ++$y) {
				$ysqr = ($position->getY() - $y) * ($position->getY() - $y);
				for($z = $minZ; $z < $maxZ; ++$z) {
					$zsqr = ($position->getZ() - $z) * ($position->getZ() - $z);
					if(($xsqr + $ysqr + $zsqr) < (pow(2, $this->getRandomRadius($random)))) {
						if($random->nextRange(0, 4) !== 0) {
							/** @phpstan-ignore-next-line */
							$world->setBlockAt($x, $y, $z, VanillaBlocks::GLOWSTONE());
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