<?php

/**
 * MultiWorld - PocketMine plugin that manages worlds.
 * Copyright (C) 2018 - 2022  CzechPMDevs
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

use czechpmdevs\multiworld\generator\normal\populator\impl\plant\Plant;
use czechpmdevs\multiworld\generator\normal\populator\impl\PlantPopulator;
use pocketmine\block\VanillaBlocks;

class BadlandsPlateau extends Badlands {

	public function __construct() {
		parent::__construct();

		$deadBush = new PlantPopulator(4, 3);
		$deadBush->addPlant(new Plant(VanillaBlocks::DEAD_BUSH(), [VanillaBlocks::HARDENED_CLAY()]));

		$this->clearPopulators();
		$this->addPopulators([$deadBush]);

		$this->setElevation(84, 87);
	}

	public function getName(): string {
		return "Mesa Plateau";
	}
}