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
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\world\ChunkManager;
use pocketmine\utils\Random;

class TallGrassPopulator extends AmountPopulator {

    /** @var bool */
    private bool $allowDoubleGrass = true;

    public function populateObject(ChunkManager $world, int $chunkX, int $chunkZ, Random $random): void {
        $this->getRandomSpawnPosition($world, $chunkX, $chunkZ, $random, $x, $y, $z);

        if ($y !== -1 and $this->canTallGrassStay($world, $x, $y, $z)) {
            $id = ($this->allowDoubleGrass && $random->nextBoundedInt(5) == 4) ? BlockLegacyIds::DOUBLE_PLANT : BlockLegacyIds::TALL_GRASS;
            $world->setBlockAt($x, $y, $z, BlockFactory::getInstance()->get($id));

            if ($id == BlockLegacyIds::DOUBLE_PLANT) {
                $world->setBlockDataAt($x, $y, $z, 2);
                $world->setBlockIdAt($x, $y + 1, $z, $id);
                $world->setBlockDataAt($x, $y + 1, $z, 10);
            }
        }
    }

    private function canTallGrassStay(ChunkManager $world, int $x, int $y, int $z): bool {
        $b = $world->getBlockAt($x, $y, $z)->getId();
        return ($b === BlockLegacyIds::AIR or $b === BlockLegacyIds::SNOW_LAYER) and $world->getBlockAt($x, $y - 1, $z)->getId() === BlockLegacyIds::GRASS;
    }

    public function setDoubleGrassAllowed(bool $allowed = true): void {
        $this->allowDoubleGrass = $allowed;
    }
}
