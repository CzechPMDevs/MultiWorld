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

namespace czechpmdevs\multiworld\util;

use czechpmdevs\multiworld\MultiWorld;

class ConfigManager {

    /** @var string */
    public static string $prefix;
    
    /** @var MultiWorld */
    public MultiWorld $plugin;
    /** @var mixed[] */
    public array $configData;

    /**
     * ConfigManager constructor.
     * @param MultiWorld $plugin
     */
    public function __construct(MultiWorld $plugin) {
        $this->plugin = $plugin;
        $this->checkConfigUpdates();
        $this->initConfig();
    }

    public function checkConfigUpdates(): void {
        if (file_exists(ConfigManager::getDataFolder() . "/config.yml")) {
            $data = @yaml_parse_file(ConfigManager::getDataFolder() . "/config.yml");

            $currentVersion = "1.5";

            if (isset($data["config-version"])) $configVersion = $data["config-version"];
            else $configVersion = "1.4";

            if ($data["config-version"] == "1.5.0") $data["config-version"] = "1.5";

            if ($configVersion !== $currentVersion) {
                $this->plugin->getLogger()->notice("Old config found, updating config...");
                if (in_array($configVersion, ["1.4"])) {
                    @rename(ConfigManager::getDataFolder() . "/config.yml", $old = ConfigManager::getDataFolder() . "/config.{$configVersion}.yml");
                    $this->plugin->saveResource("/config.yml");
                    $this->plugin->getLogger()->notice("Config updated! Old config can be found at $old.");
                } else {
                    @unlink(ConfigManager::getDataFolder() . "/config.yml");
                    $this->plugin->saveResource("/config.yml");
                    $this->plugin->getLogger()->notice("Config updated!");
                }
            }
        }
    }

    public static function getDataFolder(): string {
        return MultiWorld::getInstance()->getDataFolder();
    }

    public function initConfig(): void {
        if (!is_dir(ConfigManager::getDataFolder())) {
            @mkdir(ConfigManager::getDataFolder());
        }
        if (!is_dir(ConfigManager::getDataFolder() . "languages")) {
            @mkdir(ConfigManager::getDataFolder() . "languages");
        }
        if (!is_file(ConfigManager::getDataFolder() . "/config.yml")) {
            MultiWorld::getInstance()->saveResource("/config.yml");
        }
        if (!is_file(ConfigManager::getDataFolder() . "languages/cs_CZ.yml")) {
            MultiWorld::getInstance()->saveResource("languages/cs_CZ.yml");
        }
        if (!is_file(ConfigManager::getDataFolder() . "languages/en_US.yml")) {
            MultiWorld::getInstance()->saveResource("languages/en_US.yml");
        }
        if (!is_file(ConfigManager::getDataFolder() . "languages/de_DE.yml")) {
            MultiWorld::getInstance()->saveResource("languages/de_DE.yml");
        }
        if (!is_file(ConfigManager::getDataFolder() . "languages/ja_JP.yml")) {
            MultiWorld::getInstance()->saveResource("languages/ja_JP.yml");
        }
        if (!is_file(ConfigManager::getDataFolder() . "languages/ru_RU.yml")) {
            MultiWorld::getInstance()->saveResource("languages/ru_RU.yml");
        }
        if (!is_file(ConfigManager::getDataFolder() . "languages/zh_CN.yml")) {
            MultiWorld::getInstance()->saveResource("languages/zh_CN.yml");
        }
        if (!is_file(ConfigManager::getDataFolder() . "languages/ina_IND.yml")) {
            MultiWorld::getInstance()->saveResource("languages/ina_IND.yml");
        }

        // load prefix
        ConfigManager::$prefix = MultiWorld::getInstance()->getConfig()->get("prefix") . " §a";
    }

    public static function getDataPath(): string {
        return MultiWorld::getInstance()->getServer()->getDataPath();
    }
    
    public static function getPrefix(): string {
        return isset(ConfigManager::$prefix) ? ConfigManager::$prefix : "[MultiWorld]";
    }
}
