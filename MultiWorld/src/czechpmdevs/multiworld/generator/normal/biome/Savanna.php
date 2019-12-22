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

use czechpmdevs\multiworld\generator\normal\populator\object\Plant;
use czechpmdevs\multiworld\generator\normal\populator\PlantPopulator;
use czechpmdevs\multiworld\generator\normal\populator\TallGrass;
use czechpmdevs\multiworld\generator\normal\populator\Tree;
use pocketmine\block\Dandelion;
use pocketmine\block\Flower;
use pocketmine\block\Sapling;
use pocketmine\level\biome\GrassyBiome;

/**
 * Class Savanna
 * @package czechpmdevs\multiworld\generator\normal\biome
 */
class Savanna extends GrassyBiome {

    public function __construct() {
        parent::__construct();

        $flowers = new PlantPopulator();
        $flowers->setBaseAmount(6);
        $flowers->setRandomAmount(7);
        $flowers->addPlant(new Plant(new Dandelion()));
        $flowers->addPlant(new Plant(new Flower()));
        $flowers->setSpawnPercentage(75);

        $acacia = new Tree(Sapling::ACACIA);
        $acacia->setBaseAmount(1);
        $acacia->setRandomAmount(1);

        $this->addPopulator($acacia);
        $this->addPopulator($flowers);

        $this->setElevation(95, 100);

        $tallGrass = new TallGrass();
        $tallGrass->setBaseAmount(56);
        $tallGrass->setRandomAmount(12);

        $this->addPopulator($tallGrass);

        $this->temperature = 0.8;
        $this->rainfall = 0.4;
    }

    /**
     * @return string
     */
    public function getName(): string {
        return "Savanna";
    }
}