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
use czechpmdevs\multiworld\generator\normal\populator\impl\LakePopulator;
use czechpmdevs\multiworld\generator\normal\populator\impl\PlantPopulator;
use czechpmdevs\multiworld\generator\normal\populator\impl\TallGrassPopulator;
use czechpmdevs\multiworld\generator\normal\populator\impl\TreePopulator;
use czechpmdevs\multiworld\generator\normal\populator\object\Plant;
use pocketmine\block\Dandelion;
use pocketmine\block\Flower;

class Plains extends GrassyBiome {

	public function __construct() {
		parent::__construct(0.8, 0.4);

		$flowers = new PlantPopulator(9, 7, 85);
		$flowers->addPlant(new Plant(new Dandelion()));
		$flowers->addPlant(new Plant(new Flower()));

		$daisy = new PlantPopulator(9, 7, 85);
		$daisy->addPlant(new Plant(new Flower(8)));

		$bluet = new PlantPopulator(9, 7, 85);
		$bluet->addPlant(new Plant(new Flower(3)));

		$tulips = new PlantPopulator(9, 7, 85);
		$tulips->addPlant(new Plant(new Flower(4)));
		$tulips->addPlant(new Plant(new Flower(5)));

		$tree = new TreePopulator(2, 1, 85);
		$lake = new LakePopulator();
		$tallGrass = new TallGrassPopulator(89, 26);

		$this->addPopulators([$lake, $flowers, $daisy, $bluet, $tulips, $tree, $tallGrass]);

		$this->setElevation(62, 66);
	}

	public function getName(): string {
		return "Plains";
	}
}