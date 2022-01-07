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

use czechpmdevs\multiworld\generator\normal\biome\types\SandyBiome;
use czechpmdevs\multiworld\generator\normal\populator\impl\CactusPopulator;
use czechpmdevs\multiworld\generator\normal\populator\impl\plant\Plant;
use czechpmdevs\multiworld\generator\normal\populator\impl\PlantPopulator;
use pocketmine\block\VanillaBlocks;

class Desert extends SandyBiome {

	public function __construct() {
		parent::__construct(2.0, 0.0);

		$cactus = new CactusPopulator(4, 3);

		$deadBush = new PlantPopulator(4, 2);
		$deadBush->addPlant(new Plant(VanillaBlocks::DEAD_BUSH(), [VanillaBlocks::SAND()]));

		$this->addPopulators([$cactus, $deadBush]);

		$this->setElevation(63, 69);
	}

	public function getName(): string {
		return "Desert";
	}
}