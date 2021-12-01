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

use czechpmdevs\multiworld\generator\normal\populator\Populator;
use Generator;
use pocketmine\block\VanillaBlocks;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;

class LakePopulator extends Populator {

	public function populate(ChunkManager $world, int $chunkX, int $chunkZ, Random $random): void {
		if($random->nextBoundedInt(7) != 0) {
			return;
		}

		$this->getSpawnPosition($world->getChunk($chunkX, $chunkZ), $random, $x, $y, $z);
		$pos = new Vector3($chunkX * 16 + $x, $y, $chunkZ * 16 + $z);

		$blocks = [];

		/** @var Vector3 $vec */
		foreach($this->getRandomShape($random) as $vec) {
			$finalPos = $pos->addVector($vec);

			$block = $vec->addVector($pos)->getY() < $pos->getY() ? VanillaBlocks::WATER() : VanillaBlocks::AIR();

			$blocks[] = [$finalPos, $block];
			/** @phpstan-ignore-next-line */
			if($block->isSameType(VanillaBlocks::WATER()) && ($world->getBlockAt($finalPos->getX() + 1, $finalPos->getY(), $finalPos->getZ()) || $world->getBlockAt($finalPos->getX() - 1, $finalPos->getY(), $finalPos->getZ()) || $world->getBlockAt($finalPos->getX(), $finalPos->getY(), $finalPos->getZ() + 1) || $world->getBlockAt($finalPos->getX(), $finalPos->getY(), $finalPos->getZ() - 1))) {
				return;
			}
		}

		foreach($blocks as [$vec, $block]) {
			/** @phpstan-ignore-next-line */
			$world->setBlockAt($vec->getX(), $vec->getY(), $vec->getZ(), $block);
		}
	}

	/**
	 * @phpstan-return Generator<Vector3>
	 */
	private function getRandomShape(Random $random): Generator {
		for($x = -($random->nextRange(12, 20)); $x < $random->nextRange(12, 20); $x++) {
			$xsqr = $x * $x;
			for($z = -($random->nextRange(12, 20)); $z < $random->nextRange(12, 20); $z++) {
				$zsqr = $z * $z;
				for($y = $random->nextRange(0, 1); $y < $random->nextRange(6, 7); $y++) {
					if(($xsqr * 1.5) + ($zsqr * 1.5) <= $random->nextRange(34, 40)) {
						yield new Vector3($x, $y - 4, $z);
					}
				}
			}
		}
	}
}