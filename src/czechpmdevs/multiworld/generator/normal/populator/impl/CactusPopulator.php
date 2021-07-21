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
use pocketmine\block\BlockLegacyIds;
use pocketmine\world\ChunkManager;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;

class CactusPopulator extends AmountPopulator {

    public function populateObject(ChunkManager $world, int $chunkX, int $chunkZ, Random $random): void {
        $this->getRandomSpawnPosition($world, $chunkX, $chunkZ, $random, $x, $y, $z);

        if ($y !== -1 && $this->canCactusStay($world, new Vector3($x, $y, $z))) {
            for ($aY = 0; $aY < $random->nextRange(0, 3); $aY++) {
                $world->setBlockIdAt($x, $y + $aY, $z, BlockLegacyIds::CACTUS);
                $world->setBlockDataAt($x, $y, $z, 1);
            }
        }
    }

    private function canCactusStay(ChunkManager $world, Vector3 $pos): bool {
        /** @phpstan-ignore-next-line */
        $b = $world->getBlockIdAt($pos->getX(), $pos->getY(), $pos->getZ());
        /** @phpstan-ignore-next-line */
        if ($world->getBlockIdAt($pos->getX() + 1, $pos->getY(), $pos->getZ()) != 0 || $world->getBlockIdAt($pos->getX() - 1, $pos->getY(), $pos->getZ()) != 0 || $world->getBlockIdAt($pos->getX(), $pos->getY(), $pos->getZ() + 1) != 0 || $world->getBlockIdAt($pos->getX(), $pos->getY(), $pos->getZ() - 1) != 0) {
            return false;
        }

        /** @phpstan-ignore-next-line */
        return ($b === BlockLegacyIds::AIR) && $world->getBlockIdAt($pos->getX(), $pos->getY() - 1, $pos->getZ()) === BlockLegacyIds::SAND;
    }
}