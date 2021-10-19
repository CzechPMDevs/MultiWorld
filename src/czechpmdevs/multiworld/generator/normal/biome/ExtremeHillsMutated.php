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

use pocketmine\block\Block;

class ExtremeHillsMutated extends ExtremeHills {

	public function __construct() {
		parent::__construct();

		$this->setElevation(75, 120);

		$this->setGroundCover([
			Block::get(Block::GRAVEL),
			Block::get(Block::GRAVEL),
			Block::get(Block::GRAVEL),
			Block::get(Block::GRAVEL)
		]);
	}

	public function getName(): string {
		return "Extreme Hills Mutated";
	}
}