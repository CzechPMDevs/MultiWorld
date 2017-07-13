<?php

namespace MultiWorld\Util;

use MultiWorld\MultiWorld;

class ConfigManager {

    /** @var  MultiWorld */
    public $plugin;

    // prefix
    public static $prefix;

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
        if(!is_file(self::getDataFolder()."/config.yml")) {
            MultiWorld::getInstance()->saveResource("/config.yml");
        }

        // load prefix
        self::$prefix = MultiWorld::getInstance()->getConfig()->get("prefix")." ";
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
