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
use pocketmine\block\Block;
use pocketmine\block\BrownMushroom;
use pocketmine\block\Dandelion;
use pocketmine\block\DoublePlant;
use pocketmine\block\Flower;
use pocketmine\block\RedMushroom;
use pocketmine\block\Sapling;
use pocketmine\level\biome\GrassyBiome;

/**
 * Class Plains
 * @package czechpmdevs\multiworld\generator\normal\biome
 */
class Forest extends GrassyBiome {

    public function __construct() {
        parent::__construct();

        $mushrooms = new PlantPopulator();
        $mushrooms->setBaseAmount(2);
        $mushrooms->setRandomAmount(2);
        $mushrooms->addPlant(new Plant(new BrownMushroom()));
        $mushrooms->addPlant(new Plant(new RedMushroom()));
        $mushrooms->setSpawnPercentage(95);

        $flowers = new PlantPopulator();
        $flowers->setBaseAmount(6);
        $flowers->setRandomAmount(7);
        $flowers->addPlant(new Plant(new Dandelion()));
        $flowers->addPlant(new Plant(new Flower()));
        $flowers->setSpawnPercentage(75);

        $roses = new PlantPopulator();
        $roses->setBaseAmount(5);
        $roses->setRandomAmount(4);
        $roses->addPlant(new Plant(new DoublePlant(4), new DoublePlant(12)));
        $roses->setSpawnPercentage(50);

        $peonys = new PlantPopulator();
        $peonys->setBaseAmount(5);
        $peonys->setRandomAmount(4);
        $peonys->addPlant(new Plant(new DoublePlant(1), new DoublePlant(9)));
        $peonys->setSpawnPercentage(50);


        $oak = new Tree();
        $oak->setBaseAmount(3);
        $oak->setRandomAmount(3);

        $birch = new Tree(Sapling::BIRCH);
        $birch->setBaseAmount(3);
        $birch->setRandomAmount(3);

        $this->addPopulator($oak);
        $this->addPopulator($birch);
        $this->addPopulator($flowers);
        $this->addPopulator($peonys);
        $this->addPopulator($roses);
        $this->addPopulator($mushrooms);

        $this->setElevation(64, 74);

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
        return "Forest";
    }
}