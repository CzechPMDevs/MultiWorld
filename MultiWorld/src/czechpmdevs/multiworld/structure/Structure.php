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

namespace czechpmdevs\multiworld\structure;

use czechpmdevs\multiworld\structure\object\StructureObject;
use pocketmine\level\ChunkManager;
use pocketmine\utils\Random;

/**
 * Class Structure
 * @package czechpmdevs\multiworld\structure
 */
abstract class Structure {

    /** @var string $dir */
    private $dir;
    /** @var array $modules */
    private $objects = [];

    /**
     * Structure constructor.
     * @param string $dir
     */
    public function __construct(string $dir) {
        $this->dir = $dir;
    }

    /**
     * @param ChunkManager $level
     * @param int $x
     * @param int $y
     * @param int $z
     * @param Random $random
     *
     * @return void
     */
    abstract public function placeAt(ChunkManager $level, int $x, int $y, int $z, Random $random): void;

    /**
     * @return \Generator
     */
    public function getTargetFiles(): \Generator {
        foreach (glob($this->dir . "/*.nbt") as $file) {
            yield $file;
        }
    }

    /**
     * @return string
     */
    public function getDirectory(): string {
        return $this->dir;
    }

    /**
     * @return StructureObject[]
     */
    public function getObjects(): array {
        return $this->objects;
    }

    /**
     * @param StructureObject $object
     * @param string|null $name
     */
    public function addObject(StructureObject $object, string $name = null) {
        if(is_string($name))
            $this->objects[$name] = $object;
        else
            $this->objects[] = $object;
    }

    /**
     * @param string $name
     * @return StructureObject|null
     */
    public function getObject(string $name): ?StructureObject {
        return $this->objects[$name] ?? null;
    }
}