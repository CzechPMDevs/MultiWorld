<?php

namespace MultiWorld\Events;

use MultiWorld\MultiWorld;
use pocketmine\event\Listener;

class EventListener implements Listener  {

    /** @var MultiWorld */
    public $plugin;

    public function __construct($plugin) {
        $this->plugin = $plugin;
    }
}
