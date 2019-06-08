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

namespace czechpmdevs\multiworld\generator\normal\biome;

use czechpmdevs\multiworld\generator\normal\BiomeManager;
use czechpmdevs\multiworld\generator\normal\populator\CactusPopulator;
use pocketmine\level\biome\SandyBiome;

/**
 * Class DesertHills
 * @package czechpmdevs\multiworld\generator\normal\biome
 */
class DesertHills extends SandyBiome {

    /**
     * Desert constructor.
     */
    public function __construct() {
        $this->setId(BiomeManager::DESERT_HILLS);
        $this->setElevation(64, 85);
        $cactus = new CactusPopulator();
        $cactus->setRandomAmount(4);
        $cactus->setBaseAmount(2);
        $this->addPopulator($cactus);
        parent::__construct();
    }

    /**
     * @return string
     */
    public function getName(): string {
        return "Desert Hills";
    }
}