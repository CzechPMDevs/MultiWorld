<?php

/**
 * MultiWorld - PocketMine plugin that manages worlds.
 * Copyright (C) 2018  CzechPMDevs
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

namespace multiworld\util;

use multiworld\MultiWorld;

/**
 * Class ConfigManager
 * @package multiworld\Util
 */
class ConfigManager {

    /** @var  MultiWorld */
    public $plugin;

    // prefix
    public static $prefix;

    /** @var  array $configData */
    public $configData;

    /**
     * ConfigManager constructor.
     * @param MultiWorld $plugin
     */
    public function __construct(MultiWorld $plugin) {
        $this->plugin = $plugin;
        $this->initConfig();
    }

    /**
     * @return void
     */
    public function initConfig() {
        if(!is_dir(self::getDataFolder())) {
            @mkdir(self::getDataFolder());
        }
        if(!is_dir(self::getDataFolder()."languages")) {
            @mkdir(self::getDataFolder()."languages");
        }
        if(!is_file(self::getDataFolder()."/config.yml")) {
            MultiWorld::getInstance()->saveResource("/config.yml");
        }
        if(!is_file(self::getDataFolder()."languages/Czech.yml")) {
          MultiWorld::getInstance()->saveResource("languages/Czech.yml");
        }
        if(!is_file(self::getDataFolder()."languages/English.yml")) {
            MultiWorld::getInstance()->saveResource("languages/English.yml");
        }
        if(!is_file(self::getDataFolder()."languages/German.yml")) {
          MultiWorld::getInstance()->saveResource("languages/German.yml");
        }
        if(!is_file(self::getDataFolder()."languages/Japanese.yml")) {
            MultiWorld::getInstance()->saveResource("languages/Japanese.yml");
        }
        if(!is_file(self::getDataFolder()."languages/Russian.yml")) {
            MultiWorld::getInstance()->saveResource("languages/Russian.yml");
        }

        // load prefix
        self::$prefix = MultiWorld::getInstance()->getConfig()->get("prefix")." §7";
    }

    /**
     * @return string
     */
    public static function getDataFolder() {
        return MultiWorld::getInstance()->getDataFolder();
    }

    /**
     * @return string
     */
    public static function getDataPath() {
        return MultiWorld::getInstance()->getServer()->getDataPath();
    }

    /**
     * @return string $prefix
     */
    public static function getPrefix():string {
        return is_string(self::$prefix) ? self::$prefix : "[MultiWorld]";
    }
}
