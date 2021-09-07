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

namespace czechpmdevs\multiworld\generator\normal\populator;

use pocketmine\block\Block;
use pocketmine\utils\Random;
use pocketmine\world\format\Chunk;
use function array_map;
use function in_array;

abstract class Populator implements \pocketmine\world\generator\populator\Populator {

	protected function getSpawnPosition(?Chunk $chunk, Random $random, ?int &$x = null, ?int &$y = null, ?int &$z = null): bool {
		if($chunk === null) {
			return false;
		}

		$i = 0;
		do {
			$x = $random->nextBoundedInt(16);
			$z = $random->nextBoundedInt(16);

			for($y = 0; $y < 128; ++$y) {
				if($chunk->getFullBlock($x, $y, $z) >> 4 == 0 && $chunk->getFullBlock($x, $y + 1, $z) >> 4 == 0) {
					return true;
				}
			}
		} while($i++ < 4);

		return false;
	}

	/**
	 * @param Block[] $requiredUnderground
	 */
	protected function getSpawnPositionOn(?Chunk $chunk, Random $random, array $requiredUnderground = [], ?int &$x = null, ?int &$y = null, ?int &$z = null): bool {
		if($chunk === null) {
			return false;
		}

		$requiredUnderground = array_map(fn(Block $block) => $block->getFullId(), $requiredUnderground);

		$i = 0;
		do {
			$x = $random->nextBoundedInt(16);
			$z = $random->nextBoundedInt(16);

			for($y = 0; $y < 128; ++$y) {
				if($chunk->getFullBlock($x, $y, $z) >> 4 == 0 && in_array($chunk->getFullBlock($x, $y - 1, $z), $requiredUnderground, true)) {
					return true;
				}
			}

		} while($i++ < 5);

		return false;
	}
}