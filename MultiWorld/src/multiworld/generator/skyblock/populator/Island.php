<?php

declare(strict_types=1);

namespace multiworld\generator\skyblock\populator;

use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\object\OakTree;
use pocketmine\level\generator\populator\Populator;
use pocketmine\level\generator\populator\Tree;
use pocketmine\math\Vector3;
use pocketmine\tile\Chest;
use pocketmine\utils\Random;

/**
 * Class Island
 * @package multiworld\generator\skyblock\populator
 */
class Island extends Populator {

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
        $level->setBlockIdAt($chestVec->getX(), $chestVec->getY(), $chestVec->getZ(), Block::CHEST);

        // tree
        $treeVec = $center->add(4, 2, 1);
        $tree = new OakTree;
        $tree->placeObject($level, $treeVec->getX(), $treeVec->getY(), $treeVec->getZ(), $random);

        // bedrock
        $level->setBlockIdAt($center->getX(), $center->getY(), $center->getZ(), Block::BEDROCK);
    }
}