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
use pocketmine\command\CommandSender;
use pocketmine\Player;

/**
 * Class LanguageManager
 * @package czechpmdevs\multiworld\Util
 */
class LanguageManager {

    private const DEFAULT_LANGUAGE = 'eyJub3QtcGVybXMiOiJcdTAwYTdjWW91IGRvIG5vdCBoYXZlIHBlcm1pc3Npb24gdG8gdXNlIHRoaXMgY29tbWFuZCIsImRlZmF1bHQtdXNhZ2UiOiJcdTAwYTdjVXNhZ2U6IFx1MDBhNzdcL213IGhlbHAiLCJoZWxwIjoiXHUwMGE3Mi0tLSBTaG93aW5nIE11bHRpV29ybGQgaGVscCBwYWdlIHslMH0gb2YgeyUxfSAoXC9tdyBoZWxwIDxwYWdlPikgLS0tIiwiaGVscC0xIjoiXHUwMGE3MlwvbXcgY3JlYXRlIFx1MDBhN2ZDcmVhdGUgYSB3b3JsZCIsImhlbHAtMiI6Ilx1MDBhNzJcL213IHRlbGVwb3J0IFx1MDBhN2ZUZWxlcG9ydCB0byBhIHdvcmxkIiwiaGVscC0zIjoiXHUwMGE3MlwvbXcgbGlzdCBcdTAwYTdmIERpc3BsYXlzIGEgbGlzdCBvZiBhbGwgd29ybGRzIiwiaGVscC00IjoiXHUwMGE3MlwvbXcgPGxvYWR8dW5sb2FkPiBcdTAwYTdmTG9hZCBvciB1bmxvYWQgYSB3b3JsZCIsImhlbHAtNSI6Ilx1MDBhNzJcL213IHVwZGF0ZSBcdTAwYTdmVXBkYXRlIGxvYmJ5LCBzcGF3biBpbiBhIHdvcmxkIG9yIGNoYW5nZSB0aGUgZGVmYXVsdCB3b3JsZCIsImhlbHAtNiI6Ilx1MDBhNzJcL213IGRlbGV0ZSBcdTAwYTdmUmVtb3ZlIGEgd29ybGQiLCJoZWxwLTciOiJcdTAwYTcyXC9tdyBpbmZvIFx1MDBhN2ZEaXNwbGF5cyBpbmZvcm1hdGlvbiBhYm91dCBhIHdvcmxkIiwiaGVscC04IjoiXHUwMGE3MlwvbXcgZ2FtZXJ1bGUgXHUwMGE3Zk1hbmFnZSB0aGUgZ2FtZXJ1bGVzIG9mIGEgbGV2ZWwiLCJoZWxwLTkiOiJcdTAwYTcyXC9tdyBtYW5hZ2UgXHUwMGE3ZkRpc3BsYXlzIGZvcm0gZm9yIG1hbmFnaW5nIHdpdGggd29ybGRzIiwiaGVscC0xMCI6Ilx1MDBhNzJcL213IHJlbmFtZSBcdTAwYTdmUmVuYW1lcyB0aGUgd29ybGQiLCJjcmVhdGUtdXNhZ2UiOiJcdTAwYTdjVXNhZ2U6IFx1MDBhNzdcL213IGNyZWF0ZSA8bmFtZT4gW3NlZWRdIFtnZW5lcmF0b3JdIiwiY3JlYXRlLWV4aXN0cyI6Ilx1MDBhN2NMZXZlbCB7JTB9IGlzIGFscmVhZHkgZ2VuZXJhdGVkISIsImNyZWF0ZS1nZW5ub3RleGlzdHMiOiJcdTAwYTdjR2VuZXJhdG9yIHslMH0gbm90IGZvdW5kLiIsImNyZWF0ZS1nZW5lcmF0aW5nIjoiXHUwMGE3YUdlbmVyYXRpbmcgd29ybGQgeyUwfSIsImNyZWF0ZS1kb25lIjoid29ybGRzIHslMH0gd2FzIGdlbmVyYXRlZCB1c2luZyBzZWVkOiB7JTF9IGFuZCBnZW5lcmF0b3I6IHslMn0uIiwidGVsZXBvcnQtdXNhZ2UiOiJcdTAwYTdjVXNhZ2U6IFx1MDBhNzdcL213IHRlbGVwb3J0IDx3b3JsZD4gW3BsYXllcl0iLCJ0ZWxlcG9ydC1sZXZlbG5vdGV4aXN0cyI6Ilx1MDBhN2NXb3JsZCB7JTB9IGhhcyBub3QgYmVlbiBjcmVhdGVkIHlldC4gVHJ5IFwvbXcgY3JlYXRlIHRvIGNyZWF0ZSBhIHdvcmxkLiIsInRlbGVwb3J0LWxvYWQiOiJMb2FkaW5nIHdvcmxkIHslMH0uLi4iLCJ0ZWxlcG9ydC1kb25lLTEiOiJcdTAwYTdhWW91IHdlcmUgdGVsZXBvcnRlZCB0byB7JTB9LiIsInRlbGVwb3J0LWRvbmUtMiI6Ilx1MDBhN2FQbGF5ZXIgeyUxfSB3YXMgdGVsZXBvcnRlZCB0byB7JTB9LiIsInRlbGVwb3J0LXBsYXllcm5vdGV4aXN0cyI6Ilx1MDBhN2NQbGF5ZXIgZG9lcyBub3QgZXhpc3QuIiwibGlzdC1kb25lIjoiXHUwMGE3YVdvcmxkcyAoeyUwfSk6IiwibG9hZC11c2FnZSI6Ilx1MDBhN2NVc2FnZTogXHUwMGE3N1wvbXcgbG9hZCA8d29ybGQ+IiwibG9hZC1ub3RleGlzdHMiOiJcdTAwYTdjV29ybGQgeyUwfSBkb2VzIG5vdCBleGlzdC4iLCJsb2FkLWxvYWRlZCI6Ilx1MDBhN2NVbmFibGUgdG8gbG9hZCB0aGUgd29ybGQuIiwibG9hZC1kb25lIjoiXHUwMGE3YVdvcmxkIGxvYWRlZC4iLCJ1bmxvYWQtdXNhZ2UiOiJcdTAwYTdjVXNhZ2U6IFx1MDBhNzdcL213IHVubG9hZCA8d29ybGQ+IiwidW5sb2FkLWxldmVsbm90ZXhpc3RzIjoiXHUwMGE3Y3slMH0gZG9lcyBub3QgZXhpc3QuIiwidW5sb2FkLXVubG9hZGVkIjoiXHUwMGE3Y1VuYWJsZSB0byB1bmxvYWQgdGhlIHdvcmxkLiIsInVubG9hZC1kb25lIjoiXHUwMGE3YVdvcmxkIHVubG9hZGVkLiIsImRlbGV0ZS11c2FnZSI6Ilx1MDBhN2NVc2FnZTogXHUwMGE3N1wvbXcgZGVsZXRlIDx3b3JsZD4iLCJkZWxldGUtbGV2ZWxub3RleGlzdHMiOiJcdTAwYTdjV29ybGQgZG9lcyBub3QgZXhpc3QuIiwiZGVsZXRlLWRvbmUiOiJcdTAwYTdhV29ybGQgZGVsZXRlZC4gKHslMH0gZmlsZXMgcmVtb3ZlZC4pIiwidXBkYXRlLXVzYWdlIjoiXHUwMGE3Y1VzYWdlOiBcdTAwYTc3XC9tdyB1cGRhdGUgPG1vZDogc3Bhd258bG9iYnl8ZGVmYXVsdD4gW29wdGlvbnM6ICh3b3JsZCkgKHgpICh5KSAoeildIiwidXBkYXRlLWxldmVsbm90ZXhpc3RzIjoiXHUwMGE3Y1dvcmxkIHslMH0gZG9lcyBub3QgZXhpc3QuIiwidXBkYXRlLXNwYXduLWRvbmUiOiJcdTAwYTdhU3Bhd24gaW4gd29ybGQgeyUwfSB3YXMgY2hhbmdlZC4iLCJ1cGRhdGUtbG9iYnktZG9uZSI6Ilx1MDBhN2FMb2JieSBpbiB3b3JsZCB7JTB9IHdhcyBjaGFuZ2VkLiIsInVwZGF0ZS1kZWZhdWx0LXVzYWdlIjoiXHUwMGE3Y1VzYWdlOiBcdTAwYTc3XC9tdyB1cGRhdGUgZGVmYXVsdCA8bGV2ZWw+IiwidXBkYXRlLWRlZmF1bHQtZG9uZSI6Ilx1MDBhN2FEZWZhdWx0IHdvcmxkIHdhcyBjaGFuZ2VkIHRvIHslMH0uIiwidXBkYXRlLW5vdHN1cHBvcnRlZCI6Ilx1MDBhNzRQbGVhc2UgcnVuIHRoaXMgY29tbWFuZCBpbi1nYW1lIiwiaW5mby1sZXZlbG5vdGV4aXN0cyI6Ilx1MDBhN2N7JTB9IGRvZXMgbm90IGV4aXN0LiIsImluZm8iOiJcdTAwYTdhLS0tIHslMH0gLS0tIiwiaW5mby1uYW1lIjoiXHUwMGE3N05hbWU6IHslMH0iLCJpbmZvLWZvbGRlck5hbWUiOiJcdTAwYTc3Rm9sZGVyIG5hbWU6IHslMH0iLCJpbmZvLXBsYXllcnMiOiJcdTAwYTc3UGxheWVyIGNvdW50OiB7JTB9IiwiaW5mby1nZW5lcmF0b3IiOiJcdTAwYTc3R2VuZXJhdG9yOiB7JTB9IiwiaW5mby1zZWVkIjoiXHUwMGE3N1NlZWQ6IHslMH0iLCJpbmZvLXRpbWUiOiJcdTAwYTc3VGltZTogeyUwfSIsImdhbWVydWxlLXVzYWdlIjoiXHUwMGE3Y1VzYWdlOiBcdTAwYTc3XC9tdyBnYW1lcnVsZSA8bGlzdHxnYW1lcnVsZT4gW3RydWV8ZmFsc2VdIFtsZXZlbF0iLCJnYW1lcnVsZS1saXN0IjoiXHUwMGE3YUVkaXRhYmxlIGdhbWVydWxlczogeyUwfSIsImdhbWVydWxlLWxldmVsbm90Zm91bmQiOiJcdTAwYTdjV29ybGQgeyUwfSBub3QgZm91bmQuIiwiZ2FtZXJ1bGUtbm90ZXhpc3RzIjoiXHUwMGE3Y0dhbWVydWxlIHslMH0gZG9lcyBub3QgZXhpc3QuIiwiZ2FtZXJ1bGUtZG9uZSI6Ilx1MDBhN2FHYW1lcnVsZSB7JTB9IGluIHdvcmxkIHslMX0gd2FzIGNoYW5nZWQgdG8geyUyfS4iLCJyZW5hbWUtdXNhZ2UiOiJcdTAwYTdjVXNhZ2U6IFx1MDBhNzdcL213IHJlbmFtZSA8ZnJvbT4gPHRvPiIsInJlbmFtZS1leGlzdHMiOiJcdTAwYTdjTGV2ZWwgd2l0aCBuYW1lIHslMH0gYWxyZWFkeSBleGlzdHMuIiwicmVuYW1lLWxldmVsbm90Zm91bmQiOiJcdTAwYTdjTGV2ZWwgeyUwfSBkb2VzIG5vdCBleGlzdHMuIiwicmVuYW1lLWRvbmUiOiJcdTAwYTdhTmFtZSBvZiBsZXZlbCB7JTB9IHdhcyBjaGFuZ2VkIHRvIHslMX0uIiwiZm9ybXMtaW52YWxpZCI6Ilx1MDBhN2NJbnZhbGlkIHJlc3VsdC4ifQ==';

