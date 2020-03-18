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

use czechpmdevs\multiworld\structure\object\StructureFeature;
use czechpmdevs\multiworld\structure\object\StructureObject;
use czechpmdevs\multiworld\structure\Structure;
use czechpmdevs\multiworld\util\PositionCalc;
use czechpmdevs\multiworld\util\SimpleBlockData;
use pocketmine\block\BlockIds;
use pocketmine\level\ChunkManager;
use pocketmine\utils\Random;

/**
 * Class PillagerOutpost
 * @package czechpmdevs\multiworld\structure
 */
class PillagerOutpost extends Structure {
    use PositionCalc;

    public const STRUCTURE_NAME = "pillager_outpost";

    private const WATCHTOWER_FILENAME = "watchtower";
    private const WATCHTOWER_OVERGROWN_FILENAME = "watchtower_overgrown";

    /** @var StructureFeature[] $features */
    public $features = [];

    /**
     * PillagerOutpost constructor.
     *
     * @param string $dir
     */
    public function __construct(string $dir) {
        parent::__construct($dir);

        $object = new StructureObject;

        foreach ($this->getTargetFiles() as $file) {
            if(in_array(basename($file, ".nbt"), [self::WATCHTOWER_FILENAME, self::WATCHTOWER_OVERGROWN_FILENAME])) {
                $object->load($file);
            }
            else {
                $this->features[] = new StructureFeature($file);
            }
        }

        $this->addObject($object, self::STRUCTURE_NAME);
    }

    /**
     * @param ChunkManager $level
     * @param int $x
     * @param int $y
     * @param int $z
     * @param Random $random
     */
    public function placeAt(ChunkManager $level, int $x, int $y, int $z, Random $random): void {
        $this->placeMainBuilding($level, $x, $y, $z, $random);
        $this->placeFeatures($level, $x, $y, $z, $random);

        // other features

        // move coords to mid
    }

    private function placeMainBuilding(ChunkManager $level, int $x, int $y, int $z, Random $random) {
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

        /**
         * @var int $xx
         * @var int $yy
         * @var int $zz
         *
         * @var SimpleBlockData $block
         */
        foreach ($this->getObject(self::STRUCTURE_NAME)->getBlocks($random) as [$xx, $yy, $zz, $block]) {
            $level->setBlockIdAt($x + $xx, $y + $yy, $z + $zz, $block->getId());
            $level->setBlockDataAt($x + $xx, $y + $yy, $z + $zz, $block->getMeta());
        }
    }

    /**
     * @param ChunkManager $level
     * @param int $x
     * @param int $y
     * @param int $z
     * @param Random $random
     */
    private function placeFeatures(ChunkManager $level, int $x, int $y, int $z, Random $random) {
        $x += 7;
        $z += 7;

        $featuresCount = $random->nextBoundedInt(3) + 1;
        $usedFeatures = [];

        for($i = 0; $i < $featuresCount; $i++) {

            choosingTheFeature:
            $featureType = $this->features[$random->nextBoundedInt(count($this->features))];
            if(in_array($featureType->path, $usedFeatures)) {
                goto choosingTheFeature;
            }

            $usedFeatures[] = $featureType->path;

            $xx = 13 + $random->nextBoundedInt(8);
            $zz = 13 + $random->nextBoundedInt(8);

            if($random->nextBoolean()) {
                $xx = -$xx;
            }
            if($random->nextBoolean()) {
                $zz = -$zz;
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
            for($xx = 0; $xx < 9 && !$break; $xx++) {
                for($zz = 0; $zz < 9 && !$break; $zz++) {
                    for($yy = 0; $yy < 8 && !$break; $yy++) {
                        if(!in_array($level->getBlockIdAt($xx + $x, $yy + $y, $zz + $z), [BlockIds::AIR, BlockIds::GRASS, BlockIds::DIRT, BlockIds::DOUBLE_PLANT, BlockIds::TALL_GRASS])) {
                            $break = true;
                            break;
                        }
                    }
                }
            }

            if($break) {
                break;
            }


            /**
             * @var int $xx
             * @var int $yy
             * @var int $zz
             *
             * @var SimpleBlockData $block
             */
            foreach ($featureType->getBlocks($random) as [$xx, $yy, $zz, $block]) {
                $level->setBlockIdAt($x + $xx, $y + $yy, $z + $zz, $block->getId());
                $level->setBlockDataAt($x + $xx, $y + $yy, $z + $zz, $block->getMeta());
            }
        }
    }
}