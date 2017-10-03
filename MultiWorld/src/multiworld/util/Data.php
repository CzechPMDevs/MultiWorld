<?php

namespace multiworld\util;

/**
 * Class Data
 * @package multiworld\util
 */
class Data {

    /** @var  string $levelName */
    public $levelName;

    /** @var  DataManager $dataManager */
    public $dataManager;

    /** @var  string $configPath */
    public $configPath;

    /**
     * @var int $allowCommands
     *
     * 0 = ALLOW
     * 1 = DENNY
     */
    public $allowCommands = 0;

    /**
     * @var int $alwaysDay
     *
     * 0 = NO
     * 1 = YES
     */
    public $alwaysDay = 0;

    /**
     * @var int $gameMode
     *
     * 0 = SURVIVAL
     * 1 = CREATIVE
     * 2 = ADVENTURE
     * 3 = SPECTATOR
     */
    public $gameMode = 0;

    /**
     * @var int $allowEditWorld
     *
     * 0 = ALLOW
     * 1 = DENNY
     */
    public $allowEditWorld = 0;

    /**
     * Data constructor.
     * @param DataManager $dataManager
     * @param string $levelName
     * @param string $configPath
     * @param int $allowCommands
     * @param int $alwaysDay
     * @param int $gameMode
     * @param int $allowEditWorld
     */
    public function __construct(DataManager $dataManager, string $levelName, string $configPath,int $allowCommands = 0, int $alwaysDay = 0, int $gameMode = 0,int $allowEditWorld = 0) {
        $this->configPath = $configPath;
        $this->levelName = $levelName;
        $this->dataManager = $dataManager;
        $this->allowCommands = $allowCommands;
        $this->alwaysDay = $alwaysDay;
        $this->allowEditWorld = $allowEditWorld;
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
     * @return bool $allowCommands
     */
    public function getAllowCommands():bool {
        return $this->allowCommands == 0;
    }

    /**
     * @param bool $allow
     */
    public function setAllowCommands(bool $allow) {
        $allow ? $this->allowCommands = 0 : $this->allowCommands = 1;
    }

    /**
     * @return bool $alwaysDay
     */
    public function getAlwaysDay():bool {
        return $this->alwaysDay == 1;
    }

    /**
     * @param bool $bool
     */
    public function setAlwaysDay(bool $bool) {
        $bool ? $this->alwaysDay = 1 : $this->alwaysDay = 0;
    }

    /**
     * @return bool $allowEditWorld
     */
    public function getAllowEditWorld():bool {
        return $this->allowEditWorld == 0;
    }

    /**
     * @param bool $allow
     */
    public function setAllowEditWorld(bool $allow) {
        $allow ? $this->allowEditWorld = 0 : $this->allowEditWorld = 1;
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