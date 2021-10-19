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

use czechpmdevs\multiworld\generator\normal\populator\impl\carve\Canyon;
use czechpmdevs\multiworld\generator\normal\populator\impl\carve\Carve;
use czechpmdevs\multiworld\generator\normal\populator\impl\carve\Cave;
use czechpmdevs\multiworld\generator\normal\populator\Populator;
use pocketmine\level\ChunkManager;
use pocketmine\level\format\Chunk;
use pocketmine\utils\Random;

class CarvePopulator extends Populator {

	/** @const int */
	public const CHECK_AREA_SIZE = 6; // originally 8

	/** @var Random */
	private Random $random;
	/** @var Carve[] */
	private array $carves = [];

	public function __construct() {
		$this->random = new Random(0);

		$this->carves[] = new Canyon($this->random);
		$this->carves[] = new Cave($this->random);
	}

	public function populate(ChunkManager $level, int $chunkX, int $chunkZ, Random $random) {
		$localRandom = new Random($level->getSeed());
		$xSeed = $localRandom->nextInt();
		$zSeed = $localRandom->nextInt();

		/** @var Chunk $chunk */
		$chunk = $level->getChunk($chunkX, $chunkZ);

		$minX = $chunkX - CarvePopulator::CHECK_AREA_SIZE;
		$maxX = $chunkX + CarvePopulator::CHECK_AREA_SIZE;
		$minZ = $chunkX - CarvePopulator::CHECK_AREA_SIZE;
		$maxZ = $chunkZ + CarvePopulator::CHECK_AREA_SIZE;

		for($x = $minX; $x <= $maxX; ++$x) {
			$randomX = $xSeed * $x;
			for($z = $minZ; $z <= $maxZ; ++$z) {
				$randomZ = $zSeed * $z;

				$seed = $randomX ^ $randomZ ^ $level->getSeed();
				foreach($this->carves as $carve) {
					$this->random->setSeed($seed);
					if($carve->canCarve($this->random, $chunkX, $chunkZ)) {
						$carve->carve($chunk, $x, $z);
					}
				}
			}
		}
	}
}