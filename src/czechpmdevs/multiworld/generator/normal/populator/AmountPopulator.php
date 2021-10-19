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

namespace czechpmdevs\multiworld\generator\normal\populator;

use pocketmine\level\ChunkManager;
use pocketmine\utils\Random;

abstract class AmountPopulator extends Populator {

	/** @var int */
	private int $baseAmount;
	/** @var int */
	private int $randomAmount;
	/** @var int */
	private int $spawnPercentage = 100;

	public function __construct(int $baseAmount, int $randomAmount, ?int $spawnPercentage = null) {
		$this->baseAmount = $baseAmount;
		$this->randomAmount = $randomAmount;

		if(!is_null($spawnPercentage)) {
			$this->spawnPercentage = $spawnPercentage;
		}
	}

	public function setBaseAmount(int $baseAmount): void {
		$this->baseAmount = $baseAmount;
	}

	public function setRandomAmount(int $randomAmount): void {
		$this->randomAmount = $randomAmount;
	}

	public function setSpawnPercentage(int $percentage): void {
		$this->spawnPercentage = $percentage;
	}

	public final function populate(ChunkManager $level, int $chunkX, int $chunkZ, Random $random): void {
		if($random->nextRange($this->spawnPercentage, 100) != 100) {
			return;
		}

		$amount = $random->nextBoundedInt($this->randomAmount + 1) + $this->baseAmount;
		for($i = 0; $i < $amount; $i++) {
			$this->populateObject($level, $chunkX, $chunkZ, $random);
		}
	}

	abstract public function populateObject(ChunkManager $level, int $chunkX, int $chunkZ, Random $random): void;
}