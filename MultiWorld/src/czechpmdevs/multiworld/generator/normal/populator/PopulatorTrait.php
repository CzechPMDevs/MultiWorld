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

namespace czechpmdevs\multiworld\generator\normal\populator;

use pocketmine\block\Block;
use pocketmine\level\ChunkManager;

/**
 * Trait PopulatorTrait
 * @package czechpmdevs\multiworld\generator\normal\populator
 */
trait PopulatorTrait {

    public function getHighestWorkableBlock(ChunkManager $level, int $x, int $z) : int{
        for($y = 127; $y >= 0; --$y){
            $b = $level->getBlockIdAt($x, $y, $z);
            if($b !== Block::AIR and $b !== Block::LEAVES and $b !== Block::LEAVES2 and $b !== Block::SNOW_LAYER){
                break;
            }
        }

        return $y === 0 ? -1 : ++$y;
    }
}