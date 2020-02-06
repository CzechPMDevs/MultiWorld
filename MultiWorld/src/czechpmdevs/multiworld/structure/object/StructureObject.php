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

use pocketmine\block\BlockIds;
use pocketmine\math\Vector3;
use pocketmine\nbt\BigEndianNBTStream;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\ListTag;
use pocketmine\nbt\tag\StringTag;

class StructureObject {

    public const SIDE_HELPER = [
        "bottom" => ["east" => 0, "west" => 1, "south" => 2, "north" => 3],
        "top" => ["east" => 4, "west" => 5, "south" => 6, "north" => 7]
    ];

    public const BLOCK_MAP = [
        "minecraft:air" => [0, 0],
        "minecraft:cobblestone" => [4, 0],
        "minecraft:torch" => [50, 5],
        "minecraft:pumpkin" => [86, 1],
        "minecraft:carved_pumpkin" => [86, 1], // wrong id
        "minecraft:dark_oak_slab" => [158, "type" => ["bottom" => 5, "top" => 13]],
        "minecraft:dark_oak_log" => [162, "axis" => ["y" => 1, "x" => 5, "z" => 9]],
        "minecraft:dark_oak_fence" => [85, 5],
        "minecraft:illager_captain_wall_banner" => [177, 4], // 2=>z-;3=>z+;4=>x-;5=>x+
        "minecraft:birch_planks" => [5, 2],
        "minecraft:dark_oak_planks" => [5, 5],
        "minecraft:white_wool" => [35, 0],
        "minecraft:hay_block" => [170, 0],
        "minecraft:cobblestone_stairs" => [67, "sides" => self::SIDE_HELPER],
        "minecraft:mossy_cobblestone_stairs" => [67, "sides" => self::SIDE_HELPER], // wrong id (isn't implemented)
        "minecraft:dark_oak_stairs" => [164, "sides" => self::SIDE_HELPER],
        "minecraft:vine" => [106, "facing" => ["north" => 4, "east" => 8 , "south" => 1, "west" => 2]],
        "minecraft:cobblestone_wall" => [139, 0],
        "minecraft:mossy_cobblestone_wall" => [139, 1],
        "minecraft:cobblestone_slab" => [44, "type" => ["bottom" => 3, "top" => 11]],
        "minecraft:mossy_cobblestone_slab" => [182, "type" => ["bottom" => 5, "top" => 13]],
        "minecraft:mossy_cobblestone" => [48, 0],
        "minecraft:crafting_table" => [58, 0],
        "minecraft:structure_block" => [0, 0] // 252
    ];

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
            $palette[] = $this->getBlockByState($state);
        }

        /** @var CompoundTag $blockData */
        foreach ($compound["blocks"] as $blockData) {
            $pos = $blockData->getListTag("pos");
            $state = $blockData->getInt("state");
            $this->data[] = [$pos->offsetGet(0), $pos->offsetGet(1), $pos->offsetGet(2), $palette[$state][0], $palette[$state][1]];
        }

        if(isset($compound["size"])) {
            /** @var ListTag $list */
            $list = $compound["size"];
            $this->axisVector = new Vector3($list->offsetGet(0), $list->offsetGet(1), $list->offsetGet(2));
        }
    }

    /**
     * @param CompoundTag $state
     *
     * @return array
     */
    private function getBlockByState(CompoundTag $state): array {
        $data = self::BLOCK_MAP[$state->getString("Name")] ?? 0;

        if($data === 0) {
            var_dump($state->getString("Name"));
            return [0, 0];
        }

        $id = $data[0];
        if(isset($data[1])) {
            return [$id, $data[1]];
        }

        if(isset($data["axis"])) {
            return [$id, $data["axis"][$state->getCompoundTag("Properties")->getString("axis")]];
        }

        if(isset($data["type"])) {
            return [$id, $data["type"][$state->getCompoundTag("Properties")->getString("type")]];
        }

        if(isset($data["sides"])) {
            return [$id, $data["sides"][$state->getCompoundTag("Properties")->getString("half")][$state->getCompoundTag("Properties")->getString("facing")]];
        }


        if(isset($data["facing"])) {
            $facing = null;
            $properties = $state->getCompoundTag("Properties");

            if($properties->offsetExists("facing"))
                $facing = $state->getCompoundTag("Properties")->getString("facing");

            else {
                /**
                 * @var string $side
                 * @var StringTag $value
                 */
                foreach ($properties->getValue() as $side => $value) {
                    if($value->getValue() == "true") {
                        $facing = $side;
                        break;
                    }
                }
            }

            return [$id, $data["facing"][$facing]];
        }

        return [0, 0];
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
}