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

use czechpmdevs\multiworld\generator\normal\biome\types\GrassyBiome;
use czechpmdevs\multiworld\generator\normal\populator\impl\PlantPopulator;
use czechpmdevs\multiworld\generator\normal\populator\impl\TallGrassPopulator;
use czechpmdevs\multiworld\generator\normal\populator\impl\TreePopulator;
use czechpmdevs\multiworld\generator\normal\populator\object\Plant;
use pocketmine\block\BrownMushroom;
use pocketmine\block\Dandelion;
use pocketmine\block\Flower;
use pocketmine\block\RedMushroom;
use pocketmine\block\Sapling;

class Swampland extends GrassyBiome {

	public function __construct() {
		parent::__construct(0.8, 0.5);

		$mushrooms = new PlantPopulator(4, 2, 95);
		$mushrooms->addPlant(new Plant(new BrownMushroom()));
		$mushrooms->addPlant(new Plant(new RedMushroom()));

		$flowers = new PlantPopulator(6, 7, 80);
		$flowers->addPlant(new Plant(new Dandelion()));
		$flowers->addPlant(new Plant(new Flower()));

		$oak = new TreePopulator(2, 2, 100, Sapling::OAK, true);
		$tallGrass = new TallGrassPopulator(56, 12);

		$this->addPopulators([$mushrooms, $flowers, $oak, $tallGrass]);

		$this->setElevation(60, 65);
	}

	public function getName(): string {
		return "Swampland";
	}
}