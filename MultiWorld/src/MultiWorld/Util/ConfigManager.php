<?php

namespace MultiWorld\Util;

use MultiWorld\MultiWorld;

/**
 * Class ConfigManager
 * @package MultiWorld\Util
 */
class ConfigManager {

    /** @var  MultiWorld */
    public $plugin;

    // prefix
    public static $prefix;

    /** @var  array $configData */
    public $configData;

    /**
     * ConfigManager constructor.
     * @param MultiWorld $plugin
     */
    public function __construct(MultiWorld $plugin) {
        $this->plugin = $plugin;
        $this->initConfig();
        if(is_file(self::getDataFolder()."/config.yml")) {
            $this->loadData();
        }
    }

    function loadData() {
        $config = $this->plugin->getConfig();
        $gmWorlds = $config->get("gamemodeWorlds");
        if(is_array($gmWorlds)) {
            $this->configData["creativeWorlds"] = $gmWorlds["creativeWorlds"];
            $this->configData["survivalWorlds"] = $gmWorlds["survivalWorlds"];
            $this->configData["adventureWorlds"] = $gmWorlds["adventureWorlds"];
            $this->configData["spectatorWorlds"] = $gmWorlds["spectatorWorlds"];
        }
        else {
            $this->plugin->getLogger()->critical("Cloud not load GameMode world data (Data is not saved in array)");
        }
    }

    /**
     * @return void
     */
    function initConfig() {
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
     * @return bool|mixed
     */
    public static function getPrefix() {
        return self::$prefix;
    }
}
