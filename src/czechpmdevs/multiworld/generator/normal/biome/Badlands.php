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

use czechpmdevs\multiworld\generator\normal\biome\types\CoveredBiome;
use czechpmdevs\multiworld\generator\normal\populator\impl\CactusPopulator;
use czechpmdevs\multiworld\generator\normal\populator\impl\plant\Plant;
use czechpmdevs\multiworld\generator\normal\populator\impl\PlantPopulator;
use pocketmine\block\utils\DyeColor;
use pocketmine\block\VanillaBlocks;
use pocketmine\world\generator\object\OreType;
use pocketmine\world\generator\populator\Ore;

class Badlands extends CoveredBiome {

	public function __construct() {
		parent::__construct(2, 0);

		$this->setGroundCover([
			VanillaBlocks::RED_SAND(),
			VanillaBlocks::HARDENED_CLAY(),
			VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::RED()),
			VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::YELLOW()),
			VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::YELLOW()),
			VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::BROWN()),
			VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::WHITE()),
			VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::ORANGE()),
			VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::RED()),
			VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::YELLOW()),
			VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::YELLOW()),
			VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::BROWN()),
			VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::WHITE()),
			VanillaBlocks::STAINED_CLAY()->setColor(DyeColor::ORANGE())
		]);

		$cactus = new CactusPopulator(3, 2);

		$deadBush = new PlantPopulator(3, 2);
		$deadBush->addPlant(new Plant(VanillaBlocks::DEAD_BUSH(), [VanillaBlocks::STAINED_CLAY()]));

		$ore = new Ore();
		$ore->setOreTypes([new OreType(VanillaBlocks::GOLD_ORE(), VanillaBlocks::STONE(), 24, 12, 0, 128)]);

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