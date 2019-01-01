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

namespace czechpmdevs\multiworld\generator\skyblock\populator;

use pocketmine\block\Block;
use pocketmine\event\level\LevelLoadEvent;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\object\OakTree;
use pocketmine\level\generator\populator\Populator;
use pocketmine\level\generator\populator\Tree;
use pocketmine\level\Level;
use pocketmine\math\Vector3;
use pocketmine\tile\Chest;
use pocketmine\tile\Tile;
use pocketmine\utils\Random;

/**
 * Class Island
 * @package czechpmdevs\multiworld\generator\skyblock\populator
 */
class Island extends Populator {

    /**
     * @param ChunkManager|Level $level
     * @param int $chunkX
     * @param int $chunkZ
     * @param Random $random
     *
     * @return mixed|void
     */
    public function populate(ChunkManager $level, int $chunkX, int $chunkZ, Random $random) {
        $center = new Vector3(256, 68, 256);

        for($x = -1; $x <= 1; $x++) {
            for($y = -1; $y <= 1; $y++) {
                for($z = -1; $z <= 1; $z++) {

                    // center
                    $centerVec = $center->add($x, $y, $z);
                    if($centerVec->getY() == 69) {
                        $level->setBlockIdAt($centerVec->getX(), $centerVec->getY(), $centerVec->getZ(), Block::GRASS);
                    }
                    else {
                        $level->setBlockIdAt($centerVec->getX(), $centerVec->getY(), $centerVec->getZ(), Block::DIRT);
                    }

                    // left
                    $leftVec = $center->add(3)->add($x, $y, $z);
                    if($leftVec->getY() == 69) {
                        $level->setBlockIdAt($leftVec->getX(), $leftVec->getY(), $leftVec->getZ(), Block::GRASS);
                    }
                    else {
                        $level->setBlockIdAt($leftVec->getX(), $leftVec->getY(), $leftVec->getZ(), Block::DIRT);
                    }

                    // down
                    $downVec = $center->subtract(0, 0, 3)->add($x, $y, $z);
                    if($leftVec->getY() == 69) {
                        $level->setBlockIdAt($downVec->getX(), $downVec->getY(), $downVec->getZ(), Block::GRASS);
                    }
                    else {
                        $level->setBlockIdAt($downVec->getX(), $downVec->getY(), $downVec->getZ(), Block::DIRT);
                    }
                }
            }
        }

        // chest
        $chestVec = $center->add(0, 2, -4);

        if($level instanceof Level) {
            $level->addTile(Tile::createTile(Tile::CHEST, $level, Tile::createNBT($chestVec)));
            $level->setBlockIdAt($chestVec->getX(), $chestVec->getY(), $chestVec->getZ(), Block::CHEST);
        }

        // tree
        $treeVec = $center->add(4, 2, 1);
        $tree = new OakTree;
        $tree->placeObject($level, $treeVec->getX(), $treeVec->getY(), $treeVec->getZ(), $random);

        // bedrock
        $level->setBlockIdAt($center->getX(), $center->getY(), $center->getZ(), Block::BEDROCK);
    }
}