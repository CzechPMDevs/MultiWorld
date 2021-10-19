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

namespace czechpmdevs\multiworld\generator\normal\populator\impl\carve;

use pocketmine\block\BlockIds;
use pocketmine\level\format\Chunk;
use pocketmine\level\Level;
use pocketmine\utils\Random;
use function floor;
use function max;
use function min;

abstract class Carve {

	/** @var Random */
	protected Random $random;

	final public function __construct(Random $random) {
		$this->random = $random;
	}

	/**
	 * @param Chunk $chunk Is chunk, which will be updated (it should be same chunk / population)
	 *
	 * @param int $chunkX X coordinate from 'original' chunk (from chunk, where the carve starts)
	 * @param int $chunkZ Z coordinate from 'original' chunk (from chunk, where the carve starts)
	 */
	abstract public function carve(Chunk $chunk, int $chunkX, int $chunkZ): void;

	protected function carveSphere(Chunk $chunk, float $centerX, float $centerY, float $centerZ, float $horizontalSize, float $verticalSize): void {
		$realChunkX = $chunk->getX() * 16;
		$realChunkZ = $chunk->getZ() * 16;

		if(
			($centerX < $realChunkX - 8.0 - $horizontalSize * 2.0) ||
			($centerZ < $realChunkZ - 8.0 - $horizontalSize * 2.0) ||
			($centerX > $realChunkX + 24.0 + $horizontalSize * 2.0) ||
			($centerZ > $realChunkZ + 24.0 + $horizontalSize * 2.0)
		) return;

		$minX = (int)max(0, (int)floor($centerX - $horizontalSize) - $realChunkX - 1);
		$maxX = (int)min(16, (int)floor($centerX + $horizontalSize) - $realChunkX + 1);
		$minY = (int)max(1, (int)floor($centerY - $verticalSize) - 1);
		$maxY = (int)min(Level::Y_MAX - 8, (int)floor($centerY + $verticalSize) + 1);
		$minZ = (int)max(0, (int)floor($centerZ - $horizontalSize) - $realChunkZ - 1);
		$maxZ = (int)min(16, (int)floor($centerZ + $horizontalSize) - $realChunkZ + 1);

		if($this->collidesWithLiquids($chunk, $minX, $maxX, $minY, $maxY, $minZ, $maxZ)) {
			return;
		}

		for($x = $minX; $x < $maxX; ++$x) {
			$modX = ($x + $realChunkX + 0.5 - $centerX) / $horizontalSize;
			for($z = $minZ; $z < $maxZ; ++$z) {
				$modZ = ($z + $realChunkZ + 0.5 - $centerZ) / $horizontalSize;

				if(($modXZ = ($modX ** 2) + ($modZ ** 2)) < 1.0) {
					for($y = $maxY; $y > $minY; --$y) {
						$modY = ($y - 0.5 - $centerY) / $verticalSize;

						if($this->continue($modXZ, $modY, $y)) {
							if($chunk->getBlockId($x, $y, $z) == BlockIds::WATER || $chunk->getBlockId($x, $y + 1, $z) == BlockIds::WATER) {
								continue;
							}

							if($y < 11) {
								$chunk->setBlockId($x, $y, $z, BlockIds::STILL_LAVA);
								continue;
							}

							if(
								$chunk->getBlockId($x, $y - 1, $z) == BlockIds::DIRT &&
								$chunk->getBlockId($x, $y + 1, $z) == BlockIds::AIR &&
								$y > 62
							) {
								$chunk->setBlockId($x, $y - 1, $z, BlockIds::GRASS);
							}

							$chunk->setBlockId($x, $y, $z, BlockIds::AIR);
						}
					}
				}
			}
		}
	}

	private function collidesWithLiquids(Chunk $chunk, int $minX, int $maxX, int $minY, int $maxY, int $minZ, int $maxZ): bool {
		for($x = $minX; $x < $maxX; ++$x) {
			for($z = $minZ; $z < $maxZ; ++$z) {
				for($y = $minY - 1; $y < $maxY + 1; ++$y) {
					$id = $chunk->getBlockId($x, $y, $z);
					if(
						$id == BlockIds::FLOWING_WATER ||
						$id == BlockIds::STILL_LAVA ||
						$id == BlockIds::FLOWING_LAVA ||
						$id == BlockIds::STILL_LAVA
					) {
						return true;
					}

					if($y != $maxY + 1 && $x != $minX && $x != $maxX - 1 && $z != $minZ && $z != $maxZ - 1) {
						$y = $maxY;
					}
				}
			}
		}
		return false;
	}

	protected function canReach(int $chunkX, int $chunkZ, float $x, float $z, int $angle, int $maxAngle, float $radius): bool {
		return (($x - ($chunkX * 16) - 8) ** 2) + (($z - ($chunkZ * 16) - 8) ** 2) - (($maxAngle - $angle) ** 2) <= ($radius + 18) ** 2;
	}

	abstract protected function continue(float $modXZ, float $modY, int $y): bool;

	/** @noinspection PhpUnusedParameterInspection */
	public function canCarve(Random $random, int $chunkX, int $chunkZ): bool {
		return true;
	}
}