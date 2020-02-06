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

use pocketmine\math\Vector3;
use pocketmine\nbt\BigEndianNBTStream;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use const pocketmine\RESOURCE_PATH;

class StructureObject {

    /** @var array $blockMap */
    private static $blockMap;

    /** @var string $path */
    public $path;

    /** @var array $data */
    public $data;

    /** @var Vector3 $axisVector */
    public $axisVector;

    public function __construct(string $path) {
        $this->path = $path;
        $this->load();
    }

    private function load() {
        $data = (new BigEndianNBTStream())->readCompressed(file_get_contents($this->path));
        /** @var CompoundTag $compound */
        $compound = $data->getValue();

        /**
         * @var int[] $palette
         *
         * stateId => pmmpId
         */
        $palette = [];

        /** @var CompoundTag $state */
        foreach ($compound["palette"] as $state) {
            $palette[] = $this->getBlockMap()[$state->getString("Name")] ?? 0;
        }

        /** @var CompoundTag $blockData */
        foreach ($compound["blocks"] as $blockData) {
            $pos = $blockData->getListTag("pos");
            $state = $blockData->getInt("state");
            $this->data[] = [$pos->offsetGet(0), $pos->offsetGet(1), $pos->offsetGet(2), $palette[$state]];
        }

        if(isset($compound["size"])) {
            /** @var ListTag $list */
            $list = $compound["size"];
            $this->axisVector = new Vector3($list->offsetGet(0), $list->offsetGet(1), $list->offsetGet(2));
        }
    }

    /**
     * @return array
     */
    public function getBlocks(): array {
        return $this->data;
    }

    /**
     * @return Vector3
     */
    public function getAxisVector(): Vector3 {
        return $this->axisVector;
    }

    /**
     * @return array
     */
    private function getBlockMap(): array {
        return is_array(self::$blockMap) ? self::$blockMap : self::$blockMap = json_decode(file_get_contents(RESOURCE_PATH . "vanilla/block_id_map.json"), true);
    }
}