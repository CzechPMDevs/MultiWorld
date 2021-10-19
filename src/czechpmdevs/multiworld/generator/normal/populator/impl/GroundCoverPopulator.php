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

use czechpmdevs\multiworld\generator\normal\BiomeFactory;
use pocketmine\block\BlockFactory;
use pocketmine\block\Liquid;
use pocketmine\level\ChunkManager;
use pocketmine\level\format\Chunk;
use pocketmine\level\generator\populator\Populator;
use pocketmine\utils\Random;
use function count;
use function min;
use function ord;

/**
 * Copy of PocketMine's ground cover populator, only with different biome source.
 * @link https://github.com/pmmp/PocketMine-MP/blob/stable/src/pocketmine/level/generator/populator/GroundCover.php
 */
class GroundCoverPopulator extends Populator {

	public function populate(ChunkManager $level, int $chunkX, int $chunkZ, Random $random) {
		/** @var Chunk $chunk */
		$chunk = $level->getChunk($chunkX, $chunkZ);
		for($x = 0; $x < 16; ++$x) {
			for($z = 0; $z < 16; ++$z) {
				$biome = BiomeFactory::getInstance()->getBiome($chunk->getBiomeId($x, $z));
				$cover = $biome->getGroundCover();
				if(count($cover) > 0) {
					$diffY = 0;
					if(!$cover[0]->isSolid()) {
						$diffY = 1;
					}

					$column = $chunk->getBlockIdColumn($x, $z);
					$startY = 127;
					for(; $startY > 0; --$startY) {
						if($column[$startY] !== "\x00" and !BlockFactory::get(ord($column[$startY]))->isTransparent()) {
							break;
						}
					}
					$startY = min(127, $startY + $diffY);
					$endY = $startY - count($cover);
					for($y = $startY; $y > $endY and $y >= 0; --$y) {
						$b = $cover[$startY - $y];
						if($column[$y] === "\x00" and $b->isSolid()) {
							break;
						}
						if($b->canBeFlowedInto() and BlockFactory::get(ord($column[$y])) instanceof Liquid) {
							continue;
						}
						if($b->getDamage() === 0) {
							$chunk->setBlockId($x, $y, $z, $b->getId());
						} else {
							$chunk->setBlock($x, $y, $z, $b->getId(), $b->getDamage());
						}
					}
				}
			}
		}
	}
}
