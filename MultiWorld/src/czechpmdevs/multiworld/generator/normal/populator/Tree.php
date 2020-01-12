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

use czechpmdevs\multiworld\generator\normal\object\Tree as ObjectTree;
use pocketmine\block\Block;
use pocketmine\block\Sapling;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\populator\Populator;
use pocketmine\utils\Random;

class Tree extends Populator {
    use PopulatorTrait;

	/** @var ChunkManager */
	private $level;
	private $randomAmount;
	private $baseAmount;
	private $spawnPercentage = 100;

	private $type;
	private $vines = false;

	public function __construct($type = Sapling::OAK, bool $vines = false){
		$this->type = $type;
		$this->vines = $vines;
	}

    /**
     * @param $percentage
     */
	public function setSpawnPercentage($percentage) {
	    $this->spawnPercentage = $percentage;
    }

	public function setRandomAmount($amount){
		$this->randomAmount = $amount;
	}

	public function setBaseAmount($amount){
		$this->baseAmount = $amount;
	}

	public function populate(ChunkManager $level, int $chunkX, int $chunkZ, Random $random){
	    if($random->nextRange(100, $this->spawnPercentage) != 100) {
	        return;
        }
		$this->level = $level;
		$amount = $random->nextRange(0, $this->randomAmount + 1) + $this->baseAmount;
		for($i = 0; $i < $amount; ++$i){
			$x = $random->nextRange($chunkX << 4, ($chunkX << 4) + 15);
			$z = $random->nextRange($chunkZ << 4, ($chunkZ << 4) + 15);
			$y = $this->getHighestWorkableBlock($level, $x, $z);
			if($y === -1){
				continue;
			}
			if($level->getBlockIdAt($x, $y-1, $z) !== Block::GRASS) {
			    continue;
            }
			ObjectTree::growTree($this->level, $x, $y, $z, $random, $this->type, $this->vines);
		}
	}
}
