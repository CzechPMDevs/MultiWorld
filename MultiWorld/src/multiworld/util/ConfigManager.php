<?php

declare(strict_types=1);

namespace multiworld\util;

use multiworld\MultiWorld;

/**
 * Class ConfigManager
 * @package multiworld\Util
 */
class ConfigManager {

    /** @var  MultiWorld */
    public $plugin;

    // prefix
    public static $prefix;

    /** @var  array $configData */
    public $configData;

    /** @var DataManager $dataManager */
    public $dataManager;

    /**
     * ConfigManager constructor.
     * @param MultiWorld $plugin
     */
    public function __construct(MultiWorld $plugin) {
        $this->plugin = $plugin;
        $this->initConfig();
        $this->dataManager = new DataManager($this);
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
        if(!is_dir(self::getDataFolder()."worlds")) {
            @mkdir(self::getDataFolder()."worlds");
        }
        if(!is_file(self::getDataFolder()."/config.yml")) {
            MultiWorld::getInstance()->saveResource("/config.yml");
        }
        if(!is_file(self::getDataFolder()."languages/English.yml")) {
            MultiWorld::getInstance()->saveResource("languages/English.yml");
        }
        if(!is_file(self::getDataFolder()."languages/Czech.yml")) {
            MultiWorld::getInstance()->saveResource("languages/Czech.yml");
        }
        if(!is_file(self::getDataFolder()."languages/Russian.yml")) {
            MultiWorld::getInstance()->saveResource("languages/Russian.yml");
        }
        if(!is_file(self::getDataFolder()."languages/German.yml")) {
            MultiWorld::getInstance()->saveResource("languages/German.yml");
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
     * @return string $prefix
     */
    public static function getPrefix():string {
        return is_string(self::$prefix) ? self::$prefix : "[MultiWorld]";
    }

    public function getDataManager():DataManager {
        return $this->dataManager;
    }
}
