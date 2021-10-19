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

namespace czechpmdevs\multiworld\generator\void;

use pocketmine\block\BlockIds;
use pocketmine\level\ChunkManager;
use pocketmine\level\format\Chunk;
use pocketmine\level\generator\Generator;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;

class VoidGenerator extends Generator {

	/** @var ChunkManager */
	protected $level;
	/** @var Random */
	protected $random;

	/** @phpstan-ignore-next-line */
	public function __construct(array $settings = []) {
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return "void";
	}

	public function init(ChunkManager $level, Random $random): void {
		$this->level = $level;
		$this->random = $random;
	}

	public function generateChunk(int $chunkX, int $chunkZ): void {
		/** @phpstan-var Chunk $chunk */
		$chunk = $this->level->getChunk($chunkX, $chunkZ);

		if($chunkX == 16 && $chunkZ == 16) {
			$chunk->setBlockId(0, 64, 0, BlockIds::GRASS);
		}
	}

	public function getSpawn(): Vector3 {
		return new Vector3(256, 65, 256);
	}

	public function populateChunk(int $chunkX, int $chunkZ): void {
	}

	public function getSettings(): array {
		return [];
	}
}