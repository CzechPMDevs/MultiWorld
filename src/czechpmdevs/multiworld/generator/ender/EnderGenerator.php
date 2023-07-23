<?php

/**
 * MultiWorld - PocketMine plugin that manages worlds.
 * Copyright (C) 2018 - 2023  CzechPMDevs
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
use pocketmine\world\World;
use function abs;

class EnderGenerator extends Generator {
	public const BASE_ISLAND_HEIGHT = 55;
	public const NOISE_SIZE = 12;
	public const NOISE_SIZE_HALF = 6;

	public const CENTER_X = 255;
	public const CENTER_Z = 255;

	public const ISLAND_RADIUS = 100;

	private Simplex $baseNoise;
	private Simplex $islandNoise;

	/** @var Populator[] */
	private array $populators = [];

	public function __construct(int $seed, string $preset) {
		parent::__construct($seed, $preset);

		$this->baseNoise = new Simplex($this->random, 4, 1 / 16, 1 / 128);
		$this->islandNoise = new Simplex($this->random, 1, 1, 1 / 1024);
		$this->populators[] = new EnderPilar(self::CENTER_X, self::CENTER_Z, self::ISLAND_RADIUS);
	}

	public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void {
		$this->random->setSeed(0xdeadbeef ^ ($chunkX << 8) ^ $chunkZ ^ $this->seed);

		/** @phpstan-var Chunk $chunk */
		$chunk = $world->getChunk($chunkX, $chunkZ);
		$noise = $this->baseNoise->getFastNoise2D(16, 16, 2, $chunkX * 16, 0, $chunkZ * 16);
		$islandNoise = $this->islandNoise->getFastNoise2D(16, 16, 2, $chunkX * 16, 0, $chunkZ * 16);

		$endStone = VanillaBlocks::END_STONE()->getStateId();

		$baseX = $chunkX * Chunk::EDGE_LENGTH;
		$baseZ = $chunkZ * Chunk::EDGE_LENGTH;
		for($x = 0; $x < 16; ++$x) {
			$absoluteX = $baseX + $x;
			for($z = 0; $z < 16; ++$z) {
				$absoluteZ = $baseZ + $z;

				for($y = World::Y_MIN; $y < World::Y_MAX; ++$y)
					$chunk->setBiomeId($x, $y, $z, BiomeIds::THE_END);


				// @phpstan-ignore-next-line
				$islandNoiseValue = abs($islandNoise[$x][$z]);
				if(
					(($absoluteX - self::CENTER_X) ** 2 + ($absoluteZ - self::CENTER_Z) ** 2 > self::ISLAND_RADIUS ** 2) &&
					$islandNoiseValue < 0.5
				) {
					continue;
				}

				// @phpstan-ignore-next-line
				$noiseValue = (int)abs($noise[$x][$z] * self::NOISE_SIZE) + self::NOISE_SIZE_HALF;
				for($y = -$noiseValue; $y < $noiseValue; ++$y) {
					$chunk->setBlockStateId($x, self::BASE_ISLAND_HEIGHT + $y, $z, $endStone);
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