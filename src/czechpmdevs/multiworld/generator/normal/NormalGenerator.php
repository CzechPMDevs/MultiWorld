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

namespace czechpmdevs\multiworld\generator\normal;

use czechpmdevs\multiworld\generator\normal\populator\impl\CarvePopulator;
use czechpmdevs\multiworld\generator\normal\populator\impl\GroundCoverPopulator;
use pocketmine\block\BlockIds;
use pocketmine\block\CoalOre;
use pocketmine\block\DiamondOre;
use pocketmine\block\Dirt;
use pocketmine\block\GoldOre;
use pocketmine\block\Gravel;
use pocketmine\block\IronOre;
use pocketmine\block\LapisOre;
use pocketmine\block\RedstoneOre;
use pocketmine\block\Stone;
use pocketmine\level\biome\Biome;
use pocketmine\level\ChunkManager;
use pocketmine\level\format\Chunk;
use pocketmine\level\generator\Generator;
use pocketmine\level\generator\noise\Simplex;
use pocketmine\level\generator\object\OreType;
use pocketmine\level\generator\populator\Ore;
use pocketmine\level\generator\populator\Populator;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;
use function exp;

class NormalGenerator extends Generator {

	/** @var float[][] */
	private static array $GAUSSIAN_KERNEL;
	/** @var int */
	private static int $SMOOTH_SIZE = 2;

	/** @var Populator[] */
	private array $populators = [];
	/** @var Populator[] */
	private array $generationPopulators = [];

	/** @var Simplex */
	private Simplex $noiseBase;
	/** @var BiomeSelector */
	private BiomeSelector $selector;

	/** @phpstan-ignore-next-line */
	public function __construct(array $options = []) {
		if(!isset(NormalGenerator::$GAUSSIAN_KERNEL)) {
			NormalGenerator::generateKernel();
		}
	}

	private static function generateKernel(): void {
		NormalGenerator::$GAUSSIAN_KERNEL = [];

		$bellSize = 1 / NormalGenerator::$SMOOTH_SIZE;
		$bellHeight = 2 * NormalGenerator::$SMOOTH_SIZE;

		for($sx = -NormalGenerator::$SMOOTH_SIZE; $sx <= NormalGenerator::$SMOOTH_SIZE; ++$sx) {
			NormalGenerator::$GAUSSIAN_KERNEL[$sx + NormalGenerator::$SMOOTH_SIZE] = [];

			for($sz = -NormalGenerator::$SMOOTH_SIZE; $sz <= NormalGenerator::$SMOOTH_SIZE; ++$sz) {
				$bx = $bellSize * $sx;
				$bz = $bellSize * $sz;
				NormalGenerator::$GAUSSIAN_KERNEL[$sx + NormalGenerator::$SMOOTH_SIZE][$sz + NormalGenerator::$SMOOTH_SIZE] = $bellHeight * exp(-($bx * $bx + $bz * $bz) / 2);
			}
		}
	}

	public function getName(): string {
		return "custom";
	}

	public function getSettings(): array {
		return [];
	}

	public function init(ChunkManager $level, Random $random): void {
		parent::init($level, $random);

		$this->random->setSeed($this->level->getSeed());
		$this->noiseBase = new Simplex($this->random, 4, 1 / 4, 1 / 32);
		$this->random->setSeed($this->level->getSeed());

		$this->selector = new BiomeSelector($this->random);

		$this->generationPopulators[] = new GroundCoverPopulator();

		$this->populators[] = new CarvePopulator();

		$ores = new Ore();
		$ores->setOreTypes([
			new OreType(new CoalOre(), 20, 16, 0, 128),
			new OreType(new IronOre(), 20, 8, 0, 64),
			new OreType(new RedstoneOre(), 8, 7, 0, 16),
			new OreType(new LapisOre(), 1, 6, 0, 32),
			new OreType(new GoldOre(), 2, 8, 0, 32),
			new OreType(new DiamondOre(), 1, 7, 0, 16),
			new OreType(new Dirt(), 20, 32, 0, 128),
			new OreType(new Gravel(), 10, 16, 0, 128),
			new OreType(new Stone(1), 10, 16, 0, 128),
			new OreType(new Stone(3), 10, 16, 0, 128),
			new OreType(new Stone(5), 10, 16, 0, 128),
		]);

		$this->populators[] = $ores;
	}

