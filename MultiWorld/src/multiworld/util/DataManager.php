<?php

namespace multiworld\util;

use multiworld\MultiWorld;
use pocketmine\event\Listener;
use pocketmine\utils\Config;

/**
 * Class DataManager
 * @package multiworld\util
 */
class DataManager implements Listener {

    /** @var  ConfigManager $configManager */
    public $configManager;

    /** @var array $data */
    public $data = [];

    /**
     * DataManager constructor.
     * @param ConfigManager $configManager
     */
    public function __construct(ConfigManager $configManager) {
        $this->getPlugin()->getServer()->getPluginManager()->registerEvents($this, $this->getPlugin());
        $this->configManager = $configManager;
    }

    public function getLevelData($levelName) {

    }

    public function loadData() {
        foreach (glob(ConfigManager::getDataFolder()."worlds/*.yml") as $file) {
            $this->saveDataFromConfig(new Config($file, Config::YAML), new Data($this, basename($file)));
        }
    }

    /**
     * @param Config $config
     * @param Data $data
     */
    public function saveDataFromConfig(Config $config, Data $data) {
        $this->getPlugin()->getLogger()->notice("Loading data for level {$data->levelName}...");
    }

    /**
     * @return ConfigManager $configManager
     */
    public function getConfigManager():ConfigManager {
        return $this->configManager;
    }

    /**
     * @return MultiWorld $multiWorld
     */
    public function getPlugin():MultiWorld {
        return MultiWorld::getInstance();
    }
}