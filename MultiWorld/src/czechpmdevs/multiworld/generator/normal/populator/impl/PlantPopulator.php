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

use czechpmdevs\multiworld\generator\normal\populator\AmountPopulator;
use czechpmdevs\multiworld\generator\normal\populator\object\Plant;
use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;

/**
 * Class PlantPopulator
 * @package czechpmdevs\multiworld\generator\normal\populator\impl
 */
class PlantPopulator extends AmountPopulator {

    /** @var Plant[] $plants */
    private $plants = [];

    /** @var array $allowedBlocks */
    private $allowedBlocks = [];

    /**
     * @param Plant $plant
     */
    public function addPlant(Plant $plant) {
        $this->plants[] = $plant;
    }

    /**
     * @param int $blockId
     */
    public function allowBlockToStayAt(int $blockId) {
        $this->allowedBlocks[] = $blockId;
    }

    /**
     * @param ChunkManager $level
     * @param int $chunkX
     * @param int $chunkZ
     * @param Random $random
     * @return void
     */
    public function populateObject(ChunkManager $level, int $chunkX, int $chunkZ, Random $random): void {
        if(count($this->plants) === 0) {
            return;
        }

        $this->getRandomSpawnPosition($level, $chunkX, $chunkZ, $random, $x, $y, $z);

        if($y !== -1 and $this->canPlantStay($level, $x, $y, $z)){
            $plant = $random->nextRange(0, (int)(count($this->plants)-1));
            $pY = $y;
            foreach ($this->plants[$plant]->blocks as $block) {
                $level->setBlockIdAt($x, $pY, $z, $block->getId());
                $level->setBlockDataAt($x, $pY, $z, $block->getDamage());
                $pY++;
            }
        }
    }

    /**
     * @param int $x
     * @param int $y
     * @param int $z
     *
     * @return bool
     */
    private function canPlantStay(ChunkManager $level, int $x, int $y, int $z): bool {
        $b = $level->getBlockIdAt($x, $y, $z);
        return ($b === Block::AIR or $b === Block::SNOW_LAYER or $b === Block::WATER) and in_array($level->getBlockIdAt($x, $y - 1, $z), array_merge([Block::GRASS], $this->allowedBlocks)) ;
    }
}