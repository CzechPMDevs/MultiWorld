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

use czechpmdevs\multiworld\generator\normal\populator\TallGrass;
use czechpmdevs\multiworld\generator\normal\populator\Tree;
use pocketmine\block\Sapling;
use pocketmine\level\biome\GrassyBiome;

/**
 * Class Mountains
 * @package czechpmdevs\multiworld\generator\normal\biome
 */
class Mountains extends GrassyBiome {

    public function __construct() {
        parent::__construct();
        $this->setElevation(66, 120);

        $tallGrass = new TallGrass();
        $tallGrass->setBaseAmount(10);
        $tallGrass->setRandomAmount(5);

        $spruce = new Tree(Sapling::SPRUCE);
        $spruce->setSpawnPercentage(79);
        $spruce->setRandomAmount(2);
        $spruce->setBaseAmount(1);

        $this->addPopulator($spruce);
        $this->addPopulator($tallGrass);

        $this->temperature = 0.8;
        $this->rainfall = 0.4;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return "Mountains";
    }
}