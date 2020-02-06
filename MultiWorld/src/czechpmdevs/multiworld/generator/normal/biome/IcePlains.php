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

namespace czechpmdevs\multiworld\generator\normal\biome;

use czechpmdevs\multiworld\generator\normal\biome\types\GrassyBiome;
use czechpmdevs\multiworld\generator\normal\biome\types\SnowyBiome;
use czechpmdevs\multiworld\generator\normal\object\Tree;
use czechpmdevs\multiworld\generator\normal\populator\impl\TreePopulator;

/**
 * Class IcePlains
 * @package czechpmdevs\multiworld\generator\normal\biome
 */
class IcePlains extends GrassyBiome implements SnowyBiome {

    /**
     * IcePlains constructor.
     */
    public function __construct() {
        parent::__construct(0, 0.5);
        $this->addPopulator(new TreePopulator(2, 2, 90, Tree::SPRUCE));
        $this->setElevation(100, 106);
    }


    /**
     * @return string
     */
    public function getName(): string {
        return "Ice Plains";
    }
}