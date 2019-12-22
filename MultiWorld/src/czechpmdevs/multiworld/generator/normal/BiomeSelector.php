<?php

/**
 * MultiWorld - PocketMine plugin that manages worlds.
 * Copyright (C) 2018 - 2019  CzechPMDevs
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

use czechpmdevs\multiworld\generator\normal\biome\BirchForest;
use const pocketmine\BUILD_NUMBER;
use pocketmine\level\biome\Biome;
use pocketmine\level\generator\noise\Simplex;
use pocketmine\utils\Random;

/**
 * Class BiomeSelector
 * @package czechpmdevs\multiworld\generator\normal
 */
class BiomeSelector {

    /** @var Simplex $temperature */
    public $temperature;

    /** @var Simplex $rainfall */
    public $rainfall;

    /** @var Simplex $ocean */
    public $ocean;

    /** @var Simplex $hills */
    public $hills;

    /** @var Simplex $river */
    public $river;

    public function __construct(Random $random) {
        $this->temperature = new Simplex($random, 2, 1 / 8, 1 / 2048);
        $this->rainfall = new Simplex($random, 2, 1 / 8, 1 / 2048);
        $this->ocean = new Simplex($random, 6, 1 / 2, 1 / 2048);
        $this->hills = new Simplex($random, 6, 1 / 2, 1 / 2048);
        $this->river = new Simplex($random, 6, 1 / 2, 1 / 1024);
    }


    public function getTemperature($x, $z){
        return $this->temperature->noise2D($x, $z, true);
    }

    public function getRainfall($x, $z){
        return $this->rainfall->noise2D($x, $z, true);
    }

    public function getOcean($x, $z) {
        return $this->ocean->noise2D($x, $z, true);
    }

    public function getHills($x, $z) {
        return $this->hills->noise2D($x, $z, true);
    }

    public function getRiver($x, $z) {
        return $this->river->noise2D($x, $z, true);
    }


    /**
     * TODO: not sure on types here
     * @param int|float $x
     * @param int|float $z
     *
     * @return Biome
     */
    public function pickBiome($x, $z) : Biome{
        $temperature = $this->getTemperature($x, $z);
        $rainfall = $this->getRainfall($x, $z);
        $ocean = $this->getOcean($x, $z);
        $hills = $this->getHills($x, $z);
        $river = $this->getRiver($x, $z);

        if($ocean < -0.25) {
            if($ocean < 0.6)
                return BiomeManager::getBiome(BiomeManager::DEEP_OCEAN);
            return BiomeManager::getBiome(BiomeManager::OCEAN);
        }

        if(abs($river) < 0.04) {
            return BiomeManager::getBiome(BiomeManager::RIVER);
        }

        if($ocean > 0 && $temperature > 0.45) {
            if($temperature < 0.55) {
                return BiomeManager::getBiome(BiomeManager::SUNFLOWER_PLAINS);
            }
            return BiomeManager::getBiome(BiomeManager::PLAINS);
        }


        if($ocean < -0.2) {
            if($temperature > 0.8) {
                return BiomeManager::getBiome(BiomeManager::SWAMP);
            }
            elseif($temperature < 0.4 && $ocean < - 0.22) {
                return BiomeManager::getBiome(BiomeManager::BEACH);
            }
        }

        if($rainfall < -0.4) {
            if($hills > 0.4) {
                return BiomeManager::getBiome(BiomeManager::DESERT_HILLS);
            }
            return BiomeManager::getBiome(BiomeManager::DESERT);
        }

        if($temperature < -0.5) {
            return BiomeManager::getBiome(BiomeManager::TAIGA);
        }

        if($temperature > -0.4) {
            if($hills > 0.8) {
                return BiomeManager::getBiome(BiomeManager::FOREST_HILLS);
            }
            if($temperature > 0.4) {
                if($hills < 0.4) {
                    return BiomeManager::getBiome(BiomeManager::BIRCH_FOREST);
                }
                else {
                    return BiomeManager::getBiome(BiomeManager::TALL_BIRCH_FOREST);
                }
            }

            if($temperature < 0.2) {
                return BiomeManager::getBiome(BiomeManager::FOREST);
            }

            return BiomeManager::getBiome(BiomeManager::SAVANNA);
        }

        if($hills > 0.1) {
            return BiomeManager::getBiome(BiomeManager::MOUNTAINS);
        }

        return BiomeManager::getBiome(BiomeManager::GRAVELLY_MOUNTAINS);
    }
}