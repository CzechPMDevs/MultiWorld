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

namespace czechpmdevs\multiworld\generator\normal\object;

use pocketmine\block\BlockIds;
use pocketmine\level\ChunkManager;
use pocketmine\utils\Random;
use function abs;

class HugeMushroom extends Tree {

	public function __construct() {
		$this->overridable[BlockIds::MYCELIUM] = true;
	}

	public function placeObject(ChunkManager $level, int $x, int $y, int $z, Random $random) {
		$block = $random->nextBoolean() ? BlockIds::BROWN_MUSHROOM_BLOCK : BlockIds::RED_MUSHROOM_BLOCK;
		$maxY = 3 + $random->nextBoundedInt(1);

		for($yy = 0; $yy <= $maxY; $yy++) {
			$level->setBlockIdAt($x, $y + $yy, $z, $block);
			$level->setBlockDataAt($x, $y + $yy, $z, 10);
		}

		switch($block) {
			case 100:
				$data = 0;
				for($i = -1; $i <= 1; $i++) {
					for($j = -1; $j <= 1; $j++) {
						$data++;
						$level->setBlockIdAt($x + $j, $y + $maxY + 1, $z + $i, $block);
						$level->setBlockDataAt($x + $j, $y + $maxY + 1, $z + $i, $data);

						for($yyy = $maxY; $yyy >= 1; $yyy--) {
							if(abs($i) == 1 && abs($j) == 1) {
								$i1 = $i < 0 ? $i - 1 : $i + 1;
								$j1 = $j < 0 ? $j - 1 : $j + 1;

								$level->setBlockIdAt($x + $j1, $yyy + $y, $z + $i, $block);
								$level->setBlockDataAt($x + $j1, $yyy + $y, $z + $i, $data);

								$level->setBlockIdAt($x + $j, $yyy + $y, $z + $i1, $block);
								$level->setBlockDataAt($x + $j, $yyy + $y, $z + $i1, $data);
							} else {
								$i1 = $i < 0 ? $i - 1 : ($i > 0 ? $i + 1 : $i);
								$j1 = $j < 0 ? $j - 1 : ($j > 0 ? $j + 1 : $j);

								if($j1 == $i1 && $j1 == 0) {
									continue;
								}

								$level->setBlockIdAt($x + $j1, $yyy + $y, $z + $i1, $block);
								$level->setBlockDataAt($x + $j1, $yyy + $y, $z + $i1, $data);
							}
						}
					}
				}
				break;

			case 99:
				for($i = -2; $i <= 2; $i++) {
					for($j = -2; $j <= 2; $j++) {
						$level->setBlockIdAt($x + $j, $maxY + 1 + $y, $z + $i, $block);
						$level->setBlockDataAt($x + $j, $maxY + 1 + $y, $z + $i, 5);
					}
				}

				$data = 0;
				for($i = -1; $i <= 1; $i++) {
					for($j = -1; $j <= 1; $j++) {
						$data++;
						if($i == 0 && $i == 1) continue;

						$i1 = $i * 3; // z
						$j1 = $j * 3; // x
						if(abs($i1) == 3 && abs($j1) == 3) {
							$i11 = $i1 < 0 ? $i1 + 1 : $i1 - 1;
							$j11 = $j1 < 0 ? $j1 + 1 : $j1 - 1;

							$level->setBlockIdAt($x + $j1, $maxY + 1 + $y, $z + $i11, $block);
							$level->setBlockDataAt($x + $j1, $maxY + 1 + $y, $z + $i11, $data);

							$level->setBlockIdAt($x + $j11, $maxY + 1 + $y, $z + $i1, $block);
							$level->setBlockDataAt($x + $j11, $maxY + 1 + $y, $z + $i1, $data);
						} else {
							if($i1 === 0) {
								$level->setBlockIdAt($x + $j1, $maxY + 1 + $y, $z + 1, $block);
								$level->setBlockDataAt($x + $j1, $maxY + 1 + $y, $z + 1, $data);

								$level->setBlockIdAt($x + $j1, $maxY + 1 + $y, $z, $block);
								$level->setBlockDataAt($x + $j1, $maxY + 1 + $y, $z, $data);

								$level->setBlockIdAt($x + $j1, $maxY + 1 + $y, $z - 1, $block);
								$level->setBlockDataAt($x + $j1, $maxY + 1 + $y, $z - 1, $data);
							} else {
								$level->setBlockIdAt($x + 1, $maxY + 1 + $y, $z + $i1, $block);
								$level->setBlockDataAt($x + 1, $maxY + 1 + $y, $z + $i1, $data);

								$level->setBlockIdAt($x, $maxY + 1 + $y, $z + $i1, $block);
								$level->setBlockDataAt($x, $maxY + 1 + $y, $z + $i1, $data);

								$level->setBlockIdAt($x - 1, $maxY + 1 + $y, $z + $i1, $block);
								$level->setBlockDataAt($x - 1, $maxY + 1 + $y, $z + $i1, $data);
							}
						}
					}
				}
				break;
		}
	}
}