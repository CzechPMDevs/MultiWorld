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
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\utils\TreeType;
use pocketmine\utils\Random;
use pocketmine\world\ChunkManager;
use function in_array;

class TreePopulator extends AmountPopulator {

	private ?TreeType $treeType;

	private bool $vines;

	private bool $high;

	public function __construct(int $baseAmount, int $randomAmount, int $spawnPercentage = 100, ?TreeType $treeType = null, bool $vines = false, bool $high = false) {
		$this->treeType = $treeType;
		$this->vines = $vines;
		$this->high = $high;

		parent::__construct($baseAmount, $randomAmount, $spawnPercentage);
	}

	public function populateObject(ChunkManager $world, int $chunkX, int $chunkZ, Random $random): void {
		$this->getRandomSpawnPosition($world, $chunkX, $chunkZ, $random, $x, $y, $z);
		if ($y === -1) {
			return;
		}

		if (!in_array($world->getBlockAt($x, $y - 1, $z)->getId(), [BlockLegacyIds::GRASS, BlockLegacyIds::MYCELIUM], true)) {
			return;
		}

		Tree::growTree($world, $x, $y, $z, $random, $this->treeType, $this->vines, $this->high);
	}
}
