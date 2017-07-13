<?php

namespace MultiWorld\Util;

use MultiWorld\MultiWorld;
use pocketmine\utils\Config;

class LanguageManager {

    /** @var  MultiWorld */
    public $plugin;

    /** @string $lang */
    public static $lang;

    // messages from config
    public static $messages;

    public function __construct(MultiWorld $plugin) {
        $this->plugin = $plugin;
    }

    public function loadLang() {
        self::$lang = MultiWorld::getInstance()->getConfig()->get("lang");
        self::$messages = new Config(MultiWorld::getInstance()->getDataFolder()."language/".self::getLang().".yml", Config::YAML);
    }

    public static function getLang() {
        return self::$lang;
    }

    /**
     * @param $message
     * @return string
     */
    public static function translateMessage($message) {
        $messages = self::$messages;
        return strval($messages[$message]);
    }
}