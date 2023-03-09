<?php

/**
 * MultiWorld - PocketMine plugin that manages worlds.
 * Copyright (C) 2018 - 2022  CzechPMDevs
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

use czechpmdevs\multiworld\generator\normal\BiomeFactory;
use pocketmine\block\BlockTypeIds;
use pocketmine\block\Liquid;
use pocketmine\block\RuntimeBlockStateRegistry;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use pocketmine\world\format\Chunk;
use pocketmine\world\generator\populator\Populator;
use pocketmine\world\World;
use function count;
use function min;

/**
 * Copy of PocketMine's ground cover populator, only with different biome source.
 * @link https://github.com/pmmp/PocketMine-MP/blob/master/src/world/generator/populator/GroundCover.php
 */
class GroundCoverPopulator implements Populator {

	public function populate(ChunkManager $world, int $chunkX, int $chunkZ, Random $random): void {
		/** @var Chunk $chunk */
		$chunk = $world->getChunk($chunkX, $chunkZ);
		$factory = RuntimeBlockStateRegistry::getInstance();
		$biomeRegistry = BiomeFactory::getInstance();
		for($x = 0; $x < 16; ++$x) {
			for($z = 0; $z < 16; ++$z) {
                for($y = World::Y_MIN; $y < World::Y_MAX; $y++) {
                    $biome = $biomeRegistry->getBiome($chunk->getBiomeId($x, $y, $z));
                    $cover = $biome->getGroundCover();
                    if (count($cover) > 0) {
                        $diffY = 0;
                        if (!$cover[0]->isSolid()) {
                            $diffY = 1;
                        }

                        $startY = 127;
                        for (; $startY > 0; --$startY) {
                            if (!$factory->fromStateId($chunk->getBlockStateId($x, $startY, $z))->isTransparent()) {
                                break;
                            }
                        }
                        $startY = min(127, $startY + $diffY);
                        $endY = $startY - count($cover);
                        for ($y = $startY; $y > $endY and $y >= 0; --$y) {
                            $b = $cover[$startY - $y];
                            $id = $factory->fromStateId($chunk->getBlockStateId($x, $y, $z));
                            if ($id->getTypeId() === BlockTypeIds::AIR and $b->isSolid()) {
                                break;
                            }
                            if ($b->canBeFlowedInto() and $id instanceof Liquid) {
                                continue;
                            }

                            $chunk->setBlockStateId($x, $y, $z, $b->getStateId());
                        }
                    }
                }
			}
		}
	}
}
