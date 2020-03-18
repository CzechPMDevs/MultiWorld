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
use czechpmdevs\multiworld\structure\type\PillagerOutpost;

/**
 * Class StructureManager
 * @package czechpmdevs\multiworld\structure
 */
class StructureManager {

    public const PILLEGEROUTPOST_PATH = "structures/pillageroutpost/";

    /** @var string[] $classPaths */
    protected static $classPaths = [];
    /** @var Structure[] $structures */
    protected static $structures = [];

    /**
     * @param array $resources
     */
    public static function saveResources(array $resources) {
        $saved = 0;
        $startTime = microtime(true);

        /** @var \SplFileInfo $resource */
        foreach ($resources as $resource) {
            if($resource->getExtension() === "nbt") {
                if(MultiWorld::getInstance()->saveResource(FileBrowsingApi::removePathFromRoot($resource->getPathname(), "resources"))) {
                    $saved++;
                }
            }
        }

        if($saved > 0) {
            MultiWorld::getInstance()->getLogger()->info("Saved $saved structures! (" . (string)round(microtime(true)-$startTime, 2) . " seconds)");
        }
    }

    public static function lazyInit() {
        if(count(self::$classPaths) === 0) {
            self::init();
        }
    }

    public static function init() {
        $dataFolder = getcwd() . DIRECTORY_SEPARATOR . "plugin_data" . DIRECTORY_SEPARATOR . "MultiWorld" . DIRECTORY_SEPARATOR;

        self::$classPaths[PillagerOutpost::class] = $dataFolder . self::PILLEGEROUTPOST_PATH;
    }

    /**
     * @api
     *
     * @param string $path
     * @param Structure $structure
     */
    public static function registerStructure(string $path, Structure $structure) {
        if(isset(self::$structures[$path])) {
            return;
        }

        self::$structures[$path] = $structure;
    }

    /**
     * @api
     *
     * @param string $class
     * @return Structure|null
     */
    public static function getStructure(string $class): ?Structure {
        self::lazyInit();
        $path = self::$classPaths[$class];

        if(!isset(self::$structures[$path])) {
            self::registerStructure($path, new $class($path));
        }

        return self::$structures[self::$classPaths[$class]] ?? null;
    }
}