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
use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\level\ChunkManager;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;

class LakePopulator extends Populator {

	public function populate(ChunkManager $level, int $chunkX, int $chunkZ, Random $random) {
		if($random->nextBoundedInt(7) != 0) {
			return;
		}

		$this->getRandomSpawnPosition($level, $chunkX, $chunkZ, $random, $x, $y, $z);
		$pos = new Vector3($x, $y, $z);

		$blocks = [];

		/** @var Vector3 $vec */
		foreach($this->getRandomShape($random) as $vec) {
			$finalPos = $pos->add($vec);

			$id = $vec->add($pos)->getY() < $pos->getY() ? Block::WATER : Block::AIR;

			$blocks[] = [$finalPos, $id];
			if($id == BlockIds::WATER &&
				/** @phpstan-ignore-next-line */
				in_array(BlockIds::AIR, [$level->getBlockIdAt($finalPos->getX() + 1, $finalPos->getY(), $finalPos->getZ()), $level->getBlockIdAt($finalPos->getX() - 1, $finalPos->getY(), $finalPos->getZ()), $level->getBlockIdAt($finalPos->getX(), $finalPos->getY(), $finalPos->getZ() + 1), $level->getBlockIdAt($finalPos->getX(), $finalPos->getY(), $finalPos->getZ() - 1)])) {
				return;
			}

		}

		foreach($blocks as [$vec, $id]) {
			/** @phpstan-ignore-next-line */
			$level->setBlockIdAt($vec->getX(), $vec->getY(), $vec->getZ(), $id);
			/** @phpstan-ignore-next-line */
			$level->setBlockDataAt($vec->getX(), $vec->getY(), $vec->getZ(), 0);
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