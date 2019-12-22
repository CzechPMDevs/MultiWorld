<?php

/**
 * MultiWorld - PocketMine plugin that manages worlds.
 * Copyright (C) 2018 - 2019  CzechPMDevs
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

namespace czechpmdevs\multiworld\util;

use czechpmdevs\multiworld\MultiWorld;

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
        $this->checkConfigUpdates();
        $this->initConfig();
    }

    public function checkConfigUpdates() {
        if(file_exists(self::getDataFolder() . "/config.yml")) {
            $data = @yaml_parse_file(self::getDataFolder() . "/config.yml");

            $currentVersion = "1.5";

            if(isset($data["config-version"])) $configVersion = $data["config-version"];
            else $configVersion = "1.4";

            if($data["config-version"] == "1.5.0") $data["config-version"] = "1.5";

            if($configVersion !== $currentVersion) {
                $this->plugin->getLogger()->notice("Old config found, updating config...");
                if(in_array($configVersion, ["1.4"])) {
                    @rename(self::getDataFolder() . "/config.yml", $old = self::getDataFolder() . "/config.{$configVersion}.yml");
                    $this->plugin->saveResource("/config.yml");
                    $this->plugin->getLogger()->notice("Config updated! Old config can be found at $old.");
                }
                else {
                    @unlink(self::getDataFolder() . "/config.yml");
                    $this->plugin->saveResource("/config.yml");
                    $this->plugin->getLogger()->notice("Config updated!");
                }
            }
        }
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
        if(!is_file(self::getDataFolder()."languages/cs_CZ.yml")) {
          MultiWorld::getInstance()->saveResource("languages/cs_CZ.yml");
        }
        if(!is_file(self::getDataFolder()."languages/en_US.yml")) {
            MultiWorld::getInstance()->saveResource("languages/en_US.yml");
        }
        if(!is_file(self::getDataFolder()."languages/de_DE.yml")) {
          MultiWorld::getInstance()->saveResource("languages/de_DE.yml");
        }
        if(!is_file(self::getDataFolder()."languages/ja_JP.yml")) {
            MultiWorld::getInstance()->saveResource("languages/ja_JP.yml");
        }
        if(!is_file(self::getDataFolder()."languages/ru_RU.yml")) {
            MultiWorld::getInstance()->saveResource("languages/ru_RU.yml");
        }
        if(!is_file(self::getDataFolder()."languages/zh_CN.yml")) {
            MultiWorld::getInstance()->saveResource("languages/zh_CN.yml");
        }
        if(!is_file(self::getDataFolder()."languages/ina_IND.yml")) {
            MultiWorld::getInstance()->saveResource("languages/ina_IND.yml");
        }

        // load prefix
        self::$prefix = MultiWorld::getInstance()->getConfig()->get("prefix")." §a";
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
