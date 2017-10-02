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

    /** @var int $gamemode */
    public $gameMode = 0;

    /** @var int $break */
    public $break = 0;

    /**
     * Data constructor.
     * @param DataManager $dataManager
     * @param string $levelName
     */
    public function __construct(DataManager $dataManager, string $levelName) {
        $this->levelName = $levelName;
        $this->dataManager = $dataManager;
    }
}