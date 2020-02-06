<?php

/**
 * MultiWorld - PocketMine plugin that manages worlds.
 * Copyright (C) 2018 - 2020  CzechPMDevs
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

/**
 * Class AmountPopulator
 * @package czechpmdevs\multiworld\generator\normal\populator
 */
abstract class AmountPopulator extends Populator {

    /** @var int $baseAmount */
    private $baseAmount = 1;
    /** @var int $randomAmount */
    private $randomAmount = 0;
    /** @var int $spawnPercentage */
    private $spawnPercentage = 100;

    /**
     * AmountPopulator constructor.
     *
     * @param int $baseAmount
     * @param int $randomAmount
     * @param int|null $spawnPercentage
     */
    public function __construct(int $baseAmount, int $randomAmount, ?int $spawnPercentage = null) {
        $this->baseAmount = $baseAmount;
        $this->randomAmount = $randomAmount;

        if(!is_null($spawnPercentage)) {
            $this->spawnPercentage = $spawnPercentage;
        }
    }

    /**
     * @param int $baseAmount
     */
    public function setBaseAmount(int $baseAmount): void {
        $this->baseAmount = $baseAmount;
    }

    /**
     * @param int $randomAmount
     */
    public function setRandomAmount(int $randomAmount): void {
        $this->randomAmount = $randomAmount;
    }

    /**
     * @param int $percentage
     */
    public function setSpawnPercentage(int $percentage): void {
        $this->spawnPercentage = $percentage;
    }

    /**
     * @param ChunkManager $level
     * @param int $chunkX
     * @param int $chunkZ
     * @param Random $random
     *
     * @return void
     */
    public final function populate(ChunkManager $level, int $chunkX, int $chunkZ, Random $random): void {
        if($random->nextRange($this->spawnPercentage, 100) != 100) {
            return;
        }

        $amount = $random->nextBoundedInt($this->randomAmount + 1) + $this->baseAmount;
        for($i = 0; $i < $amount; $i++) {
            $this->populateObject($level, $chunkX, $chunkZ, $random);
        }
    }

    /**
     * @param ChunkManager $level
     * @param int $chunkX
     * @param int $chunkZ
     * @param Random $random
     *
     * @return void
     */
    abstract public function populateObject(ChunkManager $level, int $chunkX, int $chunkZ, Random $random): void;
}