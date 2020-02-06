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

namespace czechpmdevs\multiworld\generator\normal\object;

use pocketmine\block\Block;
use pocketmine\block\BlockIds;
use pocketmine\level\ChunkManager;
use pocketmine\math\Vector2;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;

class DarkOakTree extends Tree {

    public function placeObject(ChunkManager $level, int $x, int $y, int $z, Random $rand) {
        $i = $rand->nextBoundedInt(3) + $rand->nextBoundedInt(2) + 6;

        // j k l

        if ($y >= 1 && $y + $i + 1 < 256) {
            $block = $level->getBlockIdAt($x, $y - 1, $z);

            if ($block!= Block::GRASS && $block != Block::DIRT) {
                return false;
            } else {
                $level->setBlockIdAt($x, $y - 1, $z, Block::DIRT);
                $level->setBlockIdAt($x + 1, $y - 1, $z, Block::DIRT);
                $level->setBlockIdAt($x + 1, $y - 1, $z + 1, Block::DIRT);
                $level->setBlockIdAt($x, $y - 1, $z + 1, Block::DIRT);

                $i1 = $i - $rand->nextBoundedInt(4);
                $j1 = 2 - $rand->nextBoundedInt(3); // x1 -> j1
                $x1 = $x; // k1 -> y1 -> x1
                $z1 = $z;
                $i2 = $y + $i - 1;

                for ($x2 = 0; $x2 < $i; ++$x2) {
                    if ($x2 >= $i1 && $j1 > 0) {
                        //$x1 += enumfacing->getXOffset();
                        //$z1 += enumfacing->getZOffset();
                        --$j1;
                    }

                    $y2 = $y + $x2;
                    $material = $level->getBlockIdAt($x1, $y2, $z1);

                    if ($material == BlockIds::AIR || $material == BlockIds::LEAVES) {
                        $level->setBlockIdAt($x1, $y2, $z1, BlockIds::WOOD2);
                        $level->setBlockDataAt($x1, $y2, $z1, 1);
                        $level->setBlockIdAt($x1 + 1, $y2, $z1, BlockIds::WOOD2);
                        $level->setBlockDataAt($x1 + 1, $y2, $z1, 1);
                        $level->setBlockIdAt($x1 + 1, $y2, $z1 + 1, BlockIds::WOOD2);
                        $level->setBlockDataAt($x1 + 1, $y2, $z1 + 1, 1);
                        $level->setBlockIdAt($x1, $y2, $z1 + 1, BlockIds::WOOD2);
                        $level->setBlockDataAt($x1, $y2, $z1 + 1, 1);
                    }
                }

                for ($i3 = -2; $i3 <= 0; ++$i3) {
                    for ($l3 = -2; $l3 <= 0; ++$l3) {
                        $k4 = -1;
                        $level->setBlockIdAt($x1 + $i3, $i2 + $k4, $z1 + $l3, BlockIds::LEAVES2);
                        $level->setBlockDataAt($x1 + $i3, $i2 + $k4, $z1 + $l3, 1);
                        $level->setBlockIdAt(1 + $x1 - $i3, $i2 + $k4, $z1 + $l3, BlockIds::LEAVES2);
                        $level->setBlockDataAt(1 + $x1 - $i3, $i2 + $k4, $z1 + $l3, 1);
                        $level->setBlockIdAt($x1 + $i3, $i2 + $k4, 1 + $z1 - $l3, BlockIds::LEAVES2);
                        $level->setBlockDataAt($x1 + $i3, $i2 + $k4, 1 + $z1 - $l3, 1);
                        $level->setBlockIdAt(1 + $x1 - $i3, $i2 + $k4, 1 + $z1 - $l3, BlockIds::LEAVES2);
                        $level->setBlockDataAt(1 + $x1 - $i3, $i2 + $k4, 1 + $z1 - $l3, 1);

                        if (($i3 > -2 || $l3 > -1) && ($i3 != -1 || $l3 != -2)) {
                            $k4 = 1;
                            $level->setBlockIdAt($x1 + $i3, $i2 + $k4, $z1 + $l3, BlockIds::LEAVES2);
                            $level->setBlockDataAt($x1 + $i3, $i2 + $k4, $z1 + $l3, 1);

                            $level->setBlockIdAt(1 + $x1 - $i3, $i2 + $k4, $z1 + $l3, BlockIds::LEAVES2);
                            $level->setBlockDataAt(1 + $x1 - $i3, $i2 + $k4, $z1 + $l3, 1);

                            $level->setBlockIdAt($x1 + $i3, $i2 + $k4, 1 + $z1 - $l3, BlockIds::LEAVES2);
                            $level->setBlockDataAt($x1 + $i3, $i2 + $k4, 1 + $z1 - $l3, 1);

                            $level->setBlockIdAt(1 + $x1 - $i3, $i2 + $k4, 1 + $z1 - $l3, BlockIds::LEAVES2);
                            $level->setBlockDataAt(1 + $x1 - $i3, $i2 + $k4, 1 + $z1 - $l3, 1);
                        }
                    }
                }

                if ($rand->nextBoolean()) {
                    $level->setBlockIdAt($x1, $i2 + 2, $z1, BlockIds::LEAVES2);
                    $level->setBlockDataAt($x1, $i2 + 2, $z1, 1);

                    $level->setBlockIdAt($x1 + 1, $i2 + 2, $z1, BlockIds::LEAVES2);
                    $level->setBlockDataAt($x1 + 1, $i2 + 2, $z1, 1);

                    $level->setBlockIdAt($x1, $i2 + 2, $z1 + 1, BlockIds::LEAVES2);
                    $level->setBlockDataAt($x1, $i2 + 2, $z1 + 1, 1);

                    $level->setBlockIdAt($x1 + 1, $i2 + 2, $z1 + 1, BlockIds::LEAVES2);
                    $level->setBlockDataAt($x1 + 1, $i2 + 2, $z1 + 1, 1);
                }

                for ($j3 = -3; $j3 <= 4; ++$j3) {
                    for ($i4 = -3; $i4 <= 4; ++$i4) {
                        if (($j3 != -3 || $i4 != -3) && ($j3 != -3 || $i4 != 4) && ($j3 != 4 || $i4 != -3) && ($j3 != 4 || $i4 != 4) && (abs($j3) < 3 || abs($i4) < 3)) {
                            $level->setBlockIdAt($x1 + $j3, $i2, $z1 + $i4, BlockIds::LEAVES2);
                            $level->setBlockDataAt($x1 + $j3, $i2, $z1 + $i4, 1);
                        }
                    }
                }

                for ($k3 = -1; $k3 <= 2; ++$k3) {
                    for ($j4 = -1; $j4 <= 2; ++$j4) {
                        if (($k3 < 0 || $k3 > 1 || $j4 < 0 || $j4 > 1) && $rand->nextBoundedInt(3) <= 0) {
                            $l4 = $rand->nextBoundedInt(3) + 2;

                            for ($i5 = 0; $i5 < $l4; ++$i5) {
                                $level->setBlockIdAt($x + $k3, $i2 - $i5 - 1, $z + $j4, BlockIds::LEAVES2);
                                $level->setBlockDataAt($x + $k3, $i2 - $i5 - 1, $z + $j4, 1);
                            }

                            for ($j5 = -1; $j5 <= 1; ++$j5) {
                                for ($l2 = -1; $l2 <= 1; ++$l2) {
                                    $level->setBlockIdAt($x1 + $k3 + $j5, $i2, $z1 + $j4 + $l2, BlockIds::LEAVES2);
                                    $level->setBlockDataAt($x1 + $k3 + $j5, $i2, $z1 + $j4 + $l2, 1);
                                }
                            }

                            for ($k5 = -2; $k5 <= 2; ++$k5) {
                                for ($l5 = -2; $l5 <= 2; ++$l5) {
                                    if (abs($k5) != 2 || abs($l5) != 2) {
                                        $level->setBlockIdAt($x1 + $k3 + $k5, $i2 - 1, $z1 + $j4 + $l5, BlockIds::LEAVES2);
                                        $level->setBlockDataAt($x1 + $k3 + $k5, $i2 - 1, $z1 + $j4 + $l5, 1);
                                    }
                                }
                            }
                        }
                    }
                }

                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * @param ChunkManager $level
     * @param int $x
     * @param int $y
     * @param int $z
     * @param Random $random
     * @return bool
     */
    public function canPlaceObject(ChunkManager $level, int $x, int $y, int $z, Random $random): bool {
        return parent::canPlaceObject($level, $x, $y, $z, $random) &&
            parent::canPlaceObject($level, $x + 1, $y, $z, $random) &&
            parent::canPlaceObject($level, $x + 1, $y, $z + 1, $random) &&
            parent::canPlaceObject($level, $x, $y, $z +1, $random);
    }
}