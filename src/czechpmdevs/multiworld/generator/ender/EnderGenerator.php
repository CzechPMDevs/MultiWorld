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

namespace czechpmdevs\multiworld\generator\ender;

use czechpmdevs\multiworld\generator\ender\populator\EnderPilar;
use pocketmine\block\BlockIds;
use pocketmine\level\ChunkManager;
use pocketmine\level\format\Chunk;
use pocketmine\level\generator\Generator;
use pocketmine\level\generator\noise\Simplex;
use pocketmine\level\generator\populator\Populator;
use pocketmine\math\Vector3 as Vector3;
use pocketmine\utils\Random;
use function abs;

class EnderGenerator extends Generator {

	/** @var ChunkManager */
	protected $level;
	/** @var Random */
	protected $random;

	/** @var Simplex */
	private Simplex $noiseBase;

	/** @var Populator[] */
	private array $populators = [];
	/** @var Populator[] */
	private array $generationPopulators = [];

	/** @var int */
	private int $emptyHeight = 32;

	/** @var float */
	private float $emptyAmplitude = 1;
	/** @var float */
	private float $density = 0.6;

	/** @phpstan-ignore-next-line */
	public function __construct(array $options = []) {
	}

	public function init(ChunkManager $level, Random $random): void {
		$this->level = $level;
		$this->random = $random;
		$this->random->setSeed($this->level->getSeed());
		$this->noiseBase = new Simplex($this->random, 4, 1 / 4, 1 / 64);
		$this->random->setSeed($this->level->getSeed());
		$pilar = new EnderPilar;
		$pilar->setBaseAmount(0);
		$pilar->setRandomAmount(0);
		$this->populators[] = $pilar;
	}

	public function generateChunk(int $chunkX, int $chunkZ): void {
		$this->random->setSeed(0xa6fe78dc ^ ($chunkX << 8) ^ $chunkZ ^ $this->level->getSeed());

		/** @phpstan-var Chunk $chunk */
		$chunk = $this->level->getChunk($chunkX, $chunkZ);
		$noise = $this->noiseBase->getFastNoise3D(16, 128, 16, 4, 8, 4, $chunkX * 16, 0, $chunkZ * 16);

		for($x = 0; $x < 16; ++$x) {
			for($z = 0; $z < 16; ++$z) {
				// 9 = biome end
				$chunk->setBiomeId($x, $z, 9);
				for($y = 0; $y < 128; ++$y) {
					$noiseValue = (abs($this->emptyHeight - $y) / $this->emptyHeight) * $this->emptyAmplitude - $noise[$x][$z][$y];
					$noiseValue -= 1 - $this->density;
					$distance = new Vector3(0, 64, 0);
					$distance = $distance->distance(new Vector3($chunkX * 16 + $x, ($y / 1.3), $chunkZ * 16 + $z));
					if($noiseValue < 0 && $distance < 100 or $noiseValue < -0.2 && $distance > 400) {
						$chunk->setBlockId($x, $y, $z, BlockIds::END_STONE);
					}
				}
			}
		}
		foreach($this->generationPopulators as $populator) {
			$populator->populate($this->level, $chunkX, $chunkZ, $this->random);
		}
	}

	public function populateChunk(int $chunkX, int $chunkZ): void {
		$this->random->setSeed(0xa6fe78dc ^ ($chunkX << 8) ^ $chunkZ ^ $this->level->getSeed());
		foreach($this->populators as $populator) {
			$populator->populate($this->level, $chunkX, $chunkZ, $this->random);
		}
	}

	public function getName(): string {
		return "ender";
	}

	public function getSpawn(): Vector3 {
		return new Vector3(48, 128, 48);
	}

	public function getSettings(): array {
		return [];
	}
}