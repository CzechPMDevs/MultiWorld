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

namespace czechpmdevs\multiworld\util;

use pocketmine\block\BlockLegacyIds;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;

trait PositionCalc {

    /**
     * Returns random spawn position based on Random class for
     * specified Chunk
     */
    public function getRandomSpawnPosition(ChunkManager $world, int $chunkX, int $chunkZ, Random $random, ?int &$x, ?int &$y, ?int &$z): void {
        $x = ($chunkX << 4) + $random->nextBoundedInt(16);
        $z = ($chunkZ << 4) + $random->nextBoundedInt(16);
        $y = $this->getHighestWorkableBlock($world, $x, $z);
    }

    /**
     * Returns Y coordinate of highest block at X:Z coordinates in the level
     */
    public function getHighestWorkableBlock(ChunkManager $world, int $x, int $z): int {
        for ($y = 127; $y >= 0; --$y) {
            $b = $world->getBlockAt($x, $y, $z)->getId();
            if ($b !== BlockLegacyIds::AIR and $b !== BlockLegacyIds::LEAVES and $b !== BlockLegacyIds::LEAVES2 and $b !== BlockLegacyIds::SNOW_LAYER) {
                break;
            }
        }

        return $y === 0 ? -1 : ++$y;
    }
}