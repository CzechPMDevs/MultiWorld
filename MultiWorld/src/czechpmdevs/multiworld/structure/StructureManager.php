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

use czechpmdevs\multiworld\api\FileBrowsingApi;
use czechpmdevs\multiworld\MultiWorld;
use czechpmdevs\multiworld\structure\type\Igloo;
use czechpmdevs\multiworld\structure\type\PillagerOutpost;
use czechpmdevs\multiworld\structure\type\Village;

/**
 * Class StructureManager
 * @package czechpmdevs\multiworld\structure
 */
class StructureManager {

    private const VILLAGES_PATH = "structures/village/";
    private const IGLOO_PATH = "structures/igloo/";
    private const PILLEGEROUTPOST_PATH = "structures/pillageroutpost/";

    public const VILLAGE_TYPES = [Village::PLAINS_VILLAGE, Village::DESERT_VILLAGE, Village::DESERT_VILLAGE];

    /** @var Village[] $villages */
    public static $villages = [];
    /** @var PillagerOutpost $pillagerOutpost */
    public static $pillagerOutpost;
    /** @var Igloo $igloo */
    public static $igloo;

    /**
     * @param array $resources
     */
    public static function saveResources(array $resources) {
        $saved = 0;
        $startTime = microtime(true);

        foreach ($resources as $resource) {
            if($resource->getExtension() === "nbt") {
                if(FileBrowsingApi::saveResource($resource->getLinkTarget(), MultiWorld::getInstance()->getDataFolder() . FileBrowsingApi::removePathFromRoot($resource->getLinkTarget(), "resources"))) {
                    $saved++;
                }
            }
        }

        if($saved > 0) {
            MultiWorld::getInstance()->getLogger()->info("Saved $saved structures! (" . (string)round(microtime(true)-$startTime, 2) . " seconds)");
        }
    }

    /**
     * @return string
     */
    private static function getDataFolder(): string {
        return getcwd() . DIRECTORY_SEPARATOR . "plugin_data" . DIRECTORY_SEPARATOR . "MultiWorld" . DIRECTORY_SEPARATOR;
    }

    /**
     * @api
     *
     * @return void
     */
    public static function loadVillages(): void {
        foreach (self::VILLAGE_TYPES as $type) {
            self::$villages[$type] = new Village(self::getDataFolder() . self::VILLAGES_PATH . $type . "/", $type);
        }
    }

    /**
     * @api
     *
     * @return Village[]
     */
    public static function getVillages(): array {
        return self::$villages ?? null;
    }

    /**
     * @api
     *
     * @param string $type
     * @return Village|null
     */
    public static function getVillage(string $type): ?Village {
        return self::$villages[$type] ?? null;
    }

    /**
     * @api
     *
     * @return void
     */
    public static function loadPillagerOutpost(): void {
        self::$pillagerOutpost = new PillagerOutpost(self::getDataFolder() . self::PILLEGEROUTPOST_PATH);
    }

    /**
     * @api
     *
     * @return PillagerOutpost|null
     */
    public static function getPillagerOutpost(): ?PillagerOutpost {
        return self::$pillagerOutpost;
    }

    /**
     * @api
     *
     * @return void
     */
    public static function loadIgloo(): void {
        self::$igloo = new Igloo(self::getDataFolder() . self::IGLOO_PATH);
    }

    /**
     * @api
     *
     * @return Igloo|null
     */
    public static function getIgloo(): ?Igloo {
        return self::$igloo;
    }
}