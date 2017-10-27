<?php

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