    /** @var MultiWorld $plugin */
    public $plugin;

    /** @var bool $forceDefaultLang */
    private static $forceDefaultLang = false;

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
        $config = $this->plugin->getConfig()->getAll();

        self::$defaultLang = $config["lang"];
        foreach (glob(ConfigManager::getDataFolder() . "/languages/*.yml") as $langResource) {
            self::$languages[basename($langResource, ".yml")] = yaml_parse_file($langResource);
        }

        if(!isset(self::$languages[self::$defaultLang])) {
            self::$languages[self::$defaultLang] = json_decode(base64_decode(self::DEFAULT_LANGUAGE), true); // it should fix bug
        }

        if(isset($config["forceDefaultLang"])) {
            self::$forceDefaultLang = (bool)$config["forceDefaultLang"];
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
        try {
            $lang = self::$defaultLang;
            if($sender instanceof Player && isset(self::$players[$sender->getName()])) {
                $lang = self::$players[$sender->getName()];
            }

            if(empty(self::$languages[$lang]) || self::$forceDefaultLang) {
                $lang = self::$defaultLang;
            }

            if(empty(self::$languages[$lang])) {
                $lang = "en_US";
            }

            $message = self::$languages[$lang][$msg];

            foreach ($params as $index => $param) {
                $message = str_replace("{%$index}", $param, $message);
            }


        }
        catch (\Exception $exception) {
            MultiWorld::getInstance()->getLogger()->error("LanguageManager error: " . $exception->getMessage() . " Try remove language resources and restart the server.");
            return "";
        }

        return $message;
    }
}