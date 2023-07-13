<?php

/**
 * MultiWorld - PocketMine plugin that manages worlds.
 * Copyright (C) 2018 - 2023  CzechPMDevs
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
use function file_get_contents;
use function file_put_contents;
use function is_file;
use function is_string;
use function mkdir;
use function rename;
use function unlink;
use function version_compare;

class ConfigManager {
	private const CONFIG_VERSION = "2.0.0.0";
	private const LANGUAGE_VERSION = "2.1.0.1";

	private const LANGUAGES_AVAILABLE = ["cs_CZ", "de_DE", "en_US", "es_ES", "fr_FR", "id_ID", "ja_JP", "ko_KR", "pt_BR", "ru_RU", "th_TH", "tl_PH", "tr_TR", "vi_VN", "zh_CN"];

	private static string $prefix;

	public function load(): void {
		// Initialize languages folder
		@mkdir(MultiWorld::getInstance()->getDataFolder());
		@mkdir(MultiWorld::getInstance()->getDataFolder() . "languages");

		// Save all the resources, if not preset
		$this->saveConfig();
		$this->saveLanguage();

		// Checking for updates
		$config = MultiWorld::getInstance()->getConfig()->getAll();
		$configVersion = $config["config-version"] ?? null;
		$langVersion = @file_get_contents(MultiWorld::getInstance()->getDataFolder() . "languages/.langver");

		if(!is_string($configVersion) || version_compare($configVersion, self::CONFIG_VERSION) < 0) {
			$this->saveConfig(true);
			MultiWorld::getInstance()->getLogger()->debug("Updating config to plugin-compatible version " . self::CONFIG_VERSION);
		}
		if(!is_string($langVersion) || version_compare($langVersion, self::LANGUAGE_VERSION) < 0) {
			$this->saveLanguage(true);
			MultiWorld::getInstance()->getLogger()->debug("Updating language resources to plugin-compatible version " . self::LANGUAGE_VERSION);
		}

		// Loads prefix
		ConfigManager::$prefix = MultiWorld::getInstance()->getConfig()->get("prefix") . " §a";
	}

	private function saveConfig(bool $replace = false): void {
		if($replace && is_file(MultiWorld::getInstance()->getDataFolder() . "config.yml"))  {
			@unlink(MultiWorld::getInstance()->getDataFolder() . "config.yml.old");
			@rename(MultiWorld::getInstance()->getDataFolder() . "config.yml", MultiWorld::getInstance()->getDataFolder() . "config.old.yml");

			MultiWorld::getInstance()->saveResource("config.yml", true);
			MultiWorld::getInstance()->getConfig()->reload();

			MultiWorld::getInstance()->getLogger()->notice("Config updated. Old config was renamed to 'config.old.yml'. To keep old settings you can copy-paste them into the new config.");
		} else {
			MultiWorld::getInstance()->saveResource("config.yml");
		}
	}

	private function saveLanguage(bool $replace = false): void {
		if($replace || !is_file(MultiWorld::getInstance()->getDataFolder() . "languages/.langver")) {
			file_put_contents(MultiWorld::getInstance()->getDataFolder() . "languages/.langver", self::LANGUAGE_VERSION);
			$replace = true;
		}

		foreach(self::LANGUAGES_AVAILABLE as $lang) {
			MultiWorld::getInstance()->saveResource("languages/$lang.yml", $replace);
		}
	}

	/**
	 * @internal
	 */
	public static function getPrefix(): string {
		return ConfigManager::$prefix ?? "[MultiWorld]";
	}
}
