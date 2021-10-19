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

namespace czechpmdevs\multiworld\util;

use pocketmine\block\BlockIds;
use pocketmine\level\ChunkManager;
use pocketmine\utils\Random;

trait PositionCalc {

	/**
	 * Returns random spawn position based on Random class for
	 * specified Chunk
	 */
	public function getRandomSpawnPosition(ChunkManager $level, int $chunkX, int $chunkZ, Random $random, ?int &$x, ?int &$y, ?int &$z): void {
		$x = ($chunkX << 4) + $random->nextBoundedInt(16);
		$z = ($chunkZ << 4) + $random->nextBoundedInt(16);
		$y = $this->getHighestWorkableBlock($level, $x, $z);
	}

	/**
	 * Returns Y coordinate of highest block at X:Z coordinates in the level
	 */
	public function getHighestWorkableBlock(ChunkManager $level, int $x, int $z): int {
		for($y = 127; $y >= 0; --$y) {
			$b = $level->getBlockIdAt($x, $y, $z);
			if($b !== BlockIds::AIR and $b !== BlockIds::LEAVES and $b !== BlockIds::LEAVES2 and $b !== BlockIds::SNOW_LAYER) {
				break;
			}
		}

		return $y === 0 ? -1 : ++$y;
	}
}