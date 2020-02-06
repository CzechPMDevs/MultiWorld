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
use pocketmine\level\ChunkManager;
use pocketmine\math\Vector3;
use pocketmine\utils\Random;

class BigOakTree extends Tree {

    const LEAF_DENSITY = 1.0;

    /** @var int $maxLeafDistance */
    private $maxLeafDistance = 5;
    /** @var int $trunkHeight */
    private $trunkHeight;
    /** @var int $height */
    private $height;

    /**
     * BigOakTree constructor.
     * @param Random $random
     * @param ChunkManager $level
     */
    public function __construct(Random $random, ChunkManager $level) {
        $this->height = $random->nextBoundedInt(12) + 5;
    }
    /**
     * @param ChunkManager $level
     * @param int $x
     * @param int $y
     * @param int $z
     * @param Random $random
     *
     * @return bool
     */
    public function canPlaceObject(ChunkManager $level, int $x, int $y, int $z, Random $random): bool {
        $from = new Vector3($x, $y, $z);

        $to = new Vector3($x, $y + $this->height - 1, $z);
        $blocks = $this->countAvailableBlocks($from, $to, $level);

        if ($blocks == -1) {
            return true;
        } else if ($blocks > 5) {
            $this->height = $blocks;
            return true;
        }

        return false;
    }

    /**
     * @param ChunkManager $level
     * @param int $blockX
     * @param int $blockY
     * @param int $blockZ
     * @param Random $random
     *
     * @return bool|void
     */
    public function placeObject(ChunkManager $level, int $blockX, int $blockY, int $blockZ, Random $random) {

        $trunkHeight = (int)($this->height * 0.618);
        if ($trunkHeight >= $this->height) {
            $trunkHeight = $this->height - 1;
        }

        $leafNodes = $this->generateLeafNodes($blockX, $blockY, $blockZ, $level, $random);

        // generate the leaves

        /**
         * @var Vector3 $node
         */
        foreach ($leafNodes as [$node, $branchY]) {
            for ($y = 0; $y < $this->maxLeafDistance; $y++) {
                $size = $y > 0 && $y < $this->maxLeafDistance - 1.0 ? 3.0 : 2.0;
                $nodeDistance = (int)(0.618 + $size);
                for ($x = -$nodeDistance; $x <= $nodeDistance; $x++) {
                    for ($z = -$nodeDistance; $z <= $nodeDistance; $z++) {
                        $sizeX = abs($x) + 0.5;
                        $sizeZ = abs($z) + 0.5;
                        if ($sizeX * $sizeX + $sizeZ * $sizeZ <= $size * $size && $node->getY() + $y - 3 >= $blockY) {
                            if($level->getBlockIdAt($node->getX() + $x, $node->getY() + $y, $node->getZ() + $z) == Block::AIR) {
                                $level->setBlockIdAt($node->getX() + $x, $node->getY() + $y, $node->getZ() + $z, Block::LEAVES);
                            }
                        }
                    }
                }
            }
        }

        // generate the trunk
        for ($y = 0; $y < $trunkHeight; $y++) {
            $level->setBlockIdAt($blockX, $blockY+$y, $blockZ, Block::WOOD);
        }

        // generate the branches

        /**
         * @var Vector3 $leafNode
         */
        foreach ($leafNodes as [$leafNode, $branchY]) {
            if ((double)$branchY - $blockY >= $this->height * 0.2) {
                $base = new Vector3($blockX, $branchY, $blockZ);
                $branch = $leafNode->subtract($base);

                $maxDistance = max(abs(floor($branch->getY())), max(abs(floor($branch->getX())), abs(floor($branch->getZ()))));

                $dx = (float)$branch->getX() / $maxDistance;
                $dy = (float)$branch->getY() / $maxDistance;
                $dz = (float)$branch->getZ() / $maxDistance;

                for ($i = 0; $i <= $maxDistance; $i++) {
                    $branch = $base->add(0.5 + $i * $dx, 0.5 + $i * $dy, 0.5 + $i * $dz);
                    $x = abs(floor($branch->getX()) - floor($base->getX()));
                    $z = abs($branch->getZ() - $base->getZ());
                    $max = max($x, $z);
                    $direction = $max > 0 ? $max == $x ? 4 : 8 : 0; // EAST / SOUTH

                    $level->setBlockIdAt($branch->getX(), $branch->getY(), $branch->getZ(), Block::WOOD);
                    $level->setBlockDataAt($branch->getX(), $branch->getY(), $branch->getZ(), $direction);
                }
            }
        }

        return true;
    }

    /**
     * @param Vector3 $from
     * @param Vector3 $to
     * @param ChunkManager $world
     *
     * @return int
     */
    private function countAvailableBlocks(Vector3 $from, Vector3 $to, ChunkManager $world) {
        $n = 0;
        $target = $to->subtract($from);
        $maxDistance = max(abs(floor($target->getY())), max(abs(floor($target->getX())), abs(floor($target->getZ()))));

        if($maxDistance > 0) {
            $dx = (float)$target->getX() / $maxDistance;
            $dy = (float)$target->getY() / $maxDistance;
            $dz = (float)$target->getZ() / $maxDistance;
            for ($i = 0; $i <= $maxDistance; $i++, $n++) {
                $target = $from->add(new Vector3(0.5 + $i * $dx, 0.5 + $i * $dy, 0.5 + $i * $dz));
                if ($target->getFloorY() < 0 || $target->getFloorY() > 255) {
                    return $n;
                }
            }
        }
        return -1;
    }

    /**
     * @param $blockX
     * @param $blockY
     * @param $blockZ
     * @param $world
     * @param Random $random
     *
     * @return array
     */
    private function generateLeafNodes($blockX, $blockY, $blockZ, $world, Random $random) {
        $leafNodes = [];
        $y = $blockY + $this->height - $this->maxLeafDistance;
        $trunkTopY = $blockY + $this->trunkHeight;
        $leafNodes[] = [new Vector3($blockX, $y, $blockZ), $trunkTopY];

        $nodeCount = (int)(1.382 + pow(self::LEAF_DENSITY * (double)$this->height / 13.0, 2.0));
        $nodeCount = $nodeCount < 1 ? 1 : $nodeCount;

        for ($l = --$y - $blockY; $l >= 0; $l--, $y--) {
            $h = $this->height / 2.0;
            $v = $h - $l;
            $f = $l < (float)$this->height * 0.3 ? -1.0 : $v == $h ? $h * 0.5 : $h <= abs($v) ? 0.0 : (sqrt($h * $h - $v * $v) * 0.5);

            if ($f >= 0.0) {
                for ($i = 0; $i < $nodeCount; $i++) {
                    $d1 = $f * ($random->nextFloat() + 0.328);
                    $d2 = $random->nextFloat() * M_PI * 2.0;
                    $x = (int)($d1 * sin($d2) + $blockX + 0.5);
                    $z = (int)($d1 * cos($d2) + $blockZ + 0.5);
                    if ($this->countAvailableBlocks(new Vector3($x, $y, $z), new Vector3($x, $y + $this->maxLeafDistance, $z), $world) == -1) {
                        $offX = $blockX - $x;
                        $offZ = $blockZ - $z;
                        $distance = 0.381 * hypot($offX, $offZ);
                        $branchBaseY = min($trunkTopY, (int)($y - $distance));

                        if ($this->countAvailableBlocks(new Vector3($x, $branchBaseY, $z), new Vector3($x, $y, $z), $world) == -1) {
                            $leafNodes[] = [new Vector3($x, $y, $z), $branchBaseY];
                        }
                    }
                }
            }
        }

        return $leafNodes;
    }
}

