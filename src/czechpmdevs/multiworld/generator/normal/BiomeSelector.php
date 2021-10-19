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

use czechpmdevs\multiworld\level\data\BiomeIds;
use pocketmine\level\biome\Biome;
use pocketmine\level\generator\noise\Simplex;
use pocketmine\utils\Random;
use function abs;

class BiomeSelector {

	/** @var Simplex */
	public Simplex $temperature;
	/** @var Simplex */
	public Simplex $rainfall;
	/** @var Simplex */
	public Simplex $ocean;
	/** @var Simplex */
	public Simplex $hills;
	/** @var Simplex */
	public Simplex $smallHills;
	/** @var Simplex */
	public Simplex $river;

	public function __construct(Random $random) {
		$this->temperature = new Simplex($random, 4, 1 / 4, 1 / 1024); //2 oct
		$this->rainfall = new Simplex($random, 4, 1 / 4, 1 / 1024); // 2 oct
		$this->river = new Simplex($random, 6, 1 / 2, 1 / 2048);
		$this->ocean = new Simplex($random, 6, 1 / 2, 1 / 2048);
		$this->hills = new Simplex($random, 6, 1 / 2, 1 / 512);
		$this->smallHills = new Simplex($random, 2, 1 / 32, 1 / 64);
	}

	public function pickBiome(float $x, float $z): Biome {
		$temperature = $this->temperature->noise2D($x, $z, true) * 1.2;
		$rainfall = $this->rainfall->noise2D($x, $z, true) * 1.2;
		$river = $this->river->noise2D($x, $z, true);
		$ocean = $this->ocean->noise2D($x, $z, true);
		$hills = $this->hills->noise2D($x, $z, true);
		$smallHills = $this->smallHills->noise2D($x, $z, true);

		if($ocean < -0.2) {
			if($temperature < 0) {
				return BiomeFactory::getInstance()->getBiome(BiomeIds::FROZEN_OCEAN);
			}

			if($ocean > -0.4) {
				if($temperature > 0.5 && $rainfall > 0.5 && $hills < 0) {
					return BiomeFactory::getInstance()->getBiome(BiomeIds::SWAMP);
				}
			}

			if($ocean > -0.2687) {
				if($temperature > -0.2 && $temperature < 0.6 && $smallHills > -0.4) {
					return BiomeFactory::getInstance()->getBiome(BiomeIds::BEACH);
				}
			}

			if($ocean < -0.55) {
				if($ocean < -0.7 && $rainfall < 0 && $temperature > 0) {
					if($ocean > -0.68) {
						return BiomeFactory::getInstance()->getBiome(BiomeIds::MUSHROOM_ISLAND_SHORE);
					}

					return BiomeFactory::getInstance()->getBiome(BiomeIds::MUSHROOM_ISLAND);
				}

				return BiomeFactory::getInstance()->getBiome(BiomeIds::DEEP_OCEAN);
			}


			return BiomeFactory::getInstance()->getBiome(BiomeIds::OCEAN);
		}

		if(abs($river) < 0.03) {
			if($temperature < 0) {
				return BiomeFactory::getInstance()->getBiome(BiomeIds::FROZEN_RIVER);
			}

			return BiomeFactory::getInstance()->getBiome(BiomeIds::RIVER);
		}

		if($temperature < -0.7) {
			if($rainfall > 0) {
				if($hills > 0) {
					return BiomeFactory::getInstance()->getBiome(BiomeIds::EXTREME_HILLS_MUTATED);
				}

				return BiomeFactory::getInstance()->getBiome(BiomeIds::EXTREME_HILLS_EDGE);
			}

			return BiomeFactory::getInstance()->getBiome(BiomeIds::EXTREME_HILLS);
		}

		if($temperature < -0.4) {
			if($rainfall > 0) {
				if($smallHills > 0.1) {
					return BiomeFactory::getInstance()->getBiome(BiomeIds::TAIGA_HILLS);
				}
				return BiomeFactory::getInstance()->getBiome(BiomeIds::TAIGA);
			}

			if($hills > 0.1) {
				return BiomeFactory::getInstance()->getBiome(BiomeIds::ICE_MOUNTAINS);
			}

			return BiomeFactory::getInstance()->getBiome(BiomeIds::ICE_PLAINS);
		}

		if($temperature < -0.1) {
			if($rainfall < -0.1) {
				if($hills > 0) {
					return BiomeFactory::getInstance()->getBiome(BiomeIds::BADLANDS);
				}

				return BiomeFactory::getInstance()->getBiome(BiomeIds::BADLANDS_PLATEAU);
			}

			if($smallHills > 0.1) {
				return BiomeFactory::getInstance()->getBiome(BiomeIds::ROOFED_FOREST_HILLS);
			}

			return BiomeFactory::getInstance()->getBiome(BiomeIds::ROOFED_FOREST);
		}

		if($temperature < 0.17) {
			if($rainfall < -0.1) {
//                if($smallHills > 0.1) {
//                    return BiomeFactory::getInstance()->getBiome(BiomeIds::BIRCH_FOREST_HILLS);
//                }
				if($rainfall < -0.6 && $temperature > 0.15) {
					return BiomeFactory::getInstance()->getBiome(BiomeIds::TALL_BIRCH_FOREST);
				}
				return BiomeFactory::getInstance()->getBiome(BiomeIds::BIRCH_FOREST);
			}

			if($smallHills > 0.2) {
				return BiomeFactory::getInstance()->getBiome(BiomeIds::FOREST_HILLS);
			}

			return BiomeFactory::getInstance()->getBiome(BiomeIds::FOREST);
		}

		if($temperature < 0.6) {
			if($rainfall > 0) {
				if($hills > 0.5 && $smallHills > 0.1) {
					return BiomeFactory::getInstance()->getBiome(BiomeIds::SUNFLOWER_PLAINS);
				}

				return BiomeFactory::getInstance()->getBiome(BiomeIds::PLAINS);
			}

			if($hills > 0) {
				return BiomeFactory::getInstance()->getBiome(BiomeIds::SAVANNA_PLATEAU);
			}

			return BiomeFactory::getInstance()->getBiome(BiomeIds::SAVANNA);
		}

		if($rainfall > 0.2) {
//            if($smallHills > 0.1) {
//                return BiomeFactory::getInstance()->getBiome(BiomeIds::JUNGLE_HILLS);
//            }

			return BiomeFactory::getInstance()->getBiome(BiomeIds::JUNGLE);
		}

		if($hills > 0.2 && $smallHills > 0.1) {
			return BiomeFactory::getInstance()->getBiome(BiomeIds::DESERT_HILLS);
		}

		return BiomeFactory::getInstance()->getBiome(BiomeIds::DESERT);
	}
}