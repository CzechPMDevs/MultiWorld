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

namespace czechpmdevs\multiworld\generator\normal\biome\types;

use pocketmine\block\Sand;
use pocketmine\block\Sandstone;

abstract class SandyBiome extends Biome {

	public function __construct(float $temperature, float $rainfall) {
		$this->setGroundCover([
			new Sand(),
			new Sand(),
			new Sand(),
			new Sand(),
			new Sandstone(),
			new Sandstone(),
			new Sandstone(),
			new Sandstone()
		]);

		parent::__construct($temperature, $rainfall);
	}
}