	public function generateChunk(int $chunkX, int $chunkZ): void {
		$this->random->setSeed(0xdeadbeef ^ ($chunkX << 8) ^ $chunkZ ^ $this->level->getSeed());

		/** @phpstan-var Chunk $chunk */
		$chunk = $this->level->getChunk($chunkX, $chunkZ);
		$noise = $this->noiseBase->getFastNoise3D(16, 128, 16, 4, 8, 4, $chunkX * 16, 0, $chunkZ * 16);

		$biomeCache = [];

		for($x = 0; $x < 16; ++$x) {
			for($z = 0; $z < 16; ++$z) {
				$minSum = 0;
				$maxSum = 0;
				$weightSum = 0;

				$biome = $this->pickBiome($chunkX * 16 + $x, $chunkZ * 16 + $z);
				$chunk->setBiomeId($x, $z, $biome->getId());

				for($sx = -NormalGenerator::$SMOOTH_SIZE; $sx <= NormalGenerator::$SMOOTH_SIZE; ++$sx) {
					for($sz = -NormalGenerator::$SMOOTH_SIZE; $sz <= NormalGenerator::$SMOOTH_SIZE; ++$sz) {

						$weight = NormalGenerator::$GAUSSIAN_KERNEL[$sx + NormalGenerator::$SMOOTH_SIZE][$sz + NormalGenerator::$SMOOTH_SIZE];

						if($sx === 0 and $sz === 0) {
							$adjacent = $biome;
						} else {
							$index = ((($chunkX * 16 + $x + $sx) & 0xFFFFFFFF) << 32) | (($chunkZ * 16 + $z + $sz) & 0xFFFFFFFF);
							if(isset($biomeCache[$index])) {
								$adjacent = $biomeCache[$index];
							} else {
								$biomeCache[$index] = $adjacent = $this->pickBiome($chunkX * 16 + $x + $sx, $chunkZ * 16 + $z + $sz);
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
					if($y == 0) {
						$chunk->setBlockId($x, $y, $z, BlockIds::BEDROCK);
						continue;
					}

					$noiseValue = $noise[$x][$z][$y] - 1 / $smoothHeight * ($y - $smoothHeight - $minSum);

					if($noiseValue > 0) {
						$chunk->setBlockId($x, $y, $z, BlockIds::STONE);
					} elseif($y < 63) {
						$chunk->setBlockId($x, $y, $z, BlockIds::WATER);
					}
				}
			}
		}

		foreach($this->generationPopulators as $populator) {
			$this->random->setSeed(0xdeadbeef ^ ($chunkX << 8) ^ $chunkZ ^ $this->level->getSeed());

			$populator->populate($this->level, $chunkX, $chunkZ, $this->random);
		}
	}

	private function pickBiome(int $x, int $z): Biome {
		$hash = $x * 2345803 ^ $z * 9236449 ^ $this->level->getSeed();
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

	public function populateChunk(int $chunkX, int $chunkZ): void {
		foreach($this->populators as $populator) {
			$this->random->setSeed(0xdeadbeef ^ ($chunkX << 8) ^ $chunkZ ^ $this->level->getSeed());
			$populator->populate($this->level, $chunkX, $chunkZ, $this->random);
		}

		$this->random->setSeed(0xdeadbeef ^ ($chunkX << 8) ^ $chunkZ ^ $this->level->getSeed());

		/** @phpstan-var Chunk $chunk */
		$chunk = $this->level->getChunk($chunkX, $chunkZ);
		BiomeFactory::getInstance()->getBiome($chunk->getBiomeId(7, 7))->populateChunk($this->level, $chunkX, $chunkZ, $this->random);
	}

	public function getSpawn(): Vector3 {
		return new Vector3(127.5, 128, 127.5);
	}
}
