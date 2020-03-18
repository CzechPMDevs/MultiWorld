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

namespace czechpmdevs\multiworld\structure\object;

use czechpmdevs\multiworld\util\BlockLoader;
use czechpmdevs\multiworld\util\BlockPalette;
use pocketmine\math\Vector3;
use pocketmine\nbt\BigEndianNBTStream;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\utils\Random;

class StructureObject {

    /** @var StructureBlock[][][] $blockMap */
    protected $blockMap = [];

    /** @var array $data */
    public $data;

    /** @var Vector3 $axisVector */
    public $axisVector;

    public function load(string $path) {
        $data = (new BigEndianNBTStream())->readCompressed(file_get_contents($path));

        /** @var CompoundTag $compound */
        $compound = $data->getValue();
        /** @var BlockPalette $palette */
        $palette = new BlockPalette();

        /** @var CompoundTag $state */
        foreach ($compound["palette"] as $state) {
            $palette->registerBlock(BlockLoader::getBlockByState($state));
        }

        /** @var CompoundTag $blockData */
        foreach ($compound["blocks"] as $blockData) {
            $pos = $blockData->getListTag("pos");
            $state = $blockData->getInt("state");

            $x = (int) $pos->offsetGet(0);
            $y = (int) $pos->offsetGet(1);
            $z = (int) $pos->offsetGet(2);

            $this->getBlockAt($x, $y, $z)->addBlock($palette->getBlock($state));
        }

        if(isset($compound["size"])) {
            /** @var ListTag $list */
            $list = $compound["size"];
            $axis = new Vector3($list->offsetGet(0), $list->offsetGet(1), $list->offsetGet(2));

            if(is_null($this->axisVector) || ($axis->getX() + $axis->getY() + $axis->getZ()) > ($this->axisVector->getX() + $this->axisVector->getY() + $this->axisVector->getZ())) {
                $this->axisVector = $axis;
            }
        }
    }

    /**
     * @param int $x
     * @param int $y
     * @param int $z
     */
    public function registerBlock(int $x, int $y, int $z) {
        if(!isset($this->blockMap[$x][$y][$z]))
            $this->blockMap[$x][$y][$z] = new StructureBlock();
    }

    /**
     * @param int $x
     * @param int $y
     * @param int $z
     * @return StructureBlock
     */
    public function getBlockAt(int $x, int $y, int $z): StructureBlock {
        self::registerBlock($x, $y, $z);

        return $this->blockMap[$x][$y][$z];
    }

    /**
     * @param Random $random
     * @return \Generator
     */
    public function getBlocks(Random $random): \Generator {
        foreach ($this->blockMap as $x => $yz) {
            foreach ($yz as $y => $zBlock) {
                foreach ($zBlock as $z => $block) {
                    yield [$x, $y, $z, $block->getBlock($random)];
                }
            }
        }
    }

    /**
     * @return Vector3
     */
    public function getAxisVector(): Vector3 {
        return $this->axisVector;
    }
}