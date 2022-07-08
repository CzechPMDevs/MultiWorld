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

namespace czechpmdevs\multiworld\generator\normal\object;

use pocketmine\block\BlockTypeIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use function abs;
use function cos;
use function floor;
use function hypot;
use function max;
use function min;
use function pow;
use function sin;
use function sqrt;

class BigOakTree extends Tree {

	const LEAF_DENSITY = 1.0;

	private int $maxLeafDistance = 5;

	private int $trunkHeight = 0;

	private int $height;

	public function __construct(Random $random) {
		$this->height = $random->nextBoundedInt(12) + 5;
	}

	public function canPlaceObject(ChunkManager $world, int $x, int $y, int $z, Random $random): bool {
		$from = new Vector3($x, $y, $z);

		$to = new Vector3($x, $y + $this->height - 1, $z);
		$blocks = $this->countAvailableBlocks($from, $to);

		if($blocks == -1) {
			return true;
		} elseif($blocks > 5) {
			$this->height = $blocks;
			return true;
		}

		return false;
	}

	private function countAvailableBlocks(Vector3 $from, Vector3 $to): int {
		$n = 0;
		$target = $to->subtractVector($from);
		$maxDistance = max(abs(floor($target->getY())), max(abs(floor($target->getX())), abs(floor($target->getZ()))));

		if($maxDistance > 0) {
			$dx = (float)$target->getX() / $maxDistance;
			$dy = (float)$target->getY() / $maxDistance;
			$dz = (float)$target->getZ() / $maxDistance;
			for($i = 0; $i <= $maxDistance; $i++, $n++) {
				$target = $from->add(0.5 + $i * $dx, 0.5 + $i * $dy, 0.5 + $i * $dz);
				if($target->getFloorY() < 0 || $target->getFloorY() > 255) {
					return $n;
				}
			}
		}
		return -1;
	}

	public function placeObject(ChunkManager $world, int $x, int $y, int $z, Random $random): void {
		$trunkHeight = (int)($this->height * 0.618);
		if($trunkHeight >= $this->height) {
			$trunkHeight = $this->height - 1;
		}

		$leafNodes = $this->generateLeafNodes($x, $y, $z, $random);

		// generate the leaves
		/** @var Vector3 $node */
		foreach($leafNodes as [$node, $branchY]) {
			for($yy = 0; $yy < $this->maxLeafDistance; $yy++) {
				$size = $yy > 0 && $yy < $this->maxLeafDistance - 1.0 ? 3.0 : 2.0;
				$nodeDistance = (int)(0.618 + $size);
				for($xx = -$nodeDistance; $xx <= $nodeDistance; $xx++) {
					for($zz = -$nodeDistance; $zz <= $nodeDistance; $zz++) {
						$sizeX = abs($xx) + 0.5;
						$sizeZ = abs($zz) + 0.5;
						if($sizeX * $sizeX + $sizeZ * $sizeZ <= $size * $size && $node->getY() + $yy - 3 >= $y) {
							/** @phpstan-ignore-next-line */
							if($world->getBlockAt($node->getX() + $xx, $node->getY() + $yy, $node->getZ() + $zz)->getTypeId() == BlockTypeIds::AIR) {
								/** @phpstan-ignore-next-line */
								$world->setBlockAt($node->getX() + $xx, $node->getY() + $yy, $node->getZ() + $zz, VanillaBlocks::OAK_LEAVES());
							}
						}
					}
				}
			}
		}

		// generate the trunk
		for($yy = 0; $yy < $trunkHeight; $yy++) {
			$world->setBlockAt($x, $y + $yy, $z, VanillaBlocks::OAK_WOOD());
		}

		// generate the branches

		/** @var Vector3 $leafNode */
		foreach($leafNodes as [$leafNode, $branchY]) {
			if((double)$branchY - $y >= $this->height * 0.2) {
				$base = new Vector3($x, $branchY, $z);
				$branch = $leafNode->subtractVector($base);

				$maxDistance = max(abs(floor($branch->getY())), max(abs(floor($branch->getX())), abs(floor($branch->getZ()))));

				$dx = (float)$branch->getX() / $maxDistance;
				$dy = (float)$branch->getY() / $maxDistance;
				$dz = (float)$branch->getZ() / $maxDistance;

				for($i = 0; $i <= $maxDistance; $i++) {
					$branch = $base->add(0.5 + $i * $dx, 0.5 + $i * $dy, 0.5 + $i * $dz);
					$z = abs($branch->getZ() - $base->getZ());

					/** @phpstan-ignore-next-line */
					$world->setBlockAt($branch->getX(), $branch->getY(), $branch->getZ(), VanillaBlocks::OAK_WOOD());
				}
			}
		}
	}

	/**
	 * @phpstan-return array<array{0: \pocketmine\math\Vector3, 1: int}>
	 */
	private function generateLeafNodes(int $blockX, int $blockY, int $blockZ, Random $random): array {
		$leafNodes = [];
		$y = $blockY + $this->height - $this->maxLeafDistance;
		$trunkTopY = $blockY + $this->trunkHeight;
		$leafNodes[] = [new Vector3($blockX, $y, $blockZ), $trunkTopY];

		$nodeCount = (int)(1.382 + pow(self::LEAF_DENSITY * (double)$this->height / 13.0, 2.0));
		$nodeCount = $nodeCount < 1 ? 1 : $nodeCount;

		for($l = --$y - $blockY; $l >= 0; $l--, $y--) {
			$h = $this->height / 2.0;
			$v = $h - $l;
			$f = $l < (float)$this->height * 0.3 ? -1.0 : ($v == $h ? $h * 0.5 : ($h <= abs($v) ? 0.0 : (sqrt($h * $h - $v * $v) * 0.5)));

			if($f >= 0.0) {
				for($i = 0; $i < $nodeCount; $i++) {
					$d1 = $f * ($random->nextFloat() + 0.328);
					$d2 = $random->nextFloat() * M_PI * 2.0;
					$x = (int)($d1 * sin($d2) + $blockX + 0.5);
					$z = (int)($d1 * cos($d2) + $blockZ + 0.5);
					if($this->countAvailableBlocks(new Vector3($x, $y, $z), new Vector3($x, $y + $this->maxLeafDistance, $z)) == -1) {
						$offX = $blockX - $x;
						$offZ = $blockZ - $z;
						$distance = 0.381 * hypot($offX, $offZ);
						$branchBaseY = min($trunkTopY, (int)($y - $distance));

						if($this->countAvailableBlocks(new Vector3($x, $branchBaseY, $z), new Vector3($x, $y, $z)) == -1) {
							$leafNodes[] = [new Vector3($x, $y, $z), $branchBaseY];
						}
					}
				}
			}
		}

		return $leafNodes;
	}
}

