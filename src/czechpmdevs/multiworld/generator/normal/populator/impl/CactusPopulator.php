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

use czechpmdevs\multiworld\generator\normal\populator\AmountPopulator;
use pocketmine\block\VanillaBlocks;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;

class CactusPopulator extends AmountPopulator {

	public function populateObject(ChunkManager $world, int $chunkX, int $chunkZ, Random $random): void {
		$this->getSpawnPosition($world->getChunk($chunkX, $chunkZ), $random, $x, $y, $z);

		if ($y !== -1 && $this->canCactusStay($world, new Vector3($x, $y, $z))) {
			for ($aY = 0; $aY < $random->nextRange(0, 3); $aY++) {
				$world->setBlockAt($x, $y + $aY, $z, VanillaBlocks::CACTUS());
			}
		}
	}

	private function canCactusStay(ChunkManager $world, Vector3 $pos): bool {
		/** @phpstan-ignore-next-line */
		$block = $world->getBlockAt($pos->getX(), $pos->getY(), $pos->getZ());
		if (
			/** @phpstan-ignore-next-line */
			!$world->getBlockAt($pos->getX() + 1, $pos->getY(), $pos->getZ())->isSameType(VanillaBlocks::AIR()) ||
			/** @phpstan-ignore-next-line */
			!$world->getBlockAt($pos->getX() - 1, $pos->getY(), $pos->getZ())->isSameType(VanillaBlocks::AIR()) ||
			/** @phpstan-ignore-next-line */
			!$world->getBlockAt($pos->getX(), $pos->getY(), $pos->getZ() + 1)->isSameType(VanillaBlocks::AIR()) ||
			/** @phpstan-ignore-next-line */
			!$world->getBlockAt($pos->getX(), $pos->getY(), $pos->getZ() - 1)->isSameType(VanillaBlocks::AIR())
		) {
			return false;
		}

		/** @phpstan-ignore-next-line */
		return ($block->isSameType($block)) && $world->getBlockAt($pos->getX(), $pos->getY() - 1, $pos->getZ())->isSameType(VanillaBlocks::SAND());
	}
}