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

namespace czechpmdevs\multiworld\generator\normal\biome;

use czechpmdevs\multiworld\generator\nether\populator\Ore;
use czechpmdevs\multiworld\generator\normal\biome\types\CoveredBiome;
use czechpmdevs\multiworld\generator\normal\populator\impl\CactusPopulator;
use czechpmdevs\multiworld\generator\normal\populator\impl\PlantPopulator;
use pocketmine\block\BlockIds;
use pocketmine\block\GoldOre;
use pocketmine\block\HardenedClay;
use pocketmine\block\Sand;
use pocketmine\block\StainedClay;
use pocketmine\level\generator\object\OreType;

class Badlands extends CoveredBiome {

	public function __construct() {
		parent::__construct(2, 0);

		$this->setGroundCover([
			new Sand(1),
			new HardenedClay(),
			new StainedClay(7),
			new StainedClay(0),
			new StainedClay(14),
			new HardenedClay(),
			new StainedClay(4),
			new StainedClay(4),
			new HardenedClay(),
			new HardenedClay(),
			new StainedClay(1),
			new StainedClay(1),
			new HardenedClay(),
			new StainedClay(7),
			new StainedClay(8),
			new StainedClay(4),
			new HardenedClay()
		]);

		$cactus = new CactusPopulator(3, 2);

		$deadBush = new PlantPopulator(3, 2);
		$deadBush->allowBlockToStayAt(BlockIds::SAND);

		$ore = new Ore();
		$ore->setOreTypes([new OreType(new GoldOre(), 20, 12, 0, 128)]);

		$this->addPopulators([
			$cactus,
			$deadBush,
			$ore
		]);

		$this->setElevation(63, 67);
	}

	public function getName(): string {
		return "Mesa";
	}
}