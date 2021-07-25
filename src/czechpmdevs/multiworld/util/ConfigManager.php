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
use Webmozart\PathUtil\Path;
use function array_key_exists;
use function is_dir;
use function is_file;
use function mkdir;
use function version_compare;
use function yaml_parse_file;

class ConfigManager {
	public const CONFIG_VERSION = "1.6.0.1";
	public static string $prefix;

	public function __construct() {
		// Saves required resources, checks for resource updates
		$this->initConfig($this->checkConfigUpdates());
		// Default GameRules
		GameRules::init((array) yaml_parse_file(Path::join(self::getDataFolder(), "data/gamerules.yml")));
		// Loads prefix
		self::$prefix = MultiWorld::getInstance()->getConfig()->get("prefix") . " §a";
	}

	public function initConfig(bool $forceUpdate = false) : void {
		$folder = self::getDataFolder();
		@mkdir($folder);
		@mkdir(Path::join($folder, "data"));
		@mkdir(Path::join($folder, "languages"));
		$instance = MultiWorld::getInstance();
		$languages = [
			"cs_CZ",
			"de_DE",
			"en_US",
			"ina_IND",
			"ja_JP",
			"ko_KR",
			"pt_BR",
			"ru_RU",
			"tl_PH",
			"tr_TR",
			"vi_VN",
			"zh_CN",
		];
		foreach ($languages as $language) {
			$instance->saveResource("languages/$language.yml", $forceUpdate);
		}
		$instance->saveResource("data/gamerules.yml");
		$instance->saveResource("config.yml");
	}

	public static function getDataFolder() : string {
		return MultiWorld::getInstance()->getDataFolder();
	}

	public function checkConfigUpdates() : bool {
		$configuration = MultiWorld::getInstance()->getConfig()->getAll();
		if (
			!array_key_exists("config-version", $configuration) ||
			version_compare((string) $configuration["config-version"], self::CONFIG_VERSION) < 0
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

	public static function getDataPath() : string {
		return MultiWorld::getInstance()->getServer()->getDataPath();
	}

	public static function getPrefix() : string {
		return self::$prefix ?? "[MultiWorld]";
	}
}
