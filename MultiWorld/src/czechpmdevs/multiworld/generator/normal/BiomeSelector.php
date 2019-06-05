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


    /** @var Biome[]|\SplFixedArray */
    public $map = null;


    public function __construct(Random $random) {
        $this->temperature = new Simplex($random, 2, 1 / 8, 1 / 2048);
        $this->rainfall = new Simplex($random, 2, 1 / 8, 1 / 2048);
        $this->ocean = new Simplex($random, 6, 1 / 2, 1 / 2048);
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
        if($ocean < -0.25) {
            return Biome::getBiome(BiomeManager::OCEAN);
        }
        if($rainfall < 0) {
            return Biome::getBiome(BiomeManager::DESERT);
        }
        return Biome::getBiome(BiomeManager::PLAINS);
    }
}