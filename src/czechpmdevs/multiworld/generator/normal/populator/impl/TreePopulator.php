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

use czechpmdevs\multiworld\generator\normal\object\Tree;
use czechpmdevs\multiworld\generator\normal\populator\AmountPopulator;
use pocketmine\block\BlockIds;
use pocketmine\level\ChunkManager;
use pocketmine\utils\Random;

class TreePopulator extends AmountPopulator {

	/** @var int */
	private int $type;
	/** @var bool */
	private bool $vines;

	public function __construct(int $baseAmount, int $randomAmount, int $spawnPercentage = 100, int $type = Tree::OAK, bool $vines = false) {
		$this->type = $type;
		$this->vines = $vines;

		parent::__construct($baseAmount, $randomAmount, $spawnPercentage);
	}

	public function populateObject(ChunkManager $level, int $chunkX, int $chunkZ, Random $random): void {
		$this->getRandomSpawnPosition($level, $chunkX, $chunkZ, $random, $x, $y, $z);
		if($y === -1) {
			return;
		}

		if(!in_array($level->getBlockIdAt($x, $y - 1, $z), [BlockIds::GRASS, BlockIds::MYCELIUM])) {
			return;
		}

		Tree::growTree($level, $x, $y, $z, $random, $this->type, $this->vines);
	}
}
