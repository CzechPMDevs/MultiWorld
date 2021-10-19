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

namespace czechpmdevs\multiworld\generator\nether\populator;

use czechpmdevs\multiworld\generator\normal\populator\Populator;
use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\object\OreType;
use pocketmine\math\VectorMath;
use pocketmine\utils\Random;
use function sin;

class Ore extends Populator {

	/** @var OreType[] */
	protected array $oreTypes = [];

	public function populate(ChunkManager $level, int $chunkX, int $chunkZ, Random $random) {
		foreach($this->oreTypes as $type) {
			$ore = new \pocketmine\level\generator\object\Ore($random, $type);
			for($i = 0; $i < $ore->type->clusterCount; ++$i) {
				$x = $random->nextRange($chunkX << 4, ($chunkX << 4) + 15);
				$y = $random->nextRange($ore->type->minHeight, $ore->type->maxHeight);
				$z = $random->nextRange($chunkZ << 4, ($chunkZ << 4) + 15);
				if($level->getBlockIdAt($x, $y, $z) == Block::NETHERRACK) {
					$this->placeObject($type, $random, $level, $x, $y, $z);
				}
			}
		}
	}

	public function placeObject(OreType $type, Random $random, ChunkManager $level, int $x, int $y, int $z): void {
		$clusterSize = $type->clusterSize;
		$angle = $random->nextFloat() * M_PI;
		$offset = VectorMath::getDirection2D($angle)->multiply($clusterSize / 8);
		$x1 = $x + 8 + $offset->x;
		$x2 = $x + 8 - $offset->x;
		$z1 = $z + 8 + $offset->y;
		$z2 = $z + 8 - $offset->y;
		$y1 = $y + $random->nextBoundedInt(3) + 2;
		$y2 = $y + $random->nextBoundedInt(3) + 2;
		for($count = 0; $count <= $clusterSize; ++$count) {
			$seedX = $x1 + ($x2 - $x1) * $count / $clusterSize;
			$seedY = $y1 + ($y2 - $y1) * $count / $clusterSize;
			$seedZ = $z1 + ($z2 - $z1) * $count / $clusterSize;
			$size = ((sin($count * (M_PI / $clusterSize)) + 1) * $random->nextFloat() * $clusterSize / 16 + 1) / 2;

			$startX = (int)($seedX - $size);
			$startY = (int)($seedY - $size);
			$startZ = (int)($seedZ - $size);
			$endX = (int)($seedX + $size);
			$endY = (int)($seedY + $size);
			$endZ = (int)($seedZ + $size);

			for($x = $startX; $x <= $endX; ++$x) {
				$sizeX = ($x + 0.5 - $seedX) / $size;
				$sizeX *= $sizeX;

				if($sizeX < 1) {
					for($y = $startY; $y <= $endY; ++$y) {
						$sizeY = ($y + 0.5 - $seedY) / $size;
						$sizeY *= $sizeY;

						if($y > 0 and ($sizeX + $sizeY) < 1) {
							for($z = $startZ; $z <= $endZ; ++$z) {
								$sizeZ = ($z + 0.5 - $seedZ) / $size;
								$sizeZ *= $sizeZ;

								if(($sizeX + $sizeY + $sizeZ) < 1 and $level->getBlockIdAt($x, $y, $z) === Block::NETHERRACK) {
									$level->setBlockIdAt($x, $y, $z, $type->material->getId());
									if($type->material->getDamage() !== 0) {
										$level->setBlockDataAt($x, $y, $z, $type->material->getDamage());
									}
								}
							}
						}
					}
				}
			}
		}
	}

	/**
	 * @param OreType[] $types
	 */
	public function setOreTypes(array $types): void {
		$this->oreTypes = $types;
	}
}