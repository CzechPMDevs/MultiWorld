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

namespace czechpmdevs\multiworld\generator\skyblock;

use czechpmdevs\multiworld\generator\skyblock\populator\Island;
use pocketmine\world\ChunkManager;
use pocketmine\world\generator\Generator;

class SkyBlockGenerator extends Generator {

	public function __construct(int $seed, string $preset) {
		parent::__construct($seed, $preset);
	}

	public function generateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void {
	}

	public function populateChunk(ChunkManager $world, int $chunkX, int $chunkZ): void {
		if($chunkX == 16 && $chunkZ == 16) {
			$island = new Island();
			$island->populate($world, $chunkX, $chunkZ, $this->random);
		}
	}
}
