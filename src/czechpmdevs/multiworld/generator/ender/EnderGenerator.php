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

namespace czechpmdevs\multiworld\generator\ender;

use czechpmdevs\multiworld\generator\ender\populator\EnderPilar;
use czechpmdevs\multiworld\world\data\BiomeIds;
use pocketmine\block\VanillaBlocks;
use pocketmine\world\ChunkManager;
use pocketmine\world\format\Chunk;
use pocketmine\world\generator\Generator;
use pocketmine\world\generator\noise\Simplex;
use pocketmine\world\generator\populator\Populator;

class EnderGenerator extends Generator {
	public const MIN_BASE_ISLAND_HEIGHT = 54;
	public const MAX_BASE_ISLAND_HEIGHT = 55;
	public const NOISE_SIZE = 12;

	public const CENTER_X = 255;
	public const CENTER_Z = 255;
	public const ISLAND_RADIUS = 100;

	private Simplex $noiseBase;

	/** @var Populator[] */
	private array $populators = [];

	public function __construct(int $seed, string $preset) {
		parent::__construct($seed, $preset);

		$this->noiseBase = new Simplex($this->random, 4, 1 / 16, 1 / 64);
		$this->populators[] = new EnderPilar();
	}

	public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void {
		$this->random->setSeed(0xdeadbeef ^ ($chunkX << 8) ^ $chunkZ ^ $this->seed);

		/** @phpstan-var Chunk $chunk */
		$chunk = $world->getChunk($chunkX, $chunkZ);
		$noise = $this->noiseBase->getFastNoise2D(16, 16, 2, $chunkX * 16, 0, $chunkZ * 16);

		$endStone = VanillaBlocks::END_STONE()->getStateId();

		$baseX = $chunkX * Chunk::EDGE_LENGTH;
		$baseZ = $chunkZ * Chunk::EDGE_LENGTH;
		for($x = 0; $x < 16; ++$x) {
			$absoluteX = $baseX + $x;
			for($z = 0; $z < 16; ++$z) {
				$absoluteZ = $baseZ + $z;

				$chunk->setBiomeId($x, $z, BiomeIds::THE_END);

				if(($absoluteX - self::CENTER_X) ** 2 + ($absoluteZ - self::CENTER_Z) ** 2 > self::ISLAND_RADIUS ** 2) {
					continue;
				}

				// @phpstan-ignore-next-line
				$noiseValue = (int)abs($noise[$x][$z] * self::NOISE_SIZE); // wtf
				for($y = 0; $y < $noiseValue; ++$y) {
					$chunk->setFullBlock($x, self::MAX_BASE_ISLAND_HEIGHT + $y, $z, $endStone);
				}

				$reversedNoiseValue = self::NOISE_SIZE - $noiseValue;
				for($y = 0; $y < $reversedNoiseValue; ++$y) {
					$chunk->setFullBlock($x, self::MIN_BASE_ISLAND_HEIGHT - $y, $z, $endStone);
				}

				for($y = self::MIN_BASE_ISLAND_HEIGHT; $y < self::MAX_BASE_ISLAND_HEIGHT; ++$y) {
					$chunk->setFullBlock($x, $y, $z, $endStone);
				}
			}
		}
	}

	public function populateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void {
		foreach($this->populators as $populator) {
			$this->random->setSeed(0xdeadbeef ^ ($chunkX << 8) ^ $chunkZ ^ $this->seed);
			$populator->populate($world, $chunkX, $chunkZ, $this->random);
		}
	}
}