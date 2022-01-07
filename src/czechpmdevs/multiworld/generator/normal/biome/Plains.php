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

use czechpmdevs\multiworld\generator\normal\biome\types\GrassyBiome;
use czechpmdevs\multiworld\generator\normal\populator\impl\LakePopulator;
use czechpmdevs\multiworld\generator\normal\populator\impl\plant\Plant;
use czechpmdevs\multiworld\generator\normal\populator\impl\PlantPopulator;
use czechpmdevs\multiworld\generator\normal\populator\impl\TallGrassPopulator;
use czechpmdevs\multiworld\generator\normal\populator\impl\TreePopulator;
use pocketmine\block\VanillaBlocks;

class Plains extends GrassyBiome {

	public function __construct() {
		parent::__construct(0.8, 0.4);

		$flowers = new PlantPopulator(9, 7, 85);
		$flowers->addPlant(new Plant(VanillaBlocks::DANDELION()));
		$flowers->addPlant(new Plant(VanillaBlocks::POPPY()));

		$daisy = new PlantPopulator(9, 7, 85);
		$daisy->addPlant(new Plant(VanillaBlocks::OXEYE_DAISY()));

		$bluet = new PlantPopulator(9, 7, 85);
		$bluet->addPlant(new Plant(VanillaBlocks::AZURE_BLUET()));

		$tulips = new PlantPopulator(9, 7, 85);
		$tulips->addPlant(new Plant(VanillaBlocks::PINK_TULIP()));
		$tulips->addPlant(new Plant(VanillaBlocks::ORANGE_TULIP()));

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