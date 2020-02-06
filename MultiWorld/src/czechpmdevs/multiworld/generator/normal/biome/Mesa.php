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
use czechpmdevs\multiworld\generator\normal\populator\Tree;
use pocketmine\block\BlockIds;
use pocketmine\block\DeadBush;
use pocketmine\block\GoldOre;
use pocketmine\block\Grass;
use pocketmine\block\HardenedClay;
use pocketmine\block\Sand;
use pocketmine\block\Sapling;
use pocketmine\block\StainedClay;
use pocketmine\level\biome\Biome;
use pocketmine\level\generator\object\OreType;

/**
 * Class Mesa
 * @package czechpmdevs\multiworld\generator\normal\biome
 */
class Mesa extends Biome {

    public function __construct() {
        $this->setGroundCover([
            new Grass(),
            new HardenedClay(),
            new StainedClay(7),
            new StainedClay(0),
            new StainedClay(14),
            new HardenedClay(),
            new StainedClay(4),
            new StainedClay(4),
            new HardenedClay(),
            new HardenedClay(),
            new StainedClay(1),
            new StainedClay(1),
            new HardenedClay(),
            new StainedClay(7),
            new StainedClay(8),
            new StainedClay(4),
            new HardenedClay()
        ]);


        $this->setElevation(84, 86);

        $tree = new Tree(Sapling::OAK);
        $tree->setBaseAmount(3);
        $tree->setRandomAmount(2);
        $tree->setSpawnPercentage(100);
        $this->addPopulator($tree);

        $ore = new Ore();
        $ore->setOreTypes([
            new OreType(new GoldOre(), 20, 12, 0, 128)
        ]);

        $this->addPopulator($ore);
    }

    public function getName(): string {
        return "Mesa";
    }
}