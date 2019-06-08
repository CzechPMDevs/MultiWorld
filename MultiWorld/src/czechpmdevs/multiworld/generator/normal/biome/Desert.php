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

use czechpmdevs\multiworld\generator\normal\populator\CactusPopulator;
use czechpmdevs\multiworld\generator\normal\populator\object\Plant;
use czechpmdevs\multiworld\generator\normal\populator\PlantPopulator;
use pocketmine\block\Block;
use pocketmine\block\DeadBush;
use pocketmine\level\biome\SandyBiome;

/**
 * Class Desert
 * @package czechpmdevs\multiworld\generator\normal\biome
 */
class Desert extends SandyBiome {

    /**
     * Desert constructor.
     */
    public function __construct() {
        $this->setElevation(63, 69);
        $cactus = new CactusPopulator();
        $cactus->setRandomAmount(4);
        $cactus->setBaseAmount(3);
        $this->addPopulator($cactus);

        $deadBush = new PlantPopulator();
        $deadBush->setRandomAmount(4);
        $deadBush->setBaseAmount(2);
        $deadBush->addPlant(new Plant(new DeadBush()));
        $deadBush->allowBlockToStayAt(Block::SAND);
        $this->addPopulator($deadBush);
        parent::__construct();
    }

    /**
     * @return string
     */
    public function getName(): string {
        return "Desert";
    }
}