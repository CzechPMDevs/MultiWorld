<?php

namespace MultiWorld\Util;

use MultiWorld\MultiWorld;
use pocketmine\utils\Config;

/**
 * Class ConfigManager
 * @package MultiWorld\Util
 */
class ConfigManager {

    /** @var  MultiWorld */
    public $plugin;

    // prefix
    public static $prefix;

    /**
     * ConfigManager constructor.
     * @param MultiWorld $plugin
     */
    public function __construct(MultiWorld $plugin) {
        $this->plugin = $plugin;
    }

    /**
     * @return void
     */
    public function initConfig() {
        if(!is_dir(self::getDataFolder())) {
            @mkdir(self::getDataFolder());
        }
        if(!is_dir(self::getDataPath()."levels")) {
            @mkdir(self::getDataPath()."levels");
        }
        if(!is_dir(self::getDataFolder()."languages")) {
            @mkdir(self::getDataFolder()."languages");
        }
        if(!is_file(self::getDataFolder()."/config.yml")) {
            MultiWorld::getInstance()->saveResource("/config.yml");
        }
        if(!is_file(self::getDataFolder()."languages/English.yml")) {
            MultiWorld::getInstance()->saveResource("languages/English.yml");
        }

        // load prefix
        self::$prefix = MultiWorld::getInstance()->getConfig()->get("prefix")." §7";
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
     * @return bool|mixed
     */
    public static function getPrefix() {
        return self::$prefix;
    }
}
