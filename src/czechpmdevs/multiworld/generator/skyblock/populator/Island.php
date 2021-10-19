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

namespace czechpmdevs\multiworld\generator\skyblock\populator;

use pocketmine\block\BlockIds;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\object\OakTree;
use pocketmine\level\generator\populator\Populator;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;

class Island extends Populator {

	public function populate(ChunkManager $level, int $chunkX, int $chunkZ, Random $random) {
		$center = new Vector3(256, 68, 256);

		for($x = -1; $x <= 1; $x++) {
			/** @phpstan-var float $x */
			for($y = -1; $y <= 1; $y++) {
				/** @phpstan-var float $y */
				for($z = -1; $z <= 1; $z++) {
					/** @phpstan-var float $z */

					// center
					$centerVec = $center->add($x, $y, $z);
					if($centerVec->getY() == 69) {
						/** @phpstan-ignore-next-line */
						$level->setBlockIdAt($centerVec->getX(), $centerVec->getY(), $centerVec->getZ(), BlockIds::GRASS);
					} else {
						/** @phpstan-ignore-next-line */
						$level->setBlockIdAt($centerVec->getX(), $centerVec->getY(), $centerVec->getZ(), BlockIds::DIRT);
					}

					// left
					$leftVec = $center->add(3)->add($x, $y, $z);
					if($leftVec->getY() == 69) {
						/** @phpstan-ignore-next-line */
						$level->setBlockIdAt($leftVec->getX(), $leftVec->getY(), $leftVec->getZ(), BlockIds::GRASS);
					} else {
						/** @phpstan-ignore-next-line */
						$level->setBlockIdAt($leftVec->getX(), $leftVec->getY(), $leftVec->getZ(), BlockIds::DIRT);
					}

					// down
					$downVec = $center->subtract(0, 0, 3)->add($x, $y, $z);
					if($leftVec->getY() == 69) {
						/** @phpstan-ignore-next-line */
						$level->setBlockIdAt($downVec->getX(), $downVec->getY(), $downVec->getZ(), BlockIds::GRASS);
					} else {
						/** @phpstan-ignore-next-line */
						$level->setBlockIdAt($downVec->getX(), $downVec->getY(), $downVec->getZ(), BlockIds::DIRT);
					}
				}
			}
		}

		// chest
		$chestVec = $center->add(0, 2, -4);
		/** @phpstan-ignore-next-line */
		$level->setBlockIdAt($chestVec->getX(), $chestVec->getY(), $chestVec->getZ(), BlockIds::CHEST);

		// tree
		$treeVec = $center->add(4, 2, 1);
		$tree = new OakTree;

		/** @phpstan-ignore-next-line */
		$tree->placeObject($level, $treeVec->getX(), $treeVec->getY(), $treeVec->getZ(), $random);

		// bedrock
		/** @phpstan-ignore-next-line */
		$level->setBlockIdAt($center->getX(), $center->getY(), $center->getZ(), BlockIds::BEDROCK);
	}
}