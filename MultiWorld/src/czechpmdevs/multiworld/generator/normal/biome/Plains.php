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
use czechpmdevs\multiworld\generator\normal\populator\LakePopulator;
use czechpmdevs\multiworld\generator\normal\populator\TallGrass;
use czechpmdevs\multiworld\generator\normal\populator\Tree;
use pocketmine\block\Sapling;
use pocketmine\level\biome\GrassyBiome;

/**
 * Class Plains
 * @package czechpmdevs\multiworld\generator\normal\biome
 */
class Plains extends GrassyBiome {

    public function __construct() {
        $this->setElevation(64, 67);

        $tree = new Tree();
        $tree->setBaseAmount(1);
        $tree->setRandomAmount(1);
        $tree->setSpawnPercentage(87);

        $this->addPopulator(new LakePopulator());
        $this->addPopulator($tree);

        $tallGrass = new TallGrass();
        $tallGrass->setBaseAmount(56);
        $tallGrass->setRandomAmount(12);

        $this->addPopulator($tallGrass);

        $this->temperature = 0.8;
        $this->rainfall = 0.4;
        parent::__construct();
    }

    /**
     * @return string
     */
    public function getName(): string {
        return "Plains";
    }
}