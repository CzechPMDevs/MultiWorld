<?php

declare(strict_types=1);

namespace multiworld\util;

use MultiWorld\Task\MultiWorldTask;

/**
 * Class DataTask
 * @package multiworld\util
 */
class DataTask extends MultiWorldTask {

    /** @var  Data $data */
    public $data;

    /**
     * DataTask constructor.
     * @param Data $data
     */
    public function __construct(Data $data) {
        $this->data = $data;
    }

    /**
     * @param int $currentTick
     */
    public function onRun(int $currentTick) {
        $this->getPlugin()->getLogger()->info("§aTask running ({$this->getData()->getLevelName()})");
    }

    /**
     * @return Data
     */
    public function getData():Data {
        return $this->data;
    }
}