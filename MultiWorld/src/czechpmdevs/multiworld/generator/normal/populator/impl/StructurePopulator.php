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

namespace czechpmdevs\multiworld\generator\normal\populator\impl;

use czechpmdevs\multiworld\generator\normal\populator\Populator;
use czechpmdevs\multiworld\structure\StructureManager;
use czechpmdevs\multiworld\structure\type\PillagerOutpost;
use pocketmine\level\ChunkManager;
use pocketmine\utils\Random;

/**
 * Class StructurePopulator
 * @package czechpmdevs\multiworld\generator\normal\populator
 */
class StructurePopulator extends Populator {

    /**
     * @inheritDoc
     */
    public function populate(ChunkManager $level, int $chunkX, int $chunkZ, Random $random) {
        if($random->nextBoundedInt(200) !== 0) {
            return;
        }

        $this->getRandomSpawnPosition($level, $chunkX, $chunkZ, $random, $x, $y, $z);

        $pillagerOutpost = StructureManager::getStructure(PillagerOutpost::class);
        $pillagerOutpost->placeAt($level, $x, $y, $z, $random);
    }
}