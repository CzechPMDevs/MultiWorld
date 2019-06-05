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

namespace czechpmdevs\multiworld\generator\normal\populator;

use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;

/**
 * Class LakePopulator
 * @package czechpmdevs\multiworld\generator\normal\populator
 */
class LakePopulator extends RandomShapePopulator {
    use PopulatorTrait;

    /**
     * LakePopulator constructor.
     */
    public function __construct() {
        parent::__construct(7, 10);
    }

    public function populate(ChunkManager $level, int $chunkX, int $chunkZ, Random $random) {
        if($random->nextRange(0, 4) != 0) {
            return;
        }

        $x = $random->nextRange($chunkX * 16, $chunkX * 16 + 15);
        $z = $random->nextRange($chunkX * 16, $chunkX * 16 + 15);
        $y = $this->getHighestWorkableBlock($level, $x, $z)-4;

        $pos = new Vector3($x, $y, $z);
        $pos->subtract(0, 1);

        /** @var Vector3 $vec */
        foreach ($this->getRandomShape($random) as $vec) {
            $finalPos = $pos->add($vec);
            $id = $finalPos->getY() <= $y+2 ? Block::WATER : Block::AIR;
            $level->setBlockIdAt($finalPos->getX(), $finalPos->getY(), $finalPos->getZ(), $id);
            $level->setBlockDataAt($finalPos->getX(), $finalPos->getY(), $finalPos->getZ(), 0);
        }
    }
}