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

namespace czechpmdevs\multiworld\util;

use MultiWorld\MultiWorld;
use pocketmine\command\CommandSender;
use pocketmine\Player;

/**
 * Class LanguageManager
 * @package czechpmdevs\multiworld\Util
 */
class LanguageManager {

    /** @var MultiWorld $plugin */
    public $plugin;

    /** @var string $defaultLang */
    public static $defaultLang;

    /** @var array $languages */
    public static $languages = [];

    /** @var array $players */
    public static $players = [];

    public function __construct(MultiWorld $plugin) {
        $this->plugin = $plugin;
        $this->loadLang();
    }

    public function loadLang() {
        self::$defaultLang = $this->plugin->getConfig()->get("lang");
        foreach (glob(ConfigManager::getDataFolder() . "/languages/*.yml") as $langResource) {
            self::$languages[basename($langResource, ".yml")] = yaml_parse_file($langResource);
        }
    }

    /**
     * @param CommandSender $sender
     * @param string $msg
     * @param array $params
     *
     * @return string $message
     */
    public static function getMsg(CommandSender $sender, string $msg, array $params = []): string {
        $lang = self::$defaultLang;
        if($sender instanceof Player && isset(self::$players[$sender->getName()])) {
            $lang = self::$players[$sender->getName()];
        }

        if(empty(self::$languages[$lang])) {
            $lang = self::$defaultLang;
        }

        $message = self::$languages[$lang][$msg];

        foreach ($params as $index => $param) {
            $message = str_replace("{%$index}", $param, $message);
        }

        return $message;
    }
}