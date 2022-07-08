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

namespace czechpmdevs\multiworld\generator\normal;

use czechpmdevs\multiworld\generator\normal\biome\types\Biome;
use czechpmdevs\multiworld\generator\normal\populator\impl\CarvePopulator;
use czechpmdevs\multiworld\generator\normal\populator\impl\GroundCoverPopulator;
use pocketmine\block\VanillaBlocks;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\format\Chunk;
use pocketmine\world\generator\Gaussian;
use pocketmine\world\generator\Generator;
use pocketmine\world\generator\noise\Simplex;
use pocketmine\world\generator\object\OreType;
use pocketmine\world\generator\populator\Ore;
use pocketmine\world\generator\populator\Populator;
use pocketmine\world\World;

class NormalGenerator extends Generator {

	/** @var Populator[] */
	private array $populators = [];
	/** @var Populator[] */
	private array $generationPopulators = [];

	private Simplex $noiseBase;

	private BiomeSelector $selector;

	private Gaussian $gaussian;

	public function __construct(int $seed, string $preset) {
		parent::__construct($seed, $preset);

		$this->gaussian = new Gaussian(2);

		$this->noiseBase = new Simplex($this->random, 4, 1 / 4, 1 / 32);
		$this->random->setSeed($seed);

		$this->selector = new BiomeSelector(new Random($this->seed));

		// Generation populators...
		$this->generationPopulators[] = new GroundCoverPopulator();

		// Other populators...
		$this->populators[] = new CarvePopulator($this->seed);

		$ores = new Ore();
		$stone = VanillaBlocks::STONE();
		$ores->setOreTypes([
			new OreType(VanillaBlocks::COAL_ORE(), $stone, 20, 16, 0, 128),
			new OreType(VanillaBlocks::IRON_ORE(), $stone, 20, 8, 0, 64),
			new OreType(VanillaBlocks::REDSTONE_ORE(), $stone, 8, 7, 0, 16),
			new OreType(VanillaBlocks::LAPIS_LAZULI_ORE(), $stone, 1, 6, 0, 32),
			new OreType(VanillaBlocks::GOLD_ORE(), $stone, 2, 8, 0, 32),
			new OreType(VanillaBlocks::DIAMOND_ORE(), $stone, 1, 7, 0, 16),
			new OreType(VanillaBlocks::DIRT(), $stone, 20, 32, 0, 128),
			new OreType(VanillaBlocks::GRAVEL(), $stone, 10, 16, 0, 128),
			new OreType(VanillaBlocks::ANDESITE(), $stone, 10, 16, 0, 128),
			new OreType(VanillaBlocks::GRANITE(), $stone, 10, 16, 0, 128),
			new OreType(VanillaBlocks::DIORITE(), $stone, 10, 16, 0, 128)
		]);
		$this->populators[] = $ores;
	}

	public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void {
		$this->random->setSeed(0xdeadbeef ^ ($chunkX << 8) ^ $chunkZ ^ $this->seed);

		$noise = $this->noiseBase->getFastNoise3D(16, 128, 16, 4, 8, 4, $chunkX * 16, 0, $chunkZ * 16);

		/** @var Chunk $chunk */
		$chunk = $world->getChunk($chunkX, $chunkZ);

		$biomeCache = [];

		$bedrock = VanillaBlocks::BEDROCK()->getStateId();
		$stillWater = VanillaBlocks::WATER()->getStateId();
		$stone = VanillaBlocks::STONE()->getStateId();

		$baseX = $chunkX * 16;
		$baseZ = $chunkZ * 16;
		for($x = 0; $x < 16; ++$x) {
			$absoluteX = $baseX + $x;
			for($z = 0; $z < 16; ++$z) {
				$absoluteZ = $baseZ + $z;
				$minSum = 0;
				$maxSum = 0;
				$weightSum = 0;

				$biome = $this->pickBiome($absoluteX, $absoluteZ);
				$chunk->setBiomeId($x, $z, $biome->getId());

				for($sx = -$this->gaussian->smoothSize; $sx <= $this->gaussian->smoothSize; ++$sx) {
					for($sz = -$this->gaussian->smoothSize; $sz <= $this->gaussian->smoothSize; ++$sz) {

						$weight = $this->gaussian->kernel[$sx + $this->gaussian->smoothSize][$sz + $this->gaussian->smoothSize];

						if($sx === 0 and $sz === 0) {
							$adjacent = $biome;
						} else {
							$index = World::chunkHash($absoluteX + $sx, $absoluteZ + $sz);
							if(isset($biomeCache[$index])) {
								$adjacent = $biomeCache[$index];
							} else {
								$biomeCache[$index] = $adjacent = $this->pickBiome($absoluteX + $sx, $absoluteZ + $sz);
							}
						}

						$minSum += ($adjacent->getMinElevation() - 1) * $weight;
						$maxSum += $adjacent->getMaxElevation() * $weight;

						$weightSum += $weight;
					}
				}

				$minSum /= $weightSum;
				$maxSum /= $weightSum;

				$smoothHeight = ($maxSum - $minSum) / 2;

				for($y = 0; $y < 128; ++$y) {
					if($y === 0) {
						$chunk->setFullBlock($x, $y, $z, $bedrock);
						continue;
					}
					$noiseValue = $noise[$x][$z][$y] - 1 / $smoothHeight * ($y - $smoothHeight - $minSum);

					if($noiseValue > 0) {
						$chunk->setFullBlock($x, $y, $z, $stone);
					} elseif($y <= 60) {
						$chunk->setFullBlock($x, $y, $z, $stillWater);
					}
				}
			}
		}

		foreach($this->generationPopulators as $populator) {
			$populator->populate($world, $chunkX, $chunkZ, $this->random);
		}
	}

	private function pickBiome(int $x, int $z): Biome {
		$hash = $x * 2345803 ^ $z * 9236449 ^ $this->seed;
		$hash *= $hash + 223;
		$xNoise = $hash >> 20 & 3;
		$zNoise = $hash >> 22 & 3;
		if($xNoise == 3) {
			$xNoise = 1;
		}
		if($zNoise == 3) {
			$zNoise = 1;
		}

		return $this->selector->pickBiome($x + $xNoise - 1, $z + $zNoise - 1);
	}

	public function populateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void {
		foreach($this->populators as $populator) {
			$this->random->setSeed(0xdeadbeef ^ ($chunkX << 8) ^ $chunkZ ^ $this->seed);
			$populator->populate($world, $chunkX, $chunkZ, $this->random);
		}

		$this->random->setSeed(0xdeadbeef ^ ($chunkX << 8) ^ $chunkZ ^ $this->seed);

		/** @phpstan-var Chunk $chunk */
		$chunk = $world->getChunk($chunkX, $chunkZ);
		$biome = BiomeFactory::getInstance()->getBiome($chunk->getBiomeId(7, 7));
		$biome->populateChunk($world, $chunkX, $chunkZ, $this->random);
	}
}
