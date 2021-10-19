<?php

/**
 * MultiWorld - PocketMine plugin that manages worlds.
 * Copyright (C) 2018 - 2021  CzechPMDevs
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

use czechpmdevs\multiworld\level\gamerules\GameRules;
use czechpmdevs\multiworld\MultiWorld;
use function array_key_exists;
use function is_dir;
use function is_file;
use function mkdir;
use function version_compare;
use function yaml_parse_file;

class ConfigManager {

	public const CONFIG_VERSION = "1.6.0.1";

	/** @var string */
	public static string $prefix;

	public function __construct() {
		// Saves required resources, checks for resource updates
		$this->initConfig($this->checkConfigUpdates());

		// Default GameRules
		GameRules::init((array)yaml_parse_file(ConfigManager::getDataFolder() . "data/gamerules.yml"));
		// Loads prefix
		ConfigManager::$prefix = MultiWorld::getInstance()->getConfig()->get("prefix") . " §a";
	}

	public function initConfig(bool $forceUpdate = false): void {
		if(!is_dir(ConfigManager::getDataFolder())) {
			@mkdir(ConfigManager::getDataFolder());
		}
		if(!is_dir(ConfigManager::getDataFolder() . "data")) {
			@mkdir(ConfigManager::getDataFolder() . "data");
		}
		if(!is_file(ConfigManager::getDataFolder() . "data/gamerules.yml")) {
			MultiWorld::getInstance()->saveResource("data/gamerules.yml");
		}
		if(!is_dir(ConfigManager::getDataFolder() . "languages")) {
			@mkdir(ConfigManager::getDataFolder() . "languages");
		}
		if(!is_file(ConfigManager::getDataFolder() . "languages/cs_CZ.yml") || $forceUpdate) {
			MultiWorld::getInstance()->saveResource("languages/cs_CZ.yml", $forceUpdate);
		}
		if(!is_file(ConfigManager::getDataFolder() . "languages/de_DE.yml") || $forceUpdate) {
			MultiWorld::getInstance()->saveResource("languages/de_DE.yml", $forceUpdate);
		}
		if(!is_file(ConfigManager::getDataFolder() . "languages/en_US.yml") || $forceUpdate) {
			MultiWorld::getInstance()->saveResource("languages/en_US.yml", $forceUpdate);
		}
		if(!is_file(ConfigManager::getDataFolder() . "languages/es_ES.yml") || $forceUpdate) {
			MultiWorld::getInstance()->saveResource("languages/es_ES.yml", $forceUpdate);
		}
		if(!is_file(ConfigManager::getDataFolder() . "languages/ina_IND.yml") || $forceUpdate) {
			MultiWorld::getInstance()->saveResource("languages/ina_IND.yml", $forceUpdate);
		}
		if(!is_file(ConfigManager::getDataFolder() . "languages/ja_JP.yml") || $forceUpdate) {
			MultiWorld::getInstance()->saveResource("languages/ja_JP.yml", $forceUpdate);
		}
		if(!is_file(ConfigManager::getDataFolder() . "languages/ko_KR.yml") || $forceUpdate) {
			MultiWorld::getInstance()->saveResource("languages/ko_KR.yml", $forceUpdate);
		}
		if(!is_file(ConfigManager::getDataFolder() . "languages/pt_BR.yml") || $forceUpdate) {
			MultiWorld::getInstance()->saveResource("languages/pt_BR.yml", $forceUpdate);
		}
		if(!is_file(ConfigManager::getDataFolder() . "languages/ru_RU.yml") || $forceUpdate) {
			MultiWorld::getInstance()->saveResource("languages/ru_RU.yml", $forceUpdate);
		}
		if(!is_file(ConfigManager::getDataFolder() . "languages/tl_PH.yml") || $forceUpdate) {
			MultiWorld::getInstance()->saveResource("languages/tl_PH.yml", $forceUpdate);
		}
		if(!is_file(ConfigManager::getDataFolder() . "languages/tr_TR.yml") || $forceUpdate) {
			MultiWorld::getInstance()->saveResource("languages/tr_TR.yml", $forceUpdate);
		}
		if(!is_file(ConfigManager::getDataFolder() . "languages/vi_VN.yml") || $forceUpdate) {
			MultiWorld::getInstance()->saveResource("languages/vi_VN.yml", $forceUpdate);
		}
		if(!is_file(ConfigManager::getDataFolder() . "languages/zh_CN.yml") || $forceUpdate) {
			MultiWorld::getInstance()->saveResource("languages/zh_CN.yml", $forceUpdate);
		}
		if(!is_file(ConfigManager::getDataFolder() . "/config.yml")) {
			MultiWorld::getInstance()->saveResource("/config.yml");
		}
	}

	public static function getDataFolder(): string {
		return MultiWorld::getInstance()->getDataFolder();
	}

	public function checkConfigUpdates(): bool {
		$configuration = MultiWorld::getInstance()->getConfig()->getAll();
		if(
			!array_key_exists("config-version", $configuration) ||
			version_compare((string)$configuration["config-version"], ConfigManager::CONFIG_VERSION) < 0
		) {
			// Update is required
			@unlink($this->getDataFolder() . "config.yml.old");
			@rename($this->getDataFolder() . "config.yml", $this->getDataFolder() . "config.yml.old");

			MultiWorld::getInstance()->saveResource("config.yml", true);
			MultiWorld::getInstance()->getConfig()->reload();

			MultiWorld::getInstance()->getLogger()->notice("Config and resources updated. Old config was renamed to 'config.yml.old'.");
			return true;
		}

		return false;
	}

	public static function getDataPath(): string {
		return MultiWorld::getInstance()->getServer()->getDataPath();
	}

	public static function getPrefix(): string {
		return ConfigManager::$prefix ?? "[MultiWorld]";
	}
}
