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
use pocketmine\inventory\BaseInventory;
use pocketmine\level\ChunkManager;
use pocketmine\utils\Random;
use function Couchbase\basicDecoderV1;

/**
 * Class Village
 * @package czechpmdevs\multiworld\structure
 */
class Village extends Structure {

    private const STREETS_SUBPATH = "streets/";
    private const CENTERS_SUBPATH = "town_centers/";
    private const HOUSES_SUBPATH = "houses/";

    public const DESERT_VILLAGE = "desert";
    public const PLAINS_VILLAGE = "plains";
    public const SAVANNA_VILLAGE = "savanna";
    public const SNOWY_VILLAGE = "snowy";
    public const TAIGA_VILLAGE = "taiga";

    /** @var string $type */
    private $type;

    /** @var array $decorations */
    private $decorations = [];
    /** @var array $houses */
    private $houses = [];
    /** @var array $streets */
    private $streets = [];
    /** @var array $centers */
    private $centers = [];

    public function __construct(string $dir, string $type) {
        $this->type = $type;

        foreach (glob($dir . "*.nbt") as $decPath) {
            $this->addObject($this->decorations[basename($decPath, ".nbt")] = new StructureObject($decPath));
        }
        foreach (glob($dir . self::STREETS_SUBPATH . "*.nbt") as $streetPath) {
            $this->addObject($this->streets[basename($streetPath, ".nbt")] = new StructureObject($streetPath));
        }
        foreach (glob($dir . self::CENTERS_SUBPATH . "*.nbt") as $townCentersPath) {
            $this->addObject($this->centers[basename($townCentersPath, ".nbt")] = new StructureObject($townCentersPath));
        }
        foreach (glob($dir . self::HOUSES_SUBPATH . "*.nbt") as $housesPath) {
            $this->addObject($this->houses[basename($housesPath, ".nbt")] = new StructureObject($housesPath));
        }
    }

    /**
     * @inheritDoc
     */
    public function placeAt(ChunkManager $level, int $x, int $y, int $z, Random $random): void
    {
        // TODO: Implement placeAt() method.
    }
}