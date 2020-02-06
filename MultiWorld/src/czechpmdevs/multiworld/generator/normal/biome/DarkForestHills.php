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

namespace czechpmdevs\multiworld\generator\normal\biome;

use czechpmdevs\multiworld\generator\normal\populator\object\Plant;
use czechpmdevs\multiworld\generator\normal\populator\PlantPopulator;
use czechpmdevs\multiworld\generator\normal\populator\TallGrass;
use czechpmdevs\multiworld\generator\normal\populator\Tree;
use pocketmine\block\BrownMushroom;
use pocketmine\block\DoublePlant;
use pocketmine\block\RedMushroom;
use pocketmine\block\Sapling;
use pocketmine\level\biome\GrassyBiome;

/**
 * Class DarkForestHills
 * @package czechpmdevs\multiworld\generator\normal\biome
 */
class DarkForestHills extends GrassyBiome {

    public function __construct() {
        parent::__construct();

        $mushrooms = new PlantPopulator();
        $mushrooms->setBaseAmount(2);
        $mushrooms->setRandomAmount(2);
        $mushrooms->addPlant(new Plant(new BrownMushroom()));
        $mushrooms->addPlant(new Plant(new RedMushroom()));
        $mushrooms->setSpawnPercentage(95);

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

        $tree = new Tree(Sapling::DARK_OAK);
        $tree->setBaseAmount(4);
        $tree->setRandomAmount(2);

        $mushroom = new Tree(\czechpmdevs\multiworld\generator\normal\object\Tree::MUSHROOM);
        $mushroom->setBaseAmount(1);
        $mushroom->setRandomAmount(1);
        $mushroom->setSpawnPercentage(90);

        $birch = new Tree(Sapling::BIRCH);
        $birch->setBaseAmount(1);
        $birch->setRandomAmount(2);

        $oak = new Tree(Sapling::OAK);
        $oak->setBaseAmount(1);
        $oak->setRandomAmount(1);

        $this->addPopulator($tree);
        $this->addPopulator($peonys);
        $this->addPopulator($roses);
        $this->addPopulator($mushrooms);
        $this->addPopulator($mushroom);
        $this->addPopulator($birch);
        $this->addPopulator($oak);

        $this->setElevation(78, 84);

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
        return "Dark Forest Hills";
    }
}