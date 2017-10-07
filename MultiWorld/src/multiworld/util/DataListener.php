<?php

declare(strict_types=1);

namespace multiworld\util;

use multiworld\MultiWorld;
use pocketmine\event\Listener;
use pocketmine\Server;

/**
 * Class DataListener
 * @package multiworld\util
 */
class DataListener implements Listener {

    /** @var  Data $data */
    public $data;

    /**
     * DataListener constructor.
     * @param Data $data
     */
    public function __construct(Data $data) {
        $this->data = $data;
        Server::getInstance()->getPluginManager()->registerEvents($this, $this->getPlugin());
    }

    /**
     * @return MultiWorld
     */
    public function getPlugin():MultiWorld {
        return MultiWorld::getInstance();
    }

    /**
     * @return Data
     */
    public function getData():Data {
        return $this->data;
    }
}