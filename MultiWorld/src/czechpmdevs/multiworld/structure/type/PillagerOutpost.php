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

namespace czechpmdevs\multiworld\structure\type;

use czechpmdevs\multiworld\structure\object\StructureObject;
use czechpmdevs\multiworld\structure\Structure;
use czechpmdevs\multiworld\util\PositionCalc;
use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\level\ChunkManager;
use pocketmine\utils\Random;

/**
 * Class PillagerOutpost
 * @package czechpmdevs\multiworld\structure
 */
class PillagerOutpost extends Structure {
    use PositionCalc;

    private const WATCHTOWER_FILENAME = "watchtower";
    private const WATCHTOWER_OVERGROWN_FILENAME = "watchtower_overgrown";

    /** @var StructureObject[] $features */
    public $features = [];

    /**
     * PillagerOutpost constructor.
     *
     * @param string $dir
     */
    public function __construct(string $dir) {
        foreach (glob($dir . "*.nbt") as $file) {
            if(in_array($name = basename($file, ".nbt"), [self::WATCHTOWER_FILENAME, self::WATCHTOWER_OVERGROWN_FILENAME])) {
                $this->addObject(new StructureObject($file), $name);
            }
            else {
                $this->features[] = new StructureObject($file);
            }
        }
        parent::__construct($dir);
    }

    /**
     * @param ChunkManager $level
     * @param int $x
     * @param int $y
     * @param int $z
     * @param Random $random
     */
    public function placeAt(ChunkManager $level, int $x, int $y, int $z, Random $random): void {
        // main building
        $air = 0;

        for($xx = 0; $xx < 13; $xx++) {
            for($zz = 0; $zz < 13; $zz++) {
                if($level->getBlockIdAt($x+$xx, $y-1, $z + $zz) == BlockIds::AIR) {
                    $air++;
                }
            }
        }

        if($air > 1) {
            $this->placeAt($level, $x, $y-1, $z, $random);
            return;
        }

        for($xx = 0; $xx < 13; $xx++) {
            for($zz = 0; $zz < 13; $zz++) {
                $level->setBlockIdAt($x+$xx, $y-1, $z + $zz, BlockIds::GRASS);
            }
        }

        $targetObject = $random->nextBoolean() ? self::WATCHTOWER_FILENAME : self::WATCHTOWER_OVERGROWN_FILENAME;

        foreach ($this->getObject($targetObject)->getBlocks() as [$xx, $yy, $zz, $id, $data]) {
            $level->setBlockIdAt($x + $xx, $y + $yy, $z + $zz, $id);
            $level->setBlockDataAt($x + $xx, $y + $yy, $z + $zz, $data);
        }

        // other features

        // move coords to mid
        $x += 6;
        $z += 6;

        $featuresCount = $random->nextBoundedInt(3) + 1;
        $usedFeatures = [];

        for($i = 0; $i < $featuresCount; $i++) {

            choosingTheFeature:
            $featureType = $this->features[$random->nextBoundedInt(count($this->features))];
            if(in_array($featureType->path, $usedFeatures)) {
                goto choosingTheFeature;
            }

            $usedFeatures[] = $featureType->path;

            $xx = 10 + $random->nextBoundedInt(8);
            $zz = 10 + $random->nextBoundedInt(8);

            if($random->nextBoolean()) {
                $xx = -5 + ($xx * -1);
            }
            if($random->nextBoolean()) {
                $zz = -5 + ($zz * -1);
            }

            $x += $xx;
            $z += $zz;
            $y = 50;
            for(; $y < 100; $y++) {
                if($level->getBlockIdAt($x, $y, $z) === BlockIds::AIR) {
                    break;
                }
            }

            $break = false;
            for($xx = 0; $xx < 7 && !$break; $xx++) {
                for($zz = 0; $zz < 7 && !$break; $zz++) {
                    for($yy = 0; $yy < 6 && !$break; $yy++) {
                        if(!in_array($level->getBlockIdAt($xx, $yy, $zz), [BlockIds::AIR, BlockIds::GRASS, BlockIds::DIRT, BlockIds::DOUBLE_PLANT, BlockIds::TALL_GRASS])) {
                            $break = true;
                            break;
                        }
                    }
                }
            }

            if($break) {
                break;
            }


            foreach ($featureType->getBlocks() as [$xx, $yy, $zz, $id, $data]) {
                $level->setBlockIdAt($x + $xx, $y + $yy, $z + $zz, $id);
                $level->setBlockDataAt($x + $xx, $y + $yy, $z + $zz, $data);
            }
        }
    }
}