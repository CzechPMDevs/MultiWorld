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

use czechpmdevs\multiworld\generator\nether\populator\Ore;
use czechpmdevs\multiworld\generator\normal\populator\CactusPopulator;
use czechpmdevs\multiworld\generator\normal\populator\object\Plant;
use czechpmdevs\multiworld\generator\normal\populator\PlantPopulator;
use pocketmine\block\BlockIds;
use pocketmine\block\DeadBush;
use pocketmine\block\GoldOre;
use pocketmine\block\HardenedClay;
use pocketmine\block\Sand;
use pocketmine\block\StainedClay;
use pocketmine\level\biome\Biome;
use pocketmine\level\generator\object\OreType;

/**
 * Class MesaPlateau
 * @package czechpmdevs\multiworld\generator\normal\biome
 */
class MesaPlateau extends Biome {

    public function __construct() {
        $this->setGroundCover([
            new Sand(1),
            new HardenedClay(),
            new HardenedClay(),
            new StainedClay(0),
            new HardenedClay(),
            new HardenedClay(),
            new StainedClay(4),
            new StainedClay(4),
            new HardenedClay(),
            new HardenedClay(),
            new HardenedClay(),
            new StainedClay(1),
            new StainedClay(1),
            new HardenedClay()
        ]);

        $this->setElevation(63, 66);

        $plantPopulator = new PlantPopulator();
        $plantPopulator->setSpawnPercentage(70);
        $plantPopulator->addPlant(new Plant(new DeadBush()));
        $plantPopulator->setBaseAmount(3);
        $plantPopulator->setRandomAmount(2);
        $plantPopulator->allowBlockToStayAt(BlockIds::SAND);
        $this->addPopulator($plantPopulator);

        $cactus = new CactusPopulator();
        $cactus->setBaseAmount(2);
        $cactus->setRandomAmount(2);
        $this->addPopulator($cactus);

        $ore = new Ore();
        $ore->setOreTypes([
            new OreType(new GoldOre(), 20, 12, 0, 128)
        ]);

        $this->addPopulator($ore);
    }

    public function getName(): string {
        return "Mesa Plateau";
    }
}