<?php

/**
 * MultiWorld - PocketMine plugin that manages worlds.
 * Copyright (C) 2018 - 2019  CzechPMDevs
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

use pocketmine\level\generator\populator\Populator;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;

/**
 * Class RandomShapePopulator
 * @package czechpmdevs\multiworld\generator\normal\populator
 */
abstract class RandomShapePopulator extends Populator {

    /** @var int $min */
    public $min;

    /** @var int $max */
    public $max;

    /**
     * RandomShapePopulator constructor.
     * @param int $min
     * @param int $max
     */
    public function __construct(int $min, int $max) {
        $this->min = $min;
        $this->max = $max;
    }

    /**
     * @param Random $random
     * @return \Generator
     */
    public function getRandomShape(Random $random): \Generator {
        for($x = -($this->nextRange($random)); $x < $this->nextRange($random); $x++) {
            $xsqr = $x*$x;
            for($z = -($this->nextRange($random)); $z < $this->nextRange($random); $z++) {
                $zsqr = $z*$z;
                for($y = $random->nextRange(0, 1); $y < $random->nextRange(4, 5); $y++) {
                    if(($xsqr*1.5)+($zsqr*1.5) <= $random->nextRange(12, 22)) {
                        yield new Vector3($x, $y, $z);
                    }
                }
            }
        }
    }

    /**
     * @param Random $random
     * @return int
     */
    public function nextRange(Random $random) {
        return $random->nextRange($this->min, $this->max);
    }

}