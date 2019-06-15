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

use czechpmdevs\multiworld\generator\normal\populator\LakePopulator;
use czechpmdevs\multiworld\generator\normal\populator\object\Plant;
use czechpmdevs\multiworld\generator\normal\populator\PlantPopulator;
use czechpmdevs\multiworld\generator\normal\populator\TallGrass;
use czechpmdevs\multiworld\generator\normal\populator\Tree;
use pocketmine\block\Dandelion;
use pocketmine\block\Flower;
use pocketmine\level\biome\GrassyBiome;

/**
 * Class Plains
 * @package czechpmdevs\multiworld\generator\normal\biome
 */
class Plains extends GrassyBiome {

    public function __construct() {
        $this->setElevation(64, 67);

        $flowers = new PlantPopulator();
        $flowers->setBaseAmount(6);
        $flowers->setRandomAmount(7);
        $flowers->addPlant(new Plant(new Dandelion()));
        $flowers->addPlant(new Plant(new Flower()));
        $flowers->setSpawnPercentage(80);

        $daisy = new PlantPopulator();
        $daisy->setBaseAmount(6);
        $daisy->setRandomAmount(7);
        $daisy->addPlant(new Plant(new Flower(8)));
        $daisy->setSpawnPercentage(80);

        $daisy = new PlantPopulator();
        $daisy->setBaseAmount(6);
        $daisy->setRandomAmount(7);
        $daisy->addPlant(new Plant(new Flower(8)));
        $daisy->setSpawnPercentage(80);

        $bluet = new PlantPopulator();
        $bluet->setBaseAmount(6);
        $bluet->setRandomAmount(7);
        $bluet->addPlant(new Plant(new Flower(3)));
        $bluet->setSpawnPercentage(80);

        $tulips = new PlantPopulator();
        $tulips->setBaseAmount(6);
        $tulips->setRandomAmount(7);
        $tulips->addPlant(new Plant(new Flower(4)));
        $tulips->addPlant(new Plant(new Flower(5)));
        $tulips->setSpawnPercentage(80);

        $tree = new Tree();
        $tree->setBaseAmount(1);
        $tree->setRandomAmount(1);
        $tree->setSpawnPercentage(80);

        $this->addPopulator(new LakePopulator());
        $this->addPopulator($tree);
        $this->addPopulator($flowers);
        $this->addPopulator($daisy);
        $this->addPopulator($bluet);
        $this->addPopulator($tulips);

        $tallGrass = new TallGrass();
        $tallGrass->setBaseAmount(89);
        $tallGrass->setRandomAmount(26);

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