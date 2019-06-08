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
use pocketmine\level\generator\populator\Populator;
use pocketmine\utils\Random;

/**
 * Class CactusPopulator
 * @package czechpmdevs\multiworld\generator\normal\populator
 */
class CactusPopulator extends Populator {
    use PopulatorTrait;

    /** @var ChunkManager */
    private $level;

    /** @var int $randomAmount */
    private $randomAmount;

    /** @var int $baseAmount */
    private $baseAmount;

    public function setRandomAmount($amount){
        $this->randomAmount = $amount;
    }

    public function setBaseAmount($amount){
        $this->baseAmount = $amount;
    }

    public function populate(ChunkManager $level, int $chunkX, int $chunkZ, Random $random){
        $this->level = $level;
        $amount = $random->nextRange(0, $this->randomAmount + 1) + $this->baseAmount;
        for($i = 0; $i < $amount; ++$i){
            $x = $random->nextRange($chunkX * 16, $chunkX * 16 + 15);
            $z = $random->nextRange($chunkZ * 16, $chunkZ * 16 + 15);
            $y = $this->getHighestWorkableBlock($level, $x, $z);

            if($y !== -1 and $this->canCactusStay($x, $y, $z)){
                for($aY = 0; $aY < $random->nextRange(0, 3); $aY++) {
                    $this->level->setBlockIdAt($x, $y+$aY, $z, Block::CACTUS);
                    $this->level->setBlockDataAt($x, $y, $z, 1);
                }
            }
        }
    }

    private function canCactusStay(int $x, int $y, int $z) : bool{
        $b = $this->level->getBlockIdAt($x, $y, $z);
        if($this->level->getBlockIdAt($x+1, $y, $z) != 0 || $this->level->getBlockIdAt($x-1, $y, $z) != 0 || $this->level->getBlockIdAt($x, $y, $z+1) != 0 || $this->level->getBlockIdAt($x, $y, $z-1) != 0) {
            return false;
        }
        return ($b === Block::AIR) and $this->level->getBlockIdAt($x, $y - 1, $z) === Block::SAND;
    }
}