<?php

/**
 * MultiWorld - PocketMine plugin that manages worlds.
 * Copyright (C) 2018 - 2020  CzechPMDevs
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

    /** @var Simplex $hills */
    public $hills;

    /** @var Simplex $smallHills */
    public $smallHills;

    /** @var Simplex $river */
    public $river;


    public function __construct(Random $random) {
        $this->temperature = new Simplex($random, 2, 1 / 8, 1 / 2048);
        $this->rainfall = new Simplex($random, 2, 1 / 8, 1 / 2048);
        $this->ocean = new Simplex($random, 6, 1 / 2, 1 / 2048);
        $this->hills = new Simplex($random, 6, 1 / 2, 1 / 2048);
        $this->smallHills = new Simplex($random, 2, 1 / 32, 1 / 256);
        $this->river = new Simplex($random, 6, 1 / 2, 1 / 1024);


    }

    /**
     * @param $x
     * @param $z
     *
     * @return float|int
     */
    public function getTemperature($x, $z){
        return abs(round($this->temperature->noise2D($x, $z, true) * M_PI / 3 * 2, 1));
    }

    /**
     * @param $x
     * @param $z
     *
     * @return float|int
     */
    public function getRainfall($x, $z) {
        return abs(round($this->rainfall->noise2D($x, $z, true) * M_PI / 3 * 2, 1));
    }

    /**
     * @param $x
     * @param $z
     *
     * @return float|int
     */
    public function getSmallHills($x, $z) {
        return $this->smallHills->noise2D($x, $z, true);
    }

    /**
     * @param $x
     * @param $z
     *
     * @return float|int
     */
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
    public function pickBiome($x, $z): Biome {
        if(abs($this->getRiver($x, $z)) < 0.04) {
            return BiomeManager::getBiome(BiomeManager::RIVER);
        }

        $temperature = $this->getTemperature($x, $z);
        $rainfall = $this->getRainfall($x, $z);
        $hills = $this->getSmallHills($x, $z);

        $biomes = BiomeManager::lookupForBiome($temperature, $rainfall);

        if($hills < -0.1 || !isset($biomes[1])) {
            return $biomes[0];
        }
        elseif($hills < 0.1 || !isset($biomes[2])) {
            return $biomes[1];
        }
        return $biomes[2];
    }
}