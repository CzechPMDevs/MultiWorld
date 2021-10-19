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

namespace czechpmdevs\multiworld\generator\ender\populator;

use pocketmine\block\BlockIds;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\populator\Populator;
use pocketmine\utils\Random;
use function cos;
use function deg2rad;
use function intval;
use function mt_rand;
use function sin;

class EnderPilar extends Populator {

	/** @var ChunkManager */
	private ChunkManager $level;

	/** @var int */
	private int $randomAmount;
	/** @var int */
	private int $baseAmount;

	public function setRandomAmount(int $amount): void {
		$this->randomAmount = $amount;
	}

	public function setBaseAmount(int $amount): void {
		$this->baseAmount = $amount;
	}

	public function populate(ChunkManager $level, int $chunkX, int $chunkZ, Random $random) {
		if(mt_rand(0, 100) < 10) {
			$this->level = $level;
			$amount = $random->nextRange(0, $this->randomAmount + 1) + $this->baseAmount;
			for($i = 0; $i < $amount; ++$i) {
				$x = $random->nextRange($chunkX * 16, $chunkX * 16 + 15);
				$z = $random->nextRange($chunkZ * 16, $chunkZ * 16 + 15);
				$y = $this->getHighestWorkableBlock($x, $z);
				if($this->level->getBlockIdAt($x, $y, $z) == BlockIds::END_STONE) {
					$height = mt_rand(28, 50);
					for($ny = $y; $ny < $y + $height; $ny++) {
						for($r = 0.5; $r < 5; $r += 0.5) {
							$nd = 360 / (2 * pi() * $r);
							for($d = 0; $d < 360; $d += $nd) {
								$level->setBlockIdAt(intval($x + (cos(deg2rad($d)) * $r)), $ny, intval($z + (sin(deg2rad($d)) * $r)), BlockIds::OBSIDIAN);
							}
						}
					}
				}
			}
		}
	}

	private function getHighestWorkableBlock(int $x, int $z): int {
		for($y = 127; $y >= 0; --$y) {
			$b = $this->level->getBlockIdAt($x, $y, $z);
			if($b == BlockIds::END_STONE) {
				break;
			}
		}
		return $y === 0 ? -1 : $y;
	}
}