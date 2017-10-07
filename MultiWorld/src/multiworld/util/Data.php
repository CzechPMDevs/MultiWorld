<?php

declare(strict_types=1);

namespace multiworld\util;

/**
 * Class Data
 * @package multiworld\util
 */
class Data {

    /** @var  DataTask $task */
    public $task;

    /** @var  DataListener $listener */
    public $listener;

    /** @var  string $levelName */
    public $levelName;

    /** @var  DataManager $dataManager */
    public $dataManager;

    /** @var  string $configPath */
    public $configPath;

    /**
     * @var int $gameMode
     */
    public $gameMode = 0;

    /**
     * Data constructor.
     * @param DataManager $dataManager
     * @param string $levelName
     * @param string $configPath
     * @param int $gameMode
     */
    public function __construct(DataManager $dataManager, string $levelName, string $configPath, int $gameMode = 0) {
        $this->configPath = $configPath;
        $this->levelName = $levelName;
        $this->dataManager = $dataManager;
        $this->getDataManager()->getPlugin()->getServer()->getScheduler()->scheduleRepeatingTask($this->task = new DataTask($this), 20);
        $this->getDataManager()->getPlugin()->getServer()->getPluginManager()->registerEvents($this->listener = new DataListener($this), $this->getDataManager()->getPlugin());
    }

    /**
     * @return int $gameMode
     */
    public function getGameMode():int {
        return $this->gameMode;
    }

    /**
     * @param int $gameMode
     */
    public function setGameMode(int $gameMode) {
        $this->gameMode = $gameMode;
    }

    /**
     * @return string $levelName
     */
    public function getLevelName():string {
        return $this->levelName;
    }

    /**
     * @return DataManager $dataManager
     */
    public function getDataManager():DataManager {
        return $this->dataManager;
    }
}