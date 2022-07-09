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

namespace czechpmdevs\multiworld\generator\normal\populator\impl\carve;

use pocketmine\block\VanillaBlocks;
use pocketmine\utils\Random;
use pocketmine\world\format\Chunk;
use pocketmine\world\World;
use function floor;
use function max;
use function min;

abstract class Carve {

	protected Random $random;

	final public function __construct(Random $random) {
		$this->random = $random;
	}

	/**
	 * @param Chunk $populatedChunk Is chunk, which will be updated (it should be same chunk / population)
	 *
	 * @param int $chunkX X coordinate from 'original' chunk (from chunk, where the carve starts)
	 * @param int $chunkZ Z coordinate from 'original' chunk (from chunk, where the carve starts)
	 */
	abstract public function carve(Chunk $populatedChunk, int $populatedChunkX, int $populatedChunkZ, int $chunkX, int $chunkZ): void;

	protected function carveSphere(Chunk $chunk, int $populatedChunkX, int $populatedChunkZ, float $centerX, float $centerY, float $centerZ, float $horizontalSize, float $verticalSize): void {
		$realChunkX = $populatedChunkX * 16;
		$realChunkZ = $populatedChunkZ * 16;

		if(
			($centerX < $realChunkX - 8.0 - $horizontalSize * 2.0) ||
			($centerZ < $realChunkZ - 8.0 - $horizontalSize * 2.0) ||
			($centerX > $realChunkX + 24.0 + $horizontalSize * 2.0) ||
			($centerZ > $realChunkZ + 24.0 + $horizontalSize * 2.0)
		) return;

		$minX = (int)max(0, (int)floor($centerX - $horizontalSize) - $realChunkX - 1);
		$maxX = (int)min(16, (int)floor($centerX + $horizontalSize) - $realChunkX + 1);
		$minY = (int)max(1, (int)floor($centerY - $verticalSize) - 1);
		$maxY = (int)min(World::Y_MAX - 8, (int)floor($centerY + $verticalSize) + 1);
		$minZ = (int)max(0, (int)floor($centerZ - $horizontalSize) - $realChunkZ - 1);
		$maxZ = (int)min(16, (int)floor($centerZ + $horizontalSize) - $realChunkZ + 1);

		if($this->collidesWithLiquids($chunk, $minX, $maxX, $minY, $maxY, $minZ, $maxZ)) {
			return;
		}

		$air = VanillaBlocks::AIR()->getStateId();
		$lava = VanillaBlocks::LAVA()->getStateId();
		$water = VanillaBlocks::WATER()->getStateId();

		$dirt = VanillaBlocks::DIRT()->getStateId();
		$grass = VanillaBlocks::GRASS()->getStateId();

		for($x = $minX; $x < $maxX; ++$x) {
			$modX = ($x + $realChunkX + 0.5 - $centerX) / $horizontalSize;
			for($z = $minZ; $z < $maxZ; ++$z) {
				$modZ = ($z + $realChunkZ + 0.5 - $centerZ) / $horizontalSize;

				if(($modXZ = ($modX ** 2) + ($modZ ** 2)) < 1.0) {
					for($y = $maxY; $y > $minY; --$y) {
						$modY = ($y - 0.5 - $centerY) / $verticalSize;

						if($this->continue($modXZ, $modY, $y)) {
							if($chunk->getFullBlock($x, $y, $z) === $water || $chunk->getFullBlock($x, $y + 1, $z) >> 4 === $water) {
								continue;
							}

							if($y < 11) {
								$chunk->setFullBlock($x, $y, $z, $lava);
								continue;
							}

							if(
								$chunk->getFullBlock($x, $y - 1, $z) === $dirt &&
								$chunk->getFullBlock($x, $y + 1, $z) === $air &&
								$y > 62
							) {
								$chunk->setFullBlock($x, $y - 1, $z, $grass);
							}

							$chunk->setFullBlock($x, $y, $z, $air);
						}
					}
				}
			}
		}
	}

	private function collidesWithLiquids(Chunk $chunk, int $minX, int $maxX, int $minY, int $maxY, int $minZ, int $maxZ): bool {
		$water = VanillaBlocks::WATER()->getStateId();
		$lava = VanillaBlocks::LAVA()->getStateId();
		for($x = $minX; $x < $maxX; ++$x) {
			for($z = $minZ; $z < $maxZ; ++$z) {
				for($y = $minY - 1; $y < $maxY + 1; ++$y) {
					$id = $chunk->getFullBlock($x, $y, $z);
					if(
						$id === $lava ||
						$id === $water
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