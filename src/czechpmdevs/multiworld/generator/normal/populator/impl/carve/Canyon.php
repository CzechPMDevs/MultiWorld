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

use czechpmdevs\multiworld\util\MathHelper;
use pocketmine\utils\Random;
use pocketmine\world\format\Chunk;
use pocketmine\world\World;
use const M_PI;

class Canyon extends Carve {
	private const CANYON_RANGE = 4;

	/** @var float[] */
	private array $sizeMap = [];

	public function carve(Chunk $populatedChunk, int $populatedChunkX, int $populatedChunkZ, int $chunkX, int $chunkZ): void {
		$x = (float)($chunkX * 16 + $this->random->nextBoundedInt(16));
		$y = (float)($this->random->nextBoundedInt($this->random->nextBoundedInt(40) + 8) + 20);
		$z = (float)($chunkZ * 16 + $this->random->nextBoundedInt(16));

		$horizontalAngle = $this->random->nextFloat() * M_PI * 2;
		$verticalAngle = ($this->random->nextFloat() - 0.5) * 0.25;

		$horizontalScale = ($this->random->nextFloat() * 2.0 + $this->random->nextFloat()) * 2.0;

		$nodeCountBound = (Canyon::CANYON_RANGE * 2 - 1) * 16;
		$nodeCount = $nodeCountBound - $this->random->nextBoundedInt($nodeCountBound);

		$this->generateCanyon($populatedChunk, $populatedChunkX, $populatedChunkZ, $this->random->nextInt(), $chunkX, $chunkZ, $x, $y, $z, $horizontalScale, $horizontalAngle, $verticalAngle, $nodeCount);
	}

	private function generateCanyon(Chunk $populatedChunk, int $populatedChunkX, int $populatedChunkZ, int $seed, int $chunkX, int $chunkZ, float $x, float $y, float $z, float $horizontalScale, float $horizontalAngle, float $verticalAngle, int $nodeCount): void {
		$localRandom = new Random($seed);

		$baseSize = 1.0;
		for($i = World::Y_MIN; $i < World::Y_MAX; ++$i) {
			if($i === World::Y_MIN || $this->random->nextBoundedInt(3) == 0) {
				$baseSize = 1.0 + $localRandom->nextFloat() * $localRandom->nextFloat();
			}

			$this->sizeMap[$i] = $baseSize ** 2;
		}

		$horizontalOffset = 0.0;
		$verticalOffset = 0.0;

		for($node = 0; $node < $nodeCount; ++$node) {
			$horizontalSize = 1.5 + (MathHelper::getInstance()->sin((float)$node * M_PI / (float)$nodeCount) * $horizontalScale);
			$verticalSize = $horizontalSize * 3.0;

			$horizontalSize *= $localRandom->nextFloat() * 0.25 + 0.75;
			$verticalSize *= $localRandom->nextFloat() * 0.25 + 0.75;

			$cos = MathHelper::getInstance()->cos($verticalAngle);

			$x += MathHelper::getInstance()->cos($horizontalAngle) * $cos;
			$y += MathHelper::getInstance()->sin($verticalAngle);
			$z += MathHelper::getInstance()->sin($horizontalAngle) * $cos;

			$horizontalAngle += $horizontalOffset * 0.05;
			$verticalAngle = $verticalAngle * 0.7 + $verticalOffset * 0.05;

			$horizontalOffset *= 0.5;
			$verticalOffset *= 0.8;

			$horizontalOffset += ($localRandom->nextFloat() - $localRandom->nextFloat()) * $localRandom->nextFloat() * 4.0;
			$verticalOffset += ($localRandom->nextFloat() - $localRandom->nextFloat()) * $localRandom->nextFloat() * 2.0;

			if($localRandom->nextBoundedInt(4) !== 0) {
				if(!$this->canReach($chunkX, $chunkZ, $x, $z, $node, $nodeCount, $horizontalScale)) {
					return;
				}

				$this->carveSphere($populatedChunk, $populatedChunkX, $populatedChunkZ, $x, $y, $z, $horizontalSize, $verticalSize);
			}
		}
	}

	protected function continue(float $modXZ, float $modY, int $y): bool {
		return ($modXZ * $this->sizeMap[$y - 1]) + ($modY ** 2) * 1.66 < 1.0;
	}

	public function canCarve(Random $random, int $chunkX, int $chunkZ): bool {
		return $random->nextFloat() <= 0.02;
	}
}