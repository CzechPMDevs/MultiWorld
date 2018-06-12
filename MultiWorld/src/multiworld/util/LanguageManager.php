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

use MultiWorld\MultiWorld;
use pocketmine\utils\Config;

/**
 * Class LanguageManager
 * @package multiworld\Util
 */
class LanguageManager {

    /** @var  MultiWorld */
    public $plugin;

    /** @string $lang */
    public static $lang;

    /** @var  Config */
    public static $messages;

    public function __construct(MultiWorld $plugin) {
        $this->plugin = $plugin;
        $this->loadLang();
    }

    public function loadLang() {
        self::$lang = MultiWorld::getInstance()->getConfig()->get("lang");
        self::$messages = new Config(ConfigManager::getDataFolder()."/languages/".MultiWorld::getInstance()->getConfig()->get("lang").".yml", Config::YAML);
    }

    /**
     * @return string $lang
     */
    public static function getLang() {
        return strval(self::$lang);
    }

    /**
     * @param $message
     * @return string
     */
    public static function translateMessage($message) {
        return strval(self::$messages->get($message));
    }
}