<?php

/**
 * MultiWorld - PocketMine plugin that manages worlds.
 * Copyright (C) 2018 - 2021  CzechPMDevs
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
use pocketmine\block\VanillaBlocks;
use pocketmine\math\Facing;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use function count;

class PlantPopulator extends AmountPopulator {

    /** @var Plant[] */
    private array $plants = [];
    /** @var Block[] */
    private array $allowedBlocks = [];

    public function addPlant(Plant $plant): void {
        $this->plants[] = $plant;
    }

    public function allowBlockToStayAt(Block $block): void {
        $this->allowedBlocks[] = $block;
    }

    public function populateObject(ChunkManager $world, int $chunkX, int $chunkZ, Random $random): void {
        if (count($this->plants) === 0) {
            return;
        }

        $this->getRandomSpawnPosition($world, $chunkX, $chunkZ, $random, $x, $y, $z);

        if ($y !== -1 and $this->canPlantStay($world, $x, $y, $z)) {
            $plant = $random->nextRange(0, count($this->plants) - 1);
            $pY = $y;
            foreach ($this->plants[$plant]->blocks as $block) {
                $world->setBlockAt($x, $pY, $z, $block);
                $pY++;
            }
        }
    }

    private function canPlantStay(ChunkManager $world, int $x, int $y, int $z): bool {
        $block = $world->getBlockAt($x, $y, $z);
        if(
            $block->isSameType(VanillaBlocks::AIR()) or
            $block->isSameType(VanillaBlocks::SNOW_LAYER()) or
            $block->isSameType(VanillaBlocks::WATER())
        ) {
            $block = $block->getSide(Facing::DOWN);
            if($block->isSameType(VanillaBlocks::GRASS())) {
                return true;
            }

            foreach($this->allowedBlocks as $allowed) {
                if($block->isSameType($allowed)) {
                    return true;
                }
            }
        }

        return false;
    }
}