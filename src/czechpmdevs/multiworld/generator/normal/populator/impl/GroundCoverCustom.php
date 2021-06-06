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

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\block\Liquid;
use pocketmine\level\biome\Biome;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\populator\GroundCover;
use pocketmine\level\generator\populator\Populator;
use pocketmine\utils\Random;

class GroundCoverCustom extends Populator{


    public function populate(ChunkManager $level, int $chunkX, int $chunkZ, Random $random){
        $chunk = $level->getChunk($chunkX, $chunkZ);
        for($x = 0; $x < 16; ++$x){
            for($z = 0; $z < 16; ++$z){
                $biome = Biome::getBiome($chunk->getBiomeId($x, $z));
                $cover = $biome->getGroundCover();
                if(count($cover) > 0){
                    $diffY = 0;

                    $column = $chunk->getBlockIdColumn($x, $z);
                    $startY = 127;
                    for(; $startY > 0; --$startY){
                        if($column[$startY] !== "\x00" and !BlockFactory::get(ord($column[$startY]))->isTransparent()){
                            break;
                        }
                    }
                    $startY = min(127, $startY + $diffY);
                    $endY = $startY - count($cover);
                    for($y = $startY; $y > $endY and $y >= 0; --$y){
                        $b = $cover[$startY - $y];


                        if($column[$y] === "\x00" and $b->isSolid()){
                            break;
                        }
                        if(is_array($b)){
                            $selected = $random->nextRange(0, count($b)-1);
                            $randContent = $b[$selected];
                            $data = explode(":",(string)$randContent);
                            $id = $data[0];
                            $meta = $data[1];
                            $block = Block::get((int)$id, (int)$meta);
                            if($block->getDamage() === 0){
                                $chunk->setBlockId($x, $y, $z, $block->getId());
                            }else{
                                $chunk->setBlock($x, $y, $z, $b->getId(), $block->getDamage());
                            }
                        }else{
                            if($b->getDamage() === 0){
                                $chunk->setBlockId($x, $y, $z, $b->getId());
                            }else{
                                $chunk->setBlock($x, $y, $z, $b->getId(), $b->getDamage());
                            }
                        }
                    }
                }
            }
        }
    }

}
