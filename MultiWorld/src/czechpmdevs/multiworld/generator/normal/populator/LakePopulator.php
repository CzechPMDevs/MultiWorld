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

namespace czechpmdevs\multiworld\generator\normal\populator;

use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\populator\Populator;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;

/**
 * Class LakePopulator
 * @package vixikhd\customgen\populator
 */
class LakePopulator extends Populator {
    use PopulatorTrait;

    /** @var Random $random */
    private $random;

    /** @var ChunkManager $level */
    private $level;

    public function populate(ChunkManager $level, int $chunkX, int $chunkZ, Random $random) {
        if($random->nextRange(0, 16) != 0) {
            return;
        }
        $this->random = $random;
        $this->level = $level;
        $x = $random->nextRange($chunkX << 4, ($chunkX << 4) + 15);
        $z = $random->nextRange($chunkZ << 4, ($chunkZ << 4) + 15);
        $y = $this->getHighestWorkableBlock($level, $x, $z)-4;

        $pos = new Vector3($x, $y, $z);

        /** @var Vector3 $vec */
        foreach ($this->getRandomShape() as $vec) {
            $finalPos = $pos->add($vec);
            $id = $finalPos->getY() <= $y+2 ? Block::WATER : Block::AIR;
            $this->level->setBlockIdAt($finalPos->getX(), $finalPos->getY(), $finalPos->getZ(), $id);
            $this->level->setBlockDataAt($finalPos->getX(), $finalPos->getY(), $finalPos->getZ(), 0);
        }

    }

    /**
     * @return \Generator
     */
    private function getRandomShape(): \Generator {
        for($x = -($this->random->nextRange(12, 20)); $x < $this->random->nextRange(12, 20); $x++) {
            $xsqr = $x*$x;
            for($z = -($this->random->nextRange(12, 20)); $z < $this->random->nextRange(12, 20); $z++) {
                $zsqr = $z*$z;
                for($y = $this->random->nextRange(0, 1); $y < $this->random->nextRange(4, 5); $y++) {
                    if(($xsqr*1.5)+($zsqr*1.5) <= $this->random->nextRange(12, 22)) {
                        yield new Vector3($x, $y, $z);
                    }
                }
            }
        }
    }
}