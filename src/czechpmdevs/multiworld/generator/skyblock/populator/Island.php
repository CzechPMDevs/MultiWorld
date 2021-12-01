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

use pocketmine\block\VanillaBlocks;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\generator\object\OakTree;
use pocketmine\world\generator\populator\Populator;

class Island implements Populator {

	public function populate(ChunkManager $world, int $chunkX, int $chunkZ, Random $random): void {
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
						$world->setBlockAt($centerVec->getX(), $centerVec->getY(), $centerVec->getZ(), VanillaBlocks::GRASS());
					} else {
						/** @phpstan-ignore-next-line */
						$world->setBlockAt($centerVec->getX(), $centerVec->getY(), $centerVec->getZ(), VanillaBlocks::DIRT());
					}

					// left
					$leftVec = $center->add(3, 0, 0)->add($x, $y, $z);
					if($leftVec->getY() == 69) {
						/** @phpstan-ignore-next-line */
						$world->setBlockAt($leftVec->getX(), $leftVec->getY(), $leftVec->getZ(), VanillaBlocks::GRASS());
					} else {
						/** @phpstan-ignore-next-line */
						$world->setBlockAt($leftVec->getX(), $leftVec->getY(), $leftVec->getZ(), VanillaBlocks::DIRT());
					}

					// down
					$downVec = $center->subtract(0, 0, 3)->add($x, $y, $z);
					if($leftVec->getY() == 69) {
						/** @phpstan-ignore-next-line */
						$world->setBlockAt($downVec->getX(), $downVec->getY(), $downVec->getZ(), VanillaBlocks::GRASS());
					} else {
						/** @phpstan-ignore-next-line */
						$world->setBlockAt($downVec->getX(), $downVec->getY(), $downVec->getZ(), VanillaBlocks::DIRT());
					}
				}
			}
		}

		// chest
		$chestVec = $center->add(0, 2, -4);
		/** @phpstan-ignore-next-line */
		$world->setBlockAt($chestVec->getX(), $chestVec->getY(), $chestVec->getZ(), VanillaBlocks::CHEST());

		// tree
		$treeVec = $center->add(4, 2, 1);
		$tree = new OakTree;

		/** @phpstan-ignore-next-line */
		$tree->getBlockTransaction($world, $treeVec->getX(), $treeVec->getY(), $treeVec->getZ(), $random)->apply();

		// bedrock
		/** @phpstan-ignore-next-line */
		$world->setBlockAt($center->getX(), $center->getY(), $center->getZ(), VanillaBlocks::BEDROCK());
	}